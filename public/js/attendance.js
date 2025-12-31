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

    let html = `
    <div class="font-bold mb-3">
        Total Hours: ${data.total_hours}
    </div>

    <table class="w-full border border-gray-300 text-center text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">IN</th>
                <th class="border px-2 py-1">OUT</th>
            </tr>
        </thead>
        <tbody>
    `;

    let isFirstEventIn = false;
    for (let i = 0; i < data.events.length; i += 2) {
        const inEvent  = data.events[i];
        const outEvent = data.events[i + 1];

        isFirstEventIn = inEvent?.type === 'IN';
        if(!isFirstEventIn){
            i -= 1;
        }else{
            html += `
            <tr>
                <td class="border px-2 py-1">
                    ${inEvent?.type === 'IN' ? inEvent.time : ''}
                </td>
                <td class="border px-2 py-1">
                    ${outEvent?.type === 'OUT' ? outEvent.time : ''}
                </td>
            </tr>
            `;
        }
        
    }

    html += `
            </tbody>
        </table>
    `;

    if (data.errors.length) {
        html += `<div class="mt-2 text-red-600 text-xs">`;
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
