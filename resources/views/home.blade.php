<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>on¿on - Konfigurator</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js'])
    <script>
        window.wardrobe_inventory = @json($recommendations);
    </script>
</head>

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
                        if(!empty($recommendations[$l])) {
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
    </aside>

    <div class="grid d-none" id="tags">
        @foreach($tags as $tag)
        <div>
            <input type="checkbox" id="tag-{{ $tag->id }}" value="{{ $tag->id }}" />
            <label for="tag-{{ $tag->id }}">{{ $tag->name }}</label>
        </div>
        @endforeach
    </div>
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
        if(!empty($items)) {
            $jsInventory[$cat] = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'img' => asset($item['img']), 
                ];
            }, $items);
        }
    }
@endphp

<script>
document.addEventListener('DOMContentLoaded', function () {
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

        let capsule = null;
        const square = document.querySelector(`.layer-square[data-layer="${category_name}"]`);

        if (square) {
            capsule = square.closest('.outfit-capsule');
            const img_preview = square.querySelector('.square-image');
            const plus = square.querySelector('.plus-icon');
            img_preview.src = items[current].img;
            img_preview.classList.remove('d-none');
            if (plus) plus.classList.add('d-none');
        } else {
            capsule = document.querySelector(`[data-category="${category_name}"]`);
        }

        if (capsule) {
            const hasGrid = capsule.querySelector('.layer-selection-grid') !== null;
            
            if (!hasGrid || current_active_layer === category_name) {
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
        const hasGrid = capsule.querySelector('.layer-selection-grid') !== null;

        const prevBtn = capsule.querySelector('.prev');
        if(prevBtn) {
            prevBtn.addEventListener('click', () => {
                const cat = hasGrid ? current_active_layer : base_cat;
                if (!cat) return;
                active_selection_indices[cat] = (active_selection_indices[cat] - 1 + wardrobe_inventory[cat].length) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            });
        }

        const nextBtn = capsule.querySelector('.next');
        if(nextBtn) {
            nextBtn.addEventListener('click', () => {
                const cat = hasGrid ? current_active_layer : base_cat;
                if (!cat) return;
                active_selection_indices[cat] = (active_selection_indices[cat] + 1) % wardrobe_inventory[cat].length;
                refresh_carousel_view(cat);
            });
        }

        capsule.addEventListener('wheel', (e) => {
            e.preventDefault();
            const cat = hasGrid ? current_active_layer : base_cat;
            if (!cat) return;

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
            if (!cat) return;

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