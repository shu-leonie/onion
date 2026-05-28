@extends('layouts.app')

@section('content')
<main class="wrapper">
    
    <aside class="side-area branding">
        <div class="brand-content">
            <h1 class="onion-title">on¿on</h1>
            <p class="onion-subtitle">Was ziehst du<br>heute an?</p>
            <a href="{{ url('/profile') }}" class="anziehen-btn pc-only" style="display: inline-block; text-decoration: none; text-align: center;">
                ZUM PROFIL
            </a>
        </div>
    </aside>

    <section class="main-configurator">
        <form action="{{ route('outfit.save') }}" method="POST" id="outfit-form">
            @csrf

            @if(!empty($recommendations['upper_shirt']) || !empty($recommendations['upper_pulli']) || !empty($recommendations['upper_jacke']))
                <div class="outfit-capsule" data-category="upper">
                    
                    <div class="layer-selection-grid w-100">
                        @foreach(['upper_shirt', 'upper_pulli', 'upper_jacke'] as $layer)
                            @if(!empty($recommendations[$layer]))
                                <div class="layer-square" data-layer="{{ $layer }}">
                                    <div class="square-content">
                                        <i class="bi bi-plus-lg plus-icon"></i>
                                        <img src="" class="square-image shadow-aura d-none">
                                    </div>
                                </div>
                                <input type="hidden" name="item_ids[]" id="input-{{ $layer }}">
                            @endif
                        @endforeach
                    </div>
                    <div class="layer-carousel-view w-100 d-none">
                        <button type="button" class="nav-arrow prev"><i class="bi bi-chevron-left"></i></button>
                        <div class="view-window position-relative flex-grow-1 d-flex justify-content-center">
                            <img src="" class="item-side item-prev">
                            <div class="main-item-wrapper">
                                <img src="" class="item-main shadow-aura">
                                <div class="select-trigger-overlay"></div>
                            </div>
                            <img src="" class="item-side item-next">
                        </div>
                        <button type="button" class="nav-arrow next"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>
            @endif

            @php
                $standardCategories = [
                    'head' => 'Kopfbedeckung',
                    'sunglasses' => 'Sonnenbrille',
                    'lower_pants' => 'Hose',
                    'lower_tights' => 'Strumpfhose',
                    'feet_socks' => 'Socken',
                    'feet_shoes' => 'Schuhe',
                    'hand' => 'Accessoires',
                    'sunscreen' => 'Sonnencreme'
                ];
            @endphp

            @foreach($standardCategories as $catKey => $catTitle)
                @if(!empty($recommendations[$catKey]))
                    <div class="outfit-capsule" data-category="{{ $catKey }}">
                        <div class="layer-carousel-view w-100">
                            <button type="button" class="nav-arrow prev"><i class="bi bi-chevron-left"></i></button>
                            <div class="view-window position-relative flex-grow-1 d-flex justify-content-center">
                                <img src="" class="item-side item-prev">
                                <div class="main-item-wrapper">
                                    <img src="" class="item-main shadow-aura">
                                </div>
                                <img src="" class="item-side item-next">
                            </div>
                            <button type="button" class="nav-arrow next"><i class="bi bi-chevron-right"></i></button>
                        </div>
                        <input type="hidden" name="item_ids[]" id="input-{{ $catKey }}">
                    </div>
                @endif
            @endforeach
        </form>

        <button type="submit" form="outfit-form" class="anziehen-btn mobile-only">ANZIEHEN</button>
    </section>

    <aside class="side-area info">
        <div class="weather-desktop">
            <div class="weather-city"><i class="bi bi-geo-alt-fill"></i> {{ $location ?? 'Unbekannt' }}</div>
            @if(isset($weather['apparentTemperature'][$current_time]))
                <h2 class="display-temp">{{ round($weather['apparentTemperature'][$current_time]) }}°</h2>
            @endif
            <p class="condition">{{ $weather['weather'][$current_time]['description'] ?? '' }}</p>
            <div class="weather-icons">
                <img src="{{ asset('images/weather/' . ($weather['weather'][$current_time]['image'] ?? 'unknown.png')) }}" alt="Wetter Icon" class="weather-icon">
                @if(isset($weather['cloudCover'][$current_time]) && $weather['cloudCover'][$current_time] > 50)
                    <i class="bi bi-cloud-fill overlap-cloud"></i>
                @endif
            </div>
        </div>
    </aside>

</main>

