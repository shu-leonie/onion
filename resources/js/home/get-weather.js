document.addEventListener('DOMContentLoaded', () => {
    const weatherBtn = document.getElementById('weatherBtn');
    if(weatherBtn) {
        weatherBtn.addEventListener('click', getWeatherByCity);
    }
})

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