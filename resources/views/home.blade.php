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

<main class="wrapper">
    <aside class="side-area branding">
        <div class="brand-content">
            <h1 class="onion-title">on¿on</h1>
            <p class="onion-subtitle">Was ziehst du<br>heute an?</p>
            <button type="submit" form="outfit-form" class="anziehen-btn pc-only">DAS HIER!</button>
        @guest
   
    <div class="auth-buttons mt-4">
        <a href="{{ route('login') }}" class="btn btn-primary me-2">
            Anmelden
        </a>
        <a href="{{ route('register') }}" class="btn btn-success">
            Registrieren
        </a>
    </div>
        @endguest
            
        </div>
    </aside>

    <section class="main-configurator">
        <form action="{{ route('outfit.save') }}" method="POST" id="outfit-form">
            @csrf

            @foreach($categories as $cat)
                <div class="outfit-capsule" data-category="{{ $cat }}">

                    @if($cat === 'upper')
                        <div class="layer-selection-grid">
                            <div class="layer-square" data-layer="upper_shirt">
                                <div class="square-content">
                                    <i class="bi bi-plus-lg plus-icon"></i>
                                    <img src="" class="square-image">
                                </div>
                            </div>
                            <div class="layer-square" data-layer="upper_pulli">
                                <div class="square-content">
                                    <i class="bi bi-plus-lg plus-icon"></i>
                                    <img src="" class="square-image">
                                </div>
                            </div>
                            <div class="layer-square" data-layer="upper_jacke">
                                <div class="square-content">
                                    <i class="bi bi-plus-lg plus-icon"></i>
                                    <img src="" class="square-image">
                                </div>
                            </div>
                        </div>

                        <div class="layer-carousel-view d-none">
                            <button type="button" class="nav-arrow prev"><i class="bi bi-chevron-left"></i></button>
                            <div class="view-window">
                                <img src="" class="item-side item-prev">
                                <div class="main-item-wrapper">
                                    <img src="" class="item-main shadow-aura">
                                    <div class="select-trigger-overlay"></div>
                                </div>
                                <img src="" class="item-side item-next">
                            </div>
                            <button type="button" class="nav-arrow next"><i class="bi bi-chevron-right"></i></button>
                        </div>

                        <input type="hidden" name="upper_shirt_id" id="input-upper_shirt">
                        <input type="hidden" name="upper_pulli_id" id="input-upper_pulli">
                        <input type="hidden" name="upper_jacke_id" id="input-upper_jacke">

                    @else
                        <button type="button" class="nav-arrow prev"><i class="bi bi-chevron-left"></i></button>
                        <div class="view-window">
                            <img src="" class="item-side item-prev">
                            <div class="main-item-wrapper">
                                <img src="" class="item-main shadow-aura">
                                <div class="select-trigger-overlay"></div>
                            </div>
                            <img src="" class="item-side item-next">
                        </div>
                        <button type="button" class="nav-arrow next"><i class="bi bi-chevron-right"></i></button>
                        <input type="hidden" name="{{ $cat }}_id" id="input-{{ $cat }}">
                    @endif
                </div>
            @endforeach
        </form>
        <button type="submit" form="outfit-form" class="anziehen-btn mobile-only">ANZIEHEN</button>
    </section>

    <!--wetter blcok ist grad einfach nur platzhalter den müsstet ihr bitte noch dynamsich befüllen (je nach dem wie du die sachen in der db dann final benennst...-->
    <aside class="side-area info">
        <div class="weather-desktop">

            <div class="weather-city">
                <i class="bi bi-geo-alt-fill"></i>
                <span id="locationText">
                    {{ $location }}
                </span>
            </div>

            <div class="city-search">
                <input
                    type="text"
                    id="cityInput"
                    placeholder="Ort eingeben"
                >

                <button
                    class="weather-button"
                    onclick="getWeatherByCity()"
                >
                    Ändern
                </button>
            </div>
            <p id="cityError" class="city-error"></p>

            <button
                class="location-link"
                    onclick="getLocation()"
                >
                <i class="bi bi-crosshair"></i>
                Aktuellen Standort nutzen
            </button>
            <p id="locationInfoText">
                {{ $weather['weather'][$current_time]['description'] }}
            </p>

            <div class="d-flex align-items-center gap-2">
                <h2 class="display-temp" id="temperatureText">
                    {{ $weather['apparentTemperature'][$current_time] }}°
                </h2>
                <div class="weather-icons">
                    <img src="/storage/weather-images/{{ $weather['weather'][$current_time]['image'] }}" class="weather-icon">
                </div>
            </div>
            <p class="condition" id="weatherInfoText">
                {{ $weather['weather'][$current_time]['description'] }}
            </p>

            <!--<div class="recommendation-box">
                <p>
                    hier ist theoretisch noch platz für einen kleinen infotext
                    zum wetter oder so idk
                </p>
            </div>-->

        </div>
    </aside>

    //tags ZUM TESTEN
    <div class="grid" id="tags">
        @foreach($tags as $index => $tag)
            <div>
                <input type="checkbox" id="tag-{{ $index }}" value="{{ $tag }}"/>
                <label for="tag-{{ $index }}">{{ $tag }}</label>
            </div>
        @endforeach
    </div>

</main>
<div class="mt-2">
  @include('modals.add-tag')
</div>

<div class="mt-3">
  {{-- @include('modals.upload-clothing') --}}
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@vite(['resources/js/modal.js'])

<!--Geolocation: geht nur mit herd secure vorher lokal aktivieren da https benötigt wird--> 


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

    window.location.href =
        `/?latitude=${lat}&longitude=${lon}`;
}

function error(error) {

    if (error.code === 1) {

        document.getElementById("locationInfoText").innerHTML =
            "Standortzugriff wurde abgelehnt. Bitte Ort manuell eingeben.";

    } else if (error.code === 2) {

        document.getElementById("locationInfoText").innerHTML =
            "Standort konnte nicht ermittelt werden.";

    } else if (error.code === 3) {

        document.getElementById("locationInfoText").innerHTML =
            "Zeitüberschreitung beim Standortabruf.";

    } else {

        document.getElementById("locationInfoText").innerHTML =
            "Unbekannter Standortfehler.";

    }
}


</script>

<script>
async function getWeatherByCity() {

    const city = document.getElementById("cityInput").value;

    if (!city) {
        return;
    }

    const response = await fetch(
        `/weather/city?city=${encodeURIComponent(city)}`
    );

    const data = await response.json();
    console.log(data);

    if (!response.ok) {

    document.getElementById("cityError").innerHTML =
        data.error;

    return;
    }
    document.getElementById("cityError").innerHTML = "";

    window.location.href =
        `/?latitude=${data.latitude}&longitude=${data.longitude}`;
}

</script>

</body>
</html>