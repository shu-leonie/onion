window.getLocation = function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(success, error);
    } else {
        alert("Geolocation wird nicht unterstützt.");
    }
}

function success(position) {
    window.location.href = `/?latitude=${position.coords.latitude}&longitude=${position.coords.longitude}`;
}

function error(error) {
    const infoText = document.getElementById("locationInfoText");
    if (error.code === 1) infoText.innerHTML = "Standortzugriff wurde abgelehnt. Bitte Ort manuell eingeben.";
    else if (error.code === 2) infoText.innerHTML = "Standort konnte nicht ermittelt werden.";
    else if (error.code === 3) infoText.innerHTML = "Zeitüberschreitung beim Standortabruf.";
    else infoText.innerHTML = "Unbekannter Standortfehler.";
}

window.getWeatherByCity = async function() {
    const city = document.getElementById("cityInput").value;
    if (!city) return;
    try {
        const response = await fetch(`/weather/city?city=${encodeURIComponent(city)}`);
        const data = await response.json();
        if (!response.ok) {
            document.getElementById("cityError").innerHTML = data.error || "Fehler beim Abrufen des Wetters.";
            return;
        }
        window.location.href = `/?latitude=${data.latitude}&longitude=${data.longitude}`;
    } catch (e) {
        document.getElementById("cityError").innerHTML = "Netzwerkfehler.";
    }
}