<!-- PHP Bereitet das Array für JavaScript vor -->
@php
    $jsInventory = [];
    foreach($recommendations as $cat => $items) {
        if(!empty($items)) {
            $jsInventory[$cat] = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    // Den Asset-Pfad direkt fixen, damit keine falschen Pfade entstehen
                    'img' => asset($item['img']), 
                ];
            }, $items);
        }
    }
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {

    // Dein dynamisches Array direkt aus Laravel geladen!
    const wardrobe_inventory = @json($jsInventory);

    const active_selection_indices = {};
    Object.keys(wardrobe_inventory).forEach(cat => {
        active_selection_indices[cat] = 0;
    });

    let current_active_layer = null;

    function refresh_carousel_view(category_name) {
        const items = wardrobe_inventory[category_name] || [];
        const len = items.length;
        if (len === 0) return;

        const current = active_selection_indices[category_name];
        const prev = (current - 1 + len) % len;
        const next = (current + 1) % len;

        let capsule;

        if (category_name.startsWith('upper_')) {
            capsule = document.querySelector('[data-category="upper"]');
            const square = document.querySelector(`.layer-square[data-layer="${category_name}"]`);
            if (square) {
                const img_preview = square.querySelector('.square-image');
                const plus = square.querySelector('.plus-icon');
                img_preview.src = items[current].img;
                img_preview.classList.remove('d-none');
                if (plus) plus.classList.add('d-none');
            }
        } else {
            capsule = document.querySelector(`[data-category="${category_name}"]`);
        }

        if (capsule) {
            if (!category_name.startsWith('upper_') || current_active_layer === category_name) {
                const prevImg = capsule.querySelector('.item-prev');
                const mainImg = capsule.querySelector('.item-main');
                const nextImg = capsule.querySelector('.item-next');
                if(prevImg) prevImg.src = items[prev].img;
                if(mainImg) mainImg.src = items[current].img;
                if(nextImg) nextImg.src = items[next].img;
            }

            const input = document.getElementById(`input-${category_name}`);
            if (input) input.value = items[current].id;
        }
    }

    Object.keys(wardrobe_inventory).forEach(cat => refresh_carousel_view(cat));

    document.querySelectorAll('.layer-square').forEach(square => {
        square.addEventListener('click', () => {
            const layer = square.getAttribute('data-layer');
            current_active_layer = layer;
            
            const capsule = square.closest('.outfit-capsule');
            capsule.querySelector('.layer-selection-grid').classList.add('d-none');
            capsule.querySelector('.layer-carousel-view').classList.remove('d-none');

            refresh_carousel_view(layer);
        });
    });

    document.querySelectorAll('.outfit-capsule').forEach(capsule => {
        const base_cat = capsule.getAttribute('data-category');

        const prevBtn = capsule.querySelector('.prev');
        if(prevBtn) {
            prevBtn.addEventListener('click', () => {
                const cat = (base_cat === 'upper') ? current_active_layer : base_cat;
                if (!cat) return;
                active_selection_indices[cat] = (active_selection_indices[cat] - 1 + wardrobe_inventory[cat].length) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            });
        }

        const nextBtn = capsule.querySelector('.next');
        if(nextBtn) {
            nextBtn.addEventListener('click', () => {
                const cat = (base_cat === 'upper') ? current_active_layer : base_cat;
                if (!cat) return;
                active_selection_indices[cat] = (active_selection_indices[cat] + 1) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            });
        }

        // Wheel Event (Dein Original JS)
        capsule.addEventListener('wheel', (e) => {
            e.preventDefault();
            const cat = (base_cat === 'upper') ? current_active_layer : base_cat;
            if (!cat) return;

            if (e.deltaY > 0 || e.deltaX > 0) {
                active_selection_indices[cat] = (active_selection_indices[cat] + 1) % wardrobe_inventory[cat].length;
            } else {
                active_selection_indices[cat] = (active_selection_indices[cat] - 1 + wardrobe_inventory[cat].length) % wardrobe_inventory[cat].length;
            }
            refresh_carousel_view(cat);
        }, { passive: false });

        // Touch/Swipe Event (Dein Original JS)
        let touchStartX = 0;
        capsule.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        capsule.addEventListener('touchend', (e) => {
            let touchEndX = e.changedTouches[0].screenX;
            const cat = (base_cat === 'upper') ? current_active_layer : base_cat;
            if (!cat) return;

            if (touchStartX - touchEndX > 50) { // links
                active_selection_indices[cat] = (active_selection_indices[cat] + 1) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            } else if (touchEndX - touchStartX > 50) { // rechts
                active_selection_indices[cat] = (active_selection_indices[cat] - 1 + wardrobe_inventory[cat].length) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            }
        }, { passive: true });
    });

    document.querySelectorAll('.select-trigger-overlay').forEach(overlay => {
        overlay.addEventListener('click', () => {
            const capsule = overlay.closest('.outfit-capsule');
            capsule.querySelector('.layer-carousel-view').classList.add('d-none');
            capsule.querySelector('.layer-selection-grid').classList.remove('d-none');
            current_active_layer = null;
        });
    });

    // Leere <input> Felder (für nicht existierende Kleidung) vor dem Senden deaktivieren,
    // damit das Backend nicht wegen leerer IDs meckert.
    document.getElementById('outfit-form').addEventListener('submit', function(e) {
        this.querySelectorAll('input[type="hidden"]').forEach(input => {
            if (!input.value) {
                input.disabled = true; 
            }
        });
    });
});
</script>
@endsection