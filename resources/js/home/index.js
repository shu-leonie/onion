//speichert den aktuellen index (also das item das gerade in der mitte sichbar ist) 
    // pro kategorie - das wir uns das beim anzeihen dann rauszeiehn können
    window.active_selection_indices = {
        head: 0,
        upper_shirt: 0, upper_pulli: 0, upper_jacke: 0,
        lower: 0, feet: 0
    };


document.addEventListener('DOMContentLoaded', function () {
    if(window.wardrobe_inventory) {
        Object.values(window.wardrobe_inventory).forEach(category => {
            category.forEach(item => {
                    item.hide = false;
                }
            )
        });

        //initalisierung aller kleidungsstücke beim laden der seite
        Object.keys(window.wardrobe_inventory).forEach(cat => {
            window.findNextVisibleIndex(cat, window.active_selection_indices[cat], +1);
            window.refresh_carousel_view(cat);
        });
    }
});