document.addEventListener('DOMContentLoaded', function () {
    const tagCheckboxes = document.querySelectorAll('#tags input[type="checkbox"]');
    
    function applyTagFilters() {
        const selectedTags = Array.from(document.querySelectorAll('#tags input[type="checkbox"]:checked'))
                                  .map(cb => parseInt(cb.value));
                                  
        Object.keys(window.original_inventory).forEach(cat => {
            if (selectedTags.length === 0) {
                window.wardrobe_inventory[cat] = [...window.original_inventory[cat]];
            } else {
                window.wardrobe_inventory[cat] = window.original_inventory[cat].filter(item => {
                
                    return selectedTags.some(tagId => item.tags.includes(tagId));
                });
            }

            window.active_selection_indices[cat] = 0;
            
            window.refresh_carousel_view(cat);
        });
    }

    tagCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', applyTagFilters);
    });
});