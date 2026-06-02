<div style="display: flex; flex-direction: column; gap: 8px;">
    @forelse($record->cards()->orderBy('series_cards.order')->get() as $index => $card)
        <div style="display: flex; align-items: center; gap: 10px; padding: 8px; border: 0.5px solid var(--color-gray-200); border-radius: 0.5rem; background: var(--color-gray-50);">
            <span style="font-size: 12px; font-weight: 500; color: var(--color-gray-400); min-width: 20px;">{{ $index + 1 }}</span>
            <div style="width: 48px; height: 36px; border-radius: 4px; flex-shrink: 0; overflow: hidden; background: var(--color-gray-100);">
                @if($card->drawn_animation_path)
                    <img
                        src="/storage/cards/{{ $card->drawn_animation_path }}"
                        alt="{{ $card->name['fr'] ?? '-' }}"
                        style="width: 100%; height: 100%; object-fit: cover;"
                    />
                @endif
            </div>
            <div style="flex: 1; min-width: 0;">
                <p style="font-size: 13px; font-weight: 500; margin: 0; color: var(--color-gray-950); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $card->name['fr'] ?? '-' }}
                </p>
                <p style="font-size: 12px; color: var(--color-gray-600); margin: 0;">
                    {{ $card->duration ? $card->duration . ' sec' : '-' }}
                </p>
            </div>
        </div>
    @empty
        <p style="font-size: 13px; color: var(--color-gray-400); text-align: center; padding: 1rem 0; margin: 0;">
            {{ __('filament.series_card_picker.empty') }}
        </p>
    @endforelse
</div>
