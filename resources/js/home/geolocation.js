document.addEventListener('DOMContentLoaded', () => {
    const locationBtn = document.getElementById('locationBtn');
    if(locationBtn) {
        locationBtn.addEventListener('click', getLocation);
    }
})
        
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