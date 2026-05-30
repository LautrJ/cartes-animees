<?php

namespace App\Console\Commands;

use App\Enums\SettingType;
use App\Enums\UserRole;
use App\Models\CommissionRateHistory;
use App\Models\Setting;
use App\Models\User;
use App\Services\StripeTestDataService;
use Database\Seeders\TestDataSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class AppInit extends Command
{
    protected $signature = 'app:init {--seed : Injecter des données de test}';

    protected $description = 'Initialise l\'application : reset BDD, setup Stripe, création admin';

    public function __construct(protected StripeTestDataService $stripeTestData)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! $this->confirm('⚠️  Cette commande va effacer toutes les données existantes. Continuer ?', false)) {
            $this->info('Annulé.');

            return self::SUCCESS;
        }

        // ----------------------------------------------------------------
        // 1. Reset BDD
        // ----------------------------------------------------------------
        $this->info('🗑️  Reset de la base de données...');
        Artisan::call('migrate:fresh', [], $this->output);
        $this->info('✅ Base de données réinitialisée.');

        // ----------------------------------------------------------------
        // 2. Reset Stripe sandbox
        // ----------------------------------------------------------------
        $this->info('🔄 Nettoyage Stripe sandbox...');
        $this->stripeTestData->archiveAllTestData();
        $this->info('✅ Stripe nettoyé.');

        // ----------------------------------------------------------------
        // 3. Initialisation Stripe (produit + prix)
        // ----------------------------------------------------------------
        $this->info('🚀 Création du produit Stripe...');
        $productId = $this->stripeTestData->initProduct();
        $this->info("✅ Produit créé : {$productId}");

        $subscriptionPrice = (float) $this->ask('Prix mensuel de l\'abonnement (€)', '7.99');

        // Reload config après écriture du .env
        Artisan::call('config:clear');

        // ----------------------------------------------------------------
        // 4. Création du compte admin
        // ----------------------------------------------------------------
        $this->info('👤 Création du compte administrateur...');
        $adminEmail = 'admin@cartes-animees.test';
        $adminPassword = $this->secret('Mot de passe administrateur');

        if (! $adminPassword) {
            $this->error('Le mot de passe est obligatoire.');

            return self::FAILURE;
        }

        $admin = User::create([
            'role' => UserRole::Admin,
            'first_name' => 'Admin',
            'last_name' => 'Cartes Animées',
            'email' => $adminEmail,
            'password' => Hash::make($adminPassword),
            'is_active' => true,
        ]);

        $this->info("✅ Admin créé : {$adminEmail}");

        // ----------------------------------------------------------------
        // 5. Settings de base
        // ----------------------------------------------------------------
        $this->info('⚙️  Initialisation des paramètres...');

        $commissionRate = (float) $this->ask('Taux de commission des orthophonistes (€/patient/mois)', '2.00');

        Setting::insert([
            [
                'key' => 'commission_rate',
                'value' => (string) $commissionRate,
                'type' => SettingType::Float->value,
                'label' => 'Taux de commission',
                'description' => 'Montant en euros versé à l\'orthophoniste par patient actif par mois.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'subscription_price',
                'value' => (string) $subscriptionPrice,
                'type' => SettingType::Float->value,
                'label' => 'Prix de l\'abonnement',
                'description' => 'Prix mensuel de l\'abonnement en euros.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Première ligne CommissionRateHistory
        CommissionRateHistory::create([
            'rate' => $commissionRate,
            'effective_from' => now(),
            'created_by' => $admin->id,
        ]);

        $this->info('✅ Paramètres initialisés.');

        // ----------------------------------------------------------------
        // 6. Création du prix Stripe + SubscriptionPriceHistory
        // ----------------------------------------------------------------
        $priceId = $this->stripeTestData->initPrice($productId, $subscriptionPrice, $admin);
        $this->info("✅ Prix Stripe créé : {$priceId}");

        // ----------------------------------------------------------------
        // 7. Données de test (optionnel)
        // ----------------------------------------------------------------
        if ($this->option('seed')) {
            $this->info('🌱 Injection des données de test...');
            app(TestDataSeeder::class)->run($admin, $priceId);
            $this->info('✅ Données de test injectées.');
        }

        $this->newLine();
        $this->info('🎉 Application initialisée avec succès !');
        $this->table(
            ['Paramètre', 'Valeur'],
            [
                ['Admin',              $adminEmail],
                ['Prix abonnement',    $subscriptionPrice.' €/mois'],
                ['Taux commission',    $commissionRate.' €/patient/mois'],
                ['Stripe Product ID',  $productId],
                ['Stripe Price ID',    $priceId],
            ]
        );

        return self::SUCCESS;
    }
}
