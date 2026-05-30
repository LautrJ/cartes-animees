<?php

namespace App\Filament\Therapist\Resources\Cards\Pages;

use App\Enums\ContentValidationStatus;
use App\Filament\Therapist\Resources\Cards\CardResource;
use App\Models\Card;
use App\Models\ContentValidation;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateCard extends CreateRecord
{
    protected static string $resource = CardResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['is_validated'] = false;

        $slug = Str::slug($data['name']['fr'] ?? 'animation');
        $data = $this->renameAnimationFiles($data, $slug);

        return $data;
    }

    protected function afterCreate(): void
    {
        ContentValidation::create([
            'validatable_id' => $this->record->id,
            'validatable_type' => Card::class,
            'submitted_by' => auth()->id(),
            'status' => ContentValidationStatus::Pending,
            'submitted_at' => now(),
        ]);

        User::admins()->each(fn ($admin) => Notification::make()
            ->title(auth()->user()->getFilamentName().' a soumis une carte en attente de validation.')
            ->body($this->record->name['fr'] ?? '')
            ->warning()
            ->sendToDatabase($admin)
        );
    }

    private function renameAnimationFiles(array $data, string $slug): array
    {
        $disk = Storage::disk('cards');

        foreach ([
            'drawn_animation_path' => 'drawn',
            'real_animation_path' => 'real',
            'sound_path' => 'sounds',
        ] as $field => $suffix) {
            if (! empty($data[$field])) {
                $oldPath = $data[$field];
                $ext = pathinfo($oldPath, PATHINFO_EXTENSION);
                $newName = "{$suffix}/{$slug}_{$suffix}_".now()->timestamp.".{$ext}";

                if ($disk->exists($oldPath) && $oldPath !== $newName) {
                    $disk->move($oldPath, $newName);
                }

                $data[$field] = $newName;
            }
        }

        return $data;
    }
}
