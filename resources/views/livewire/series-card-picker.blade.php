<div
    x-data="{
        search: '',
        get filteredCards() {
            if (!this.search) return $wire.availableCards;
            return $wire.availableCards.filter(c =>
                c.name.toLowerCase().includes(this.search.toLowerCase())
            );
        }
    }"
>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">

        {{-- Colonne gauche : cartes disponibles --}}
        <div style="background-color: var(--color-gray-50); border: 0.5px solid var(--color-gray-200); border-radius: 0.75rem; padding: 1rem;">
            <p style="font-size: 13px; font-weight: 500; color: var(--color-gray-600); margin: 0 0 10px;">
                {{ __('filament.series_card_picker.available') }}
            </p>

            <input
                type="text"
                x-model="search"
                placeholder="{{ __('filament.series_card_picker.search_placeholder') }}"
                style="width: 100%; margin-bottom: 10px; box-sizing: border-box;"
            />

            <div style="display: flex; flex-direction: column; gap: 8px; max-height: 400px; overflow-y: auto;">
                <template x-for="card in filteredCards" :key="card.id">
                    <div style="display: flex; align-items: center; gap: 10px; padding: 8px; border: 0.5px solid var(--color-gray-200); border-radius: 0.5rem;">
                        <div style="width: 48px; height: 36px; border-radius: 4px; flex-shrink: 0; overflow: hidden; background: var(--color-gray-100);">
                            <img
                                :src="'/storage/cards/' + card.drawn_animation_path"
                                :alt="card.name"
                                style="width: 100%; height: 100%; object-fit: cover;"
                                x-show="card.drawn_animation_path"
                            />
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p x-text="card.name" style="font-size: 13px; font-weight: 500; margin: 0; color: var(--color-gray-950); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></p>
                            <p x-text="card.duration ? card.duration + ' sec' : '-'" style="font-size: 12px; color: var(--color-gray-600); margin: 0;"></p>
                        </div>
                        <button
                            type="button"
                            @click="$wire.addCard(card.id)"
                            style="background: none; border: none; color: var(--color-gray-600); cursor: pointer; font-size: 20px; padding: 0 4px; line-height: 1;"
                            aria-label="{{ __('filament.series_card_picker.add') }}"
                        >+</button>
                    </div>
                </template>

                <p
                    x-show="filteredCards.length === 0"
                    style="font-size: 13px; color: var(--color-gray-400); text-align: center; padding: 1rem 0; margin: 0;"
                >
                    {{ __('filament.series_card_picker.no_results') }}
                </p>
            </div>
        </div>

        {{-- Colonne droite : cartes sélectionnées --}}
        <div style="background-color: var(--color-gray-50); border: 0.5px solid var(--color-gray-200); border-radius: 0.75rem; padding: 1rem;">
            <p style="font-size: 13px; font-weight: 500; color: var(--color-gray-600); margin: 0 0 10px;">
                {{ __('filament.series_card_picker.selected') }}
                <span style="color: var(--color-gray-400); font-weight: 400;">{{ __('filament.series_card_picker.drag_hint') }}</span>
            </p>

            <div id="selected-cards-list" style="display: flex; flex-direction: column; gap: 8px; max-height: 400px; overflow-y: auto; min-height: 48px;">
                @foreach($selectedCards as $index => $card)
                    <div
                        data-id="{{ $card['id'] }}"
                        style="display: flex; align-items: center; gap: 10px; padding: 8px; border: 0.5px solid var(--color-gray-200); border-radius: 0.5rem; background: var(--color-gray-50);"
                    >
                        <span style="font-size: 12px; font-weight: 500; color: var(--color-gray-400); min-width: 16px;">{{ $index + 1 }}</span>
                        <i class="ti ti-grip-vertical drag-handle" style="color: var(--color-gray-400); font-size: 16px; cursor: grab;" aria-hidden="true"></i>
                        <div style="width: 48px; height: 36px; border-radius: 4px; flex-shrink: 0; overflow: hidden; background: var(--color-gray-100);">
                            @if($card['drawn_animation_path'])
                                <img
                                    src="/storage/cards/{{ $card['drawn_animation_path'] }}"
                                    alt="{{ $card['name'] }}"
                                    style="width: 100%; height: 100%; object-fit: cover;"
                                />
                            @endif
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 13px; font-weight: 500; margin: 0; color: var(--color-gray-950); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $card['name'] }}</p>
                            <p style="font-size: 12px; color: var(--color-gray-600); margin: 0;">{{ $card['duration'] ? $card['duration'] . ' sec' : '-' }}</p>
                        </div>
                        <button
                            type="button"
                            wire:click="removeCard({{ $card['id'] }})"
                            style="background: none; border: none; color: var(--color-gray-600); cursor: pointer; font-size: 18px; padding: 0 4px; line-height: 1;"
                            aria-label="{{ __('filament.series_card_picker.remove') }}"
                        >×</button>
                    </div>
                @endforeach

                @if(empty($selectedCards))
                    <p style="font-size: 13px; color: var(--color-gray-400); text-align: center; padding: 1rem 0; margin: 0;">
                        {{ __('filament.series_card_picker.empty') }}
                    </p>
                @endif
            </div>

            <p style="font-size: 12px; color: var(--color-gray-400); margin: 10px 0 0; text-align: center;">
                {{ count($selectedCards) }} {{ __('filament.series_card_picker.count') }}
            </p>
        </div>

    </div>
</div>
