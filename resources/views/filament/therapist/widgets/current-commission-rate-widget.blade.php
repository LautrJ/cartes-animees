<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <p style="font-size: 0.875rem; color: var(--color-gray-500); margin: 0;">Taux de commission actuel</p>
                <p style="font-size: 1.75rem; font-weight: 700; color: var(--color-primary-600); margin: 4px 0;">
                    {{ $rate }} €
                    <span style="font-size: 0.875rem; font-weight: 400; color: var(--color-gray-500);">/patient/mois</span>
                </p>
                <p style="font-size: 0.75rem; color: var(--color-gray-400); margin: 0;">En vigueur depuis le {{ $since }}</p>
            </div>
            <div style="background: var(--color-primary-100); border-radius: 9999px; padding: 12px; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 28px; height: 28px; color: var(--color-primary-600);" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
