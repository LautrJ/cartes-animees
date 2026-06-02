<div
    x-on:cards-updated.window="
        $wire.dispatch('cardsDataUpdated', { cardsJson: JSON.stringify($event.detail.cards) })
    "
>
    @livewire('series-card-picker', ['seriesId' => $seriesId ?? null])
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
    let sortableInstance = null;

    function initSortable() {
        const el = document.getElementById('selected-cards-list');
        if (el && typeof Sortable !== 'undefined') {
            if (sortableInstance) {
                sortableInstance.destroy();
            }
            sortableInstance = Sortable.create(el, {
                animation: 150,
                handle: '.drag-handle',
                onEnd(evt) {
                    const items = [...el.querySelectorAll('[data-id]')];
                    const orderedIds = items.map(i => parseInt(i.dataset.id));
                    Livewire.getByName('series-card-picker')[0].reorder(orderedIds);
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initSortable, 500);
    });

    document.addEventListener('livewire:updated', function() {
        setTimeout(initSortable, 100);
    });
</script>
