@extends('layouts.app')
@vite(['resources/js/home/geolocation.js', 'resources/js/home/get-weather.js'])
<script>
    window.wardrobe_inventory = @json($recommendations);
</script>

<body data-tags="{{ json_encode($tags) }}">

@extends('layouts.app')

@section('content')
<main class="wrapper">
    <aside class="side-area branding">
        <div class="brand-content">
            <h1 class="onion-title">on¿on</h1>
            <p class="onion-subtitle">Was ziehst du<br>heute an?</p>

            @guest
            <div class="auth-buttons mt-4">
                <a href="{{ route('login') }}" class="btn btn-primary me-2">Anmelden</a>
                <a href="{{ route('register') }}" class="btn btn-primary me-2">Registrieren</a>
            </div>
            @endguest
            <a href="{{ url('/profile') }}" class="btn-primary large pc-only mt-3">ZUM PROFIL</a>
    </aside>

    <section class="main-configurator">
        <form action="{{ route('outfit.save') }}" method="POST" id="outfit-form">
            @csrf

            @php
                $layeredGroups = [
                    'head_group' => ['sunscreen', 'sunglasses', 'head'],
                    'upper_group' => ['upper_shirt', 'upper_pulli', 'upper_jacke'],
                    'lower_group' => ['lower_tights', 'lower_pants', 'hand'],
                    'feet_group' => ['feet_socks', 'feet_shoes']
                ];
            @endphp

            @foreach($layeredGroups as $groupKey => $layers)
                @php
                    $availableLayers = [];
                    foreach($layers as $l) {
                        if(isset($recommendations[$l])) {
                            $availableLayers[] = $l;
                        }
                    }
                @endphp

                @if(count($availableLayers) > 0)
                    @php
                        $isSingle = count($availableLayers) === 1;
                        $capsuleCategory = $isSingle ? $availableLayers[0] : $groupKey;
                    @endphp
                    
                    <div class="outfit-capsule" data-category="{{ $capsuleCategory }}">
                        @if(!$isSingle)
                            <div class="layer-selection-grid w-100">
                                @foreach($availableLayers as $layer)
                                    <div class="layer-square" data-layer="{{ $layer }}">
                                        <div class="square-content">
                                            <i class="bi bi-plus-lg plus-icon"></i>
                                            <img src="" class="square-image shadow-aura d-none">
                                        </div>
                                    </div>
                                    <input type="hidden" name="item_ids[]" id="input-{{ $layer }}">
                                @endforeach
                            </div>
                            <div class="layer-carousel-view w-100 d-none">
                        @else
                            <input type="hidden" name="item_ids[]" id="input-{{ $availableLayers[0] }}">
                            <div class="layer-carousel-view w-100">
                        @endif
                        
                            <button type="button" class="nav-arrow prev"><i class="bi bi-chevron-left"></i></button>
                            <div class="view-window position-relative flex-grow-1 d-flex justify-content-center">
                                <img src="" class="item-side item-prev">
                                <div class="main-item-wrapper">
                                    <img src="" class="item-main shadow-aura">
                                    @if(!$isSingle)
                                        <div class="select-trigger-overlay"></div>
                                    @endif
                                </div>
                                <img src="" class="item-side item-next">
                            </div>
                            <button type="button" class="nav-arrow next"><i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                @endif
            @endforeach
        </form>
        <button type="submit" form="outfit-form" class="btn-primary large mobile-only">ANZIEHEN</button>
    </section>

    <aside class="side-area info">
        <div class="weather-desktop">
            <div class="weather-city">
                <i class="bi bi-geo-alt-fill"></i>
                <span id="locationText">{{ $location ?? 'Unbekannt' }}</span>
            </div>

            <div class="city-search">
                <input type="text" id="cityInput" placeholder="Ort eingeben">
                <button class="btn-primary" onclick="getWeatherByCity()">Ändern</button>
            </div>
            <p id="cityError" class="city-error"></p>

            <button class="location-link" onclick="getLocation()">
                <i class="bi bi-crosshair"></i>
                Aktuellen Standort nutzen
            </button>
            <p id="locationInfoText" style="font-size: 12px; margin-top: 5px; color: #666;"></p>

            <div class="d-flex align-items-center gap-2">
                    <h2 class="display-temp" id="temperatureText">
                        {{ $weather['apparentTemperature'][$current_time] }}°
                    </h2>
                    <div class="weather-icons">
                        <img src="/storage/weather-images/{{ $weather['weather'][$current_time]['image'] }}"
                            class="weather-icon">
                    </div>
                </div>
                <p class="condition" id="weatherInfoText">
                    {{ $weather['weather'][$current_time]['description'] }}
                </p>
        </div>

            <div class="grid" id="tags">
         <p style="margin-top: 12px; font-weight: bold;"> Empfehlung nach Tags filtern: </p>
        @foreach($tags as $tag)
        <div>
            <input type="checkbox" id="tag-{{ $tag->id }}" value="{{ $tag->id }}" />
            <label for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
        </div>
        @endforeach
    </div>
    </aside>


