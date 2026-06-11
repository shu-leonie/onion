window.getPlaceholderImage = function(category) {
    const map = {
        'head': 'kopfbedeckung', 'upper_shirt': 't-shirt', 'upper_pulli': 'pullover',
        'upper_jacke': 'jacke', 'lower_pants': 'hose', 'lower_tights': 'strumpfhose',
        'feet_socks': 'socken', 'feet_shoes': 'schuhe', 'hand': 'accessoires',
        'sunglasses': 'sonnenbrille', 'sunscreen': 'sonnencreme'
    };
    return `/img/placeholders/platzhalter_${map[category] || category}.png`;
}

window.refresh_carousel_view = function(category_name) {
    const items = window.wardrobe_inventory[category_name] || [];
    const len = items.length;
    let capsule = null;
    const square = document.querySelector(`.layer-square[data-layer="${category_name}"]`);

    if (square) {
        capsule = square.closest('.outfit-capsule');
    } else {
        capsule = document.querySelector(`[data-category="${category_name}"]`);
    }
    if (!capsule) return;

    const hasGrid = capsule.querySelector('.layer-selection-grid') !== null;
    const input = document.getElementById(`input-${category_name}`);
    const prevBtn = capsule.querySelector('.prev');
    const nextBtn = capsule.querySelector('.next');
    const prevImg = capsule.querySelector('.item-prev');
    const nextImg = capsule.querySelector('.item-next');
    const mainImg = capsule.querySelector('.item-main');

    // Keine Items vorhanden -> Platzhalter anzeigen
    if (len === 0) {
        const placeholderUrl = window.getPlaceholderImage(category_name);
        if (square) {
            const img_preview = square.querySelector('.square-image');
            const plus = square.querySelector('.plus-icon');
            img_preview.src = placeholderUrl;
            img_preview.classList.remove('d-none');
            if (plus) plus.classList.add('d-none');
        }
        if (!hasGrid || window.current_active_layer === category_name) {
            if(mainImg) { mainImg.src = placeholderUrl; mainImg.classList.remove('d-none'); }
            if(prevImg) prevImg.classList.add('d-none');
            if(nextImg) nextImg.classList.add('d-none');
            if(prevBtn) prevBtn.classList.add('d-none');
            if(nextBtn) nextBtn.classList.add('d-none');
        }
        if (input) input.value = '';
        return;
    }

    const current = window.active_selection_indices[category_name];
    const prev = (current - 1 + len) % len;
    const next = (current + 1) % len;

    if (square) {
        const img_preview = square.querySelector('.square-image');
        const plus = square.querySelector('.plus-icon');
        img_preview.src = items[current].img;
        img_preview.classList.remove('d-none');
        if (plus) plus.classList.add('d-none');
    }

    if (!hasGrid || window.current_active_layer === category_name) {
        if(mainImg) mainImg.src = items[current].img;
        
        if (len === 1) {
            if(prevBtn) prevBtn.classList.add('d-none');
            if(nextBtn) nextBtn.classList.add('d-none');
            if(prevImg) prevImg.classList.add('d-none');
            if(nextImg) nextImg.classList.add('d-none');
        } else {
            if(prevBtn) prevBtn.classList.remove('d-none');
            if(nextBtn) nextBtn.classList.remove('d-none');
            if(prevImg) { prevImg.classList.remove('d-none'); prevImg.src = items[prev].img; }
            if(nextImg) { nextImg.classList.remove('d-none'); nextImg.src = items[next].img; }
        }
    }
    if (input) input.value = items[current].id;
}

document.addEventListener('DOMContentLoaded', function () {
    
    window.active_selection_indices = window.active_selection_indices || {};
    if (window.wardrobe_inventory) {
        Object.keys(window.wardrobe_inventory).forEach(cat => {
            if (window.active_selection_indices[cat] === undefined) {
                window.active_selection_indices[cat] = 0;
            }
        });
    }

    document.querySelectorAll('.outfit-capsule').forEach(capsule => {
        const base_cat = capsule.getAttribute('data-category');
        const hasGrid = capsule.querySelector('.layer-selection-grid') !== null;

        const getActiveCat = () => hasGrid ? window.current_active_layer : base_cat;

        const prevBtn = capsule.querySelector('.prev');
        if(prevBtn) {
            prevBtn.addEventListener('click', () => {
                const cat = getActiveCat();
                if (!cat || !window.wardrobe_inventory[cat] || window.wardrobe_inventory[cat].length === 0) return;
                
                window.active_selection_indices[cat] = (window.active_selection_indices[cat] - 1 + window.wardrobe_inventory[cat].length) % window.wardrobe_inventory[cat].length;
                window.refresh_carousel_view(cat);
            });
        }

        const nextBtn = capsule.querySelector('.next');
        if(nextBtn) {
            nextBtn.addEventListener('click', () => {
                const cat = getActiveCat();
                if (!cat || !window.wardrobe_inventory[cat] || window.wardrobe_inventory[cat].length === 0) return;
                
                window.active_selection_indices[cat] = (window.active_selection_indices[cat] + 1) % window.wardrobe_inventory[cat].length;
                window.refresh_carousel_view(cat);
            });
        }

        capsule.addEventListener('wheel', (e) => {
            e.preventDefault();
            const cat = getActiveCat();
            if (!cat || !window.wardrobe_inventory[cat] || window.wardrobe_inventory[cat].length === 0) return;

            if (e.deltaY > 0 || e.deltaX > 0) {
                window.active_selection_indices[cat] = (window.active_selection_indices[cat] + 1) % window.wardrobe_inventory[cat].length;
            } else {
                window.active_selection_indices[cat] = (window.active_selection_indices[cat] - 1 + window.wardrobe_inventory[cat].length) % window.wardrobe_inventory[cat].length;
            }
            window.refresh_carousel_view(cat);
        }, { passive: false });

        let touchStartX = 0;
        capsule.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        capsule.addEventListener('touchend', (e) => {
            let touchEndX = e.changedTouches[0].screenX;
            const cat = getActiveCat();
            if (!cat || !window.wardrobe_inventory[cat] || window.wardrobe_inventory[cat].length === 0) return;

            if (touchStartX - touchEndX > 50) { 
                window.active_selection_indices[cat] = (window.active_selection_indices[cat] + 1) % window.wardrobe_inventory[cat].length;
                window.refresh_carousel_view(cat);
            } else if (touchEndX - touchStartX > 50) { 
                window.active_selection_indices[cat] = (window.active_selection_indices[cat] - 1 + window.wardrobe_inventory[cat].length) % window.wardrobe_inventory[cat].length;
                window.refresh_carousel_view(cat);
            }
        }, { passive: true });
    });
    
    const outfitForm = document.getElementById('outfit-form');
    if (outfitForm) {
        outfitForm.addEventListener('submit', function(e) {
            this.querySelectorAll('input[type="hidden"]').forEach(input => {
                if (!input.value) {
                    // Falls das Feld leer ist (kein Item gewählt), schicken wir den Platzhalter-String
                    const categoryName = input.id.replace('input-', '');
                    input.value = 'placeholder:' + categoryName;
                }
            });
        });
    }
});