const { token, baseUrl, loginUrl } = window.AttendanceConfig;

/**
 * Send IN / OUT event to the API
 * @param {string} type - 'in' or 'out'
 */
async function clock(type) {
    const res = await fetch(`${baseUrl}/attendance/${type}`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
        }
    });

    if (res.status === 401) {
        window.location.href = loginUrl;
        return;
    }

    const data = await res.json();
    document.getElementById('result').innerText =
        res.ok ? data.message : (data.message ?? 'Error');

    loadStatus();
    loadDailyReport?.();
}

/**
 * Load current attendance status (clocked in / out)
 */
/**
 * Load current attendance status and sync UI
 */
async function loadStatus() {
    const res = await fetch('/attendance/status', {
        headers: {
            'Accept': 'application/json'
        }
    });

    if (res.status === 401 || res.status === 403) {
        window.location.href = loginUrl;
        return;
    }

    const data = await res.json();

    document.querySelector('[onclick="clock(\'in\')"]').disabled = data.clocked_in;
    document.querySelector('[onclick="clock(\'out\')"]').disabled = !data.clocked_in;
}



/**
 * Initial page load
 */
document.addEventListener('DOMContentLoaded', () => {
    loadStatus();
    loadDailyReport();
});



/**
 * Loads daily attendance report for selected date
 */
async function loadDailyReport() {
    const date = document.getElementById('reportDate').value;

    const res = await fetch(`/api/attendance/daily?date=${date}`, {
        headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
        }
    });

    const data = await res.json();

    if (!res.ok) {
        document.getElementById('dailyReport').innerText =
            data.message ?? 'Error loading report';
        return;
    }

    let html = `<div class="text-sm text-blue-700 font-bold mb-3">Total Hours: ${data.total_hours}</div>`;

    html += `<ul class="mb-2">`;
    data.events.forEach(e => {
        html += `<li>${e.type} - ${e.time}</li>`;
    });
    html += `</ul>`;

    if (data.errors.length) {
        html += `<div class="text-red-600">`;
        data.errors.forEach(err => {
            html += `<div>${err}</div>`;
        });
        html += `</div>`;
    }

    document.getElementById('dailyReport').innerHTML = html;
}

/**
 * Initial page load logic
 */
document.addEventListener('DOMContentLoaded', () => {
    loadDailyReport();
});