</main>

<div class="mt-2">
</div>

<div class="mt-3">
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@vite(['resources/js/modal.js'])

<script>
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error);
    } else {
        alert("Geolocation wird nicht unterstützt.");
    }
}

function success(position) {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;
    window.location.href = `/?latitude=${lat}&longitude=${lon}`;
}

function error(error) {
    if (error.code === 1) {
        document.getElementById("locationInfoText").innerHTML = "Standortzugriff wurde abgelehnt. Bitte Ort manuell eingeben.";
    } else if (error.code === 2) {
        document.getElementById("locationInfoText").innerHTML = "Standort konnte nicht ermittelt werden.";
    } else if (error.code === 3) {
        document.getElementById("locationInfoText").innerHTML = "Zeitüberschreitung beim Standortabruf.";
    } else {
        document.getElementById("locationInfoText").innerHTML = "Unbekannter Standortfehler.";
    }
}

async function getWeatherByCity() {
    const city = document.getElementById("cityInput").value;
    if (!city) return;

    try {
        const response = await fetch(`/weather/city?city=${encodeURIComponent(city)}`);
        const data = await response.json();

        if (!response.ok) {
            document.getElementById("cityError").innerHTML = data.error || "Fehler beim Abrufen des Wetters.";
            return;
        }
        document.getElementById("cityError").innerHTML = "";
        window.location.href = `/?latitude=${data.latitude}&longitude=${data.longitude}`;
    } catch (e) {
        document.getElementById("cityError").innerHTML = "Netzwerkfehler.";
    }
}
</script>

