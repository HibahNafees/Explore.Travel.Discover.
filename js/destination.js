// Destination page: Leaflet + OpenStreetMap map, and live weather from Open-Meteo (no API key).

(function () {
    // ---------------------------------------------------------------- map
    const mapEl = document.getElementById('map');
    if (mapEl && typeof L !== 'undefined') {
        const lat = parseFloat(mapEl.dataset.lat);
        const lon = parseFloat(mapEl.dataset.lon);
        const map = L.map(mapEl, { scrollWheelZoom: false }).setView([lat, lon], 10);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
        // bindPopup with a text node so the name is never parsed as HTML
        const label = document.createElement('strong');
        label.textContent = mapEl.dataset.name || '';
        L.marker([lat, lon]).addTo(map).bindPopup(label).openPopup();
    }

    // ---------------------------------------------------------------- weather
    const weatherEl = document.getElementById('weather');
    const body = weatherEl?.querySelector('[data-weather]');
    if (!weatherEl || !body) return;

    // WMO weather interpretation codes -> [description, Font Awesome icon]
    const codes = {
        0: ['clear sky', 'fa-sun'],
        1: ['mainly clear', 'fa-sun'],
        2: ['partly cloudy', 'fa-cloud-sun'],
        3: ['overcast', 'fa-cloud'],
        45: ['fog', 'fa-smog'], 48: ['rime fog', 'fa-smog'],
        51: ['light drizzle', 'fa-cloud-rain'], 53: ['drizzle', 'fa-cloud-rain'], 55: ['dense drizzle', 'fa-cloud-rain'],
        56: ['freezing drizzle', 'fa-cloud-rain'], 57: ['freezing drizzle', 'fa-cloud-rain'],
        61: ['light rain', 'fa-cloud-showers-heavy'], 63: ['rain', 'fa-cloud-showers-heavy'], 65: ['heavy rain', 'fa-cloud-showers-heavy'],
        66: ['freezing rain', 'fa-cloud-showers-heavy'], 67: ['freezing rain', 'fa-cloud-showers-heavy'],
        71: ['light snow', 'fa-snowflake'], 73: ['snow', 'fa-snowflake'], 75: ['heavy snow', 'fa-snowflake'], 77: ['snow grains', 'fa-snowflake'],
        80: ['rain showers', 'fa-cloud-sun-rain'], 81: ['rain showers', 'fa-cloud-sun-rain'], 82: ['violent showers', 'fa-cloud-showers-heavy'],
        85: ['snow showers', 'fa-snowflake'], 86: ['snow showers', 'fa-snowflake'],
        95: ['thunderstorm', 'fa-bolt'], 96: ['thunderstorm with hail', 'fa-bolt'], 99: ['thunderstorm with hail', 'fa-bolt'],
    };
    const describe = code => codes[code] || ['unknown', 'fa-question'];

    const el = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    };
    const icon = name => el('i', 'fas ' + name);

    const url = new URL('https://api.open-meteo.com/v1/forecast');
    url.search = new URLSearchParams({
        latitude: weatherEl.dataset.lat,
        longitude: weatherEl.dataset.lon,
        current: 'temperature_2m,apparent_temperature,relative_humidity_2m,weather_code,wind_speed_10m',
        daily: 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max',
        forecast_days: '5',
        timezone: 'auto',
    }).toString();

    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            const c = data.current;
            const [desc, iconName] = describe(c.weather_code);
            body.replaceChildren();

            const now = el('div', 'weather-now');
            now.append(icon(iconName));
            const nowText = el('div');
            nowText.append(
                el('span', 'weather-temp', Math.round(c.temperature_2m) + '°C'),
                el('span', 'weather-desc', desc),
                el('span', 'muted', 'feels like ' + Math.round(c.apparent_temperature) + '°C · humidity '
                    + c.relative_humidity_2m + '% · wind ' + Math.round(c.wind_speed_10m) + ' km/h')
            );
            now.append(nowText);

            const days = el('div', 'forecast');
            data.daily.time.forEach((date, i) => {
                const [dDesc, dIcon] = describe(data.daily.weather_code[i]);
                const day = el('div', 'forecast-day');
                day.title = dDesc;
                const label = i === 0 ? 'today'
                    : new Date(date + 'T12:00:00').toLocaleDateString('en-US', { weekday: 'short' });
                day.append(
                    el('span', 'forecast-label', label),
                    icon(dIcon),
                    el('span', 'forecast-temp', Math.round(data.daily.temperature_2m_max[i]) + '° / '
                        + Math.round(data.daily.temperature_2m_min[i]) + '°'),
                    el('span', 'muted', (data.daily.precipitation_probability_max[i] ?? 0) + '% rain')
                );
                days.append(day);
            });

            body.append(now, days);
        })
        .catch(() => {
            body.replaceChildren(el('p', 'muted', 'Weather is unavailable right now. Please try again later.'));
        });
})();
