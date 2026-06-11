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
    window.original_inventory = @json($jsInventory);
    window.wardrobe_inventory = JSON.parse(JSON.stringify(window.original_inventory));
</script>

@endsection
</body>
</html>