@php
    $jsInventory = [];
    foreach($recommendations as $cat => $items) {
        
        if(empty($items)) {
            $jsInventory[$cat] = [];
        } else {
            $jsInventory[$cat] = array_map(function($item) {
                $tagIds = [];
                if (isset($item['tags'])) {
                    foreach($item['tags'] as $t) {
                        $tagIds[] = is_array($t) ? $t['id'] : $t->id;
                    }
                }

                return [
                    'id' => $item['id'],
                    'img' => asset($item['img']), 
                    'tags' => $tagIds
                ];
            }, $items);
        }
    }
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    const original_inventory = @json($jsInventory);
    let wardrobe_inventory = JSON.parse(JSON.stringify(original_inventory));
    
    const active_selection_indices = {};
    
    Object.keys(original_inventory).forEach(cat => {
        active_selection_indices[cat] = 0;
    });

    let current_active_layer = null;

    function getPlaceholderImage(category) {
        const map = {
            'head': 'kopfbedeckung',
            'upper_shirt': 't-shirt',
            'upper_pulli': 'pullover',
            'upper_jacke': 'jacke',
            'lower_pants': 'hose',
            'lower_tights': 'strumpfhose',
            'feet_socks': 'socken',
            'feet_shoes': 'schuhe',
            'hand': 'accessoires',
            'sunglasses': 'sonnenbrille',
            'sunscreen': 'sonnencreme'
        };
        
        const fileName = map[category] || category;
        return `/img/placeholders/platzhalter_${fileName}.png`; 
    }

    function refresh_carousel_view(category_name) {
        const items = wardrobe_inventory[category_name] || [];
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


        if (len === 0) {
            const placeholderUrl = getPlaceholderImage(category_name);

            if (square) {
                const img_preview = square.querySelector('.square-image');
                const plus = square.querySelector('.plus-icon');
                
                img_preview.src = placeholderUrl;
                img_preview.classList.remove('d-none');
                
                if (plus) plus.classList.add('d-none');
            }
            if (!hasGrid || current_active_layer === category_name) {
                if(mainImg) {
                    mainImg.src = placeholderUrl;
                    mainImg.classList.remove('d-none');
                }
                
                if(prevImg) prevImg.classList.add('d-none');
                if(nextImg) nextImg.classList.add('d-none');
                if(prevBtn) prevBtn.classList.add('d-none');
                if(nextBtn) nextBtn.classList.add('d-none');
            }
            
            if (input) input.value = '';
            return;
        }

        const current = active_selection_indices[category_name];
        const prev = (current - 1 + len) % len;
        const next = (current + 1) % len;

        if (square) {
            const img_preview = square.querySelector('.square-image');
            const plus = square.querySelector('.plus-icon');
            img_preview.src = items[current].img;
            img_preview.classList.remove('d-none');
            if (plus) plus.classList.add('d-none');
        }

        if (!hasGrid || current_active_layer === category_name) {
            if(mainImg) mainImg.src = items[current].img;
            
            if (len === 1) {
                if(prevBtn) prevBtn.classList.add('d-none');
                if(nextBtn) nextBtn.classList.add('d-none');
                if(prevImg) prevImg.classList.add('d-none');
                if(nextImg) nextImg.classList.add('d-none');
            } else {
                if(prevBtn) prevBtn.classList.remove('d-none');
                if(nextBtn) nextBtn.classList.remove('d-none');
                if(prevImg) {
                    prevImg.classList.remove('d-none');
                    prevImg.src = items[prev].img;
                }
                if(nextImg) {
                    nextImg.classList.remove('d-none');
                    nextImg.src = items[next].img;
                }
            }
        }

        if (input) input.value = items[current].id;
    }

    Object.keys(wardrobe_inventory).forEach(cat => refresh_carousel_view(cat));


    function applyTagFilters() {

        const selectedTags = Array.from(document.querySelectorAll('#tags input[type="checkbox"]:checked'))
                                  .map(cb => parseInt(cb.value));

        Object.keys(original_inventory).forEach(cat => {
            if (selectedTags.length === 0) {

                wardrobe_inventory[cat] = [...original_inventory[cat]];
            } else {
                wardrobe_inventory[cat] = original_inventory[cat].filter(item => {
                    return selectedTags.some(tagId => item.tags.includes(tagId));
                });
            }
            active_selection_indices[cat] = 0;
            refresh_carousel_view(cat);
        });
    }

    document.querySelectorAll('#tags input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', applyTagFilters);
    });

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
        const hasGrid = capsule.querySelector('.layer-selection-grid') !== null;

        const prevBtn = capsule.querySelector('.prev');
        if(prevBtn) {
            prevBtn.addEventListener('click', () => {
                const cat = hasGrid ? current_active_layer : base_cat;
                if (!cat || !wardrobe_inventory[cat] || wardrobe_inventory[cat].length === 0) return;
                active_selection_indices[cat] = (active_selection_indices[cat] - 1 + wardrobe_inventory[cat].length) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            });
        }

        const nextBtn = capsule.querySelector('.next');
        if(nextBtn) {
            nextBtn.addEventListener('click', () => {
                const cat = hasGrid ? current_active_layer : base_cat;
                if (!cat || !wardrobe_inventory[cat] || wardrobe_inventory[cat].length === 0) return;
                active_selection_indices[cat] = (active_selection_indices[cat] + 1) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            });
        }

        capsule.addEventListener('wheel', (e) => {
            e.preventDefault();
            const cat = hasGrid ? current_active_layer : base_cat;
            if (!cat || !wardrobe_inventory[cat] || wardrobe_inventory[cat].length === 0) return;

            if (e.deltaY > 0 || e.deltaX > 0) {
                active_selection_indices[cat] = (active_selection_indices[cat] + 1) % wardrobe_inventory[cat].length;
            } else {
                active_selection_indices[cat] = (active_selection_indices[cat] - 1 + wardrobe_inventory[cat].length) % wardrobe_inventory[cat].length;
            }
            refresh_carousel_view(cat);
        }, { passive: false });

        let touchStartX = 0;
        capsule.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        capsule.addEventListener('touchend', (e) => {
            let touchEndX = e.changedTouches[0].screenX;
            const cat = hasGrid ? current_active_layer : base_cat;
            if (!cat || !wardrobe_inventory[cat] || wardrobe_inventory[cat].length === 0) return;

            if (touchStartX - touchEndX > 50) { 
                active_selection_indices[cat] = (active_selection_indices[cat] + 1) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            } else if (touchEndX - touchStartX > 50) { 
                active_selection_indices[cat] = (active_selection_indices[cat] - 1 + wardrobe_inventory[cat].length) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            }
        }, { passive: true });
    });

    document.querySelectorAll('.select-trigger-overlay').forEach(overlay => {
        overlay.addEventListener('click', () => {
            const capsule = overlay.closest('.outfit-capsule');
            const grid = capsule.querySelector('.layer-selection-grid');
            if (grid) {
                capsule.querySelector('.layer-carousel-view').classList.add('d-none');
                grid.classList.remove('d-none');
                current_active_layer = null;
            }
        });
    });

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
</body>
</html>