<!-- Live Market Tracker Popup (Final Version with Auto-Save) -->
<div id="marketTrackerPopup"
     style="display:none;
            position:fixed;
            top:70px;
            left:50%;
            transform:translateX(-50%);
            width:460px;
            max-height:80vh;
            overflow-y:auto;
            background:#3a3a3a;
            border:1px solid #FFC700;
            z-index:9999;
            padding:15px;
            box-shadow:0 4px 12px rgba(0,0,0,0.4);
            border-radius:8px;
            color:#fff;
            font-size:14px;">
    
    <!-- Header -->
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h5 style="margin:0; color: #FFC700; font-size:15px;">Live Market Tracker</h5>
        <button onclick="document.getElementById('marketTrackerPopup').style.display='none'"
                style="border:none;background:none;font-size:18px;color:#FFC700;">
            &times;
        </button>
    </div>

    <!-- Toggle Switch -->
    <div style="margin-top: 10px;">
        <label style="display:block; margin-bottom:6px;">Market Event:</label>
        <div style="position: relative; width: 220px; height: 28px; background: #333; border-radius: 20px; overflow: hidden; cursor: pointer;" onclick="toggleMarketMode()">
            <div id="toggleSlider" style="position: absolute; top: 0; left: 0; width: 50%; height: 100%; background: #FFC700; border-radius: 20px; transition: 0.3s;"></div>
            <div style="display: flex; height: 100%;">
                <div style="width: 50%; text-align: center; line-height: 28px; font-weight: bold; z-index: 2; color: black;" id="labelSelect">Calendar</div>
                <div style="width: 50%; text-align: center; line-height: 28px; font-weight: bold; z-index: 2; color: white;" id="labelManual">Manual</div>
            </div>
        </div>
    </div>

    <!-- Manual Entry Fields -->
    <div id="market_event_details" style="display:none; margin-top:10px;">
        <label>Market Name:</label>
        <input type="text" class="form-control" id="market_name" style="font-size:13px;" oninput="localStorage.setItem('market_field_market_name', this.value)">

        <label>Date:</label>
        <input type="date" class="form-control" id="market_date" style="font-size:13px;" oninput="localStorage.setItem('market_field_market_date', this.value)">

        <label>Start Time:</label>
        <input type="time" class="form-control" id="start_time" style="font-size:13px;" oninput="localStorage.setItem('market_field_start_time', this.value)">

        <label>End Time:</label>
        <input type="time" class="form-control" id="end_time" style="font-size:13px;" oninput="localStorage.setItem('market_field_end_time', this.value)">

        <div style="margin-top:10px;">
            <input type="checkbox" id="use_previous_sales"> Use previous sales data
            <div id="previous_sales_section" style="display:none; margin-top:5px;">
                <label>Previous Total:</label>
                <input type="number" class="form-control no-spinner"
       id="previous_total"
       style="font-size:16px; background:#1e1e1e; color:#fff; border:1px solid #ccc;
              padding:6px 10px; border-radius:4px; width:100%; box-sizing:border-box;"
       oninput="localStorage.setItem('market_field_previous_total', this.value)">

                <label>Goal Increase:</label><br/>
                <button class="btn btn-sm btn-outline-warning" onclick="setGoalPercent(5)">+5%</button>
                <button class="btn btn-sm btn-outline-warning" onclick="setGoalPercent(10)">+10%</button>
                <button class="btn btn-sm btn-outline-warning" onclick="setGoalPercent(15)">+15%</button>
            </div>
        </div>

        <label style="margin-top:10px;">Goal:</label>
       <input type="number" class="form-control no-spinner goal-highlight"
       id="sales_goal"
       style="font-size:16px; font-weight:bold; background:#FFC700; color:#000;
              border:2px solid #fff; border-radius:6px; text-align:center;
              padding:8px 12px; width:100%; box-sizing:border-box;"
       oninput="localStorage.setItem('market_field_sales_goal', this.value)">
    </div>

    <!-- Start Button -->
    <div style="text-align: center;">
        <button onclick="startMarketTracker()" class="btn btn-warning btn-start-tracker">
            Start Tracker
        </button>
    </div>

    <!-- Tracker Table -->
    <div id="tracker_table" style="display:none; margin-top:15px;">
        <table class="table table-sm" style="background:#2b2b2b; color:#fff; border:1px solid #444;">
            <thead style="background:#333;">
                <tr style="color:#FFC700;">
                    <th style="font-size:13px;">Hour</th>
                    <th style="font-size:13px;">Target</th>
                    <th style="font-size:13px;">Actual</th>
                    <th style="font-size:13px;">Gap</th>
                    <th style="font-size:13px;">Status</th>
                </tr>
            </thead>
            <tbody id="tracker_rows"></tbody>
        </table>
        <div id="motivation_line" style="font-weight:bold; color:#FFC700; margin-top:10px;"></div>
    </div>

    <!-- Recap -->
    <div id="recap_section" style="display:none; margin-top:15px; border-top:1px solid #FFC700; padding-top:10px;">
        <h6 style="color:#FFC700; font-size:14px;">Market Recap</h6>
        <p><strong>Total Sales:</strong> $<span id="recap_total_sales">0</span></p>
        <p><strong>Goal:</strong> $<span id="recap_goal">0</span></p>
        <p><strong>Top Product:</strong> <span id="recap_top_product">N/A</span></p>
        <p><strong>Best Hour:</strong> <span id="recap_best_hour">N/A</span></p>
        <p id="recap_message"><em>Great job today!</em></p>
    </div>
</div>

<!-- Styles -->
<style>
    input.no-spinner::-webkit-inner-spin-button,
    input.no-spinner::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input.no-spinner {
        -moz-appearance: textfield;
    }
    input.goal-highlight {
        font-size: 18px;
        font-weight: bold;
        background-color: #FFC700;
        color: #000;
        border: 2px solid #FFF;
        text-align: center;
    }
    input.hourly-input {
        width: 80px !important;
        font-weight: bold;
        font-size: 13px;
        text-align: center;
    }
    .btn-start-tracker {
        margin-top: 10px;
        font-weight: bold;
        font-size: 14px;
        padding: 6px 14px;
    }
</style>

<!-- Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Restore saved values
    const fields = ['market_name', 'market_date', 'start_time', 'end_time', 'previous_total', 'sales_goal'];
    fields.forEach(id => {
        const val = localStorage.getItem('market_field_' + id);
        if (val !== null) {
            const el = document.getElementById(id);
            if (el) el.value = val;
        }
    });

    const usePrev = localStorage.getItem('market_field_use_previous_sales');
    if (usePrev !== null) {
        const cb = document.getElementById('use_previous_sales');
        cb.checked = usePrev === 'true';
        document.getElementById('previous_sales_section').style.display = cb.checked ? 'block' : 'none';
    }

    document.getElementById('use_previous_sales').addEventListener('change', function () {
        document.getElementById('previous_sales_section').style.display = this.checked ? 'block' : 'none';
        localStorage.setItem('market_field_use_previous_sales', this.checked);
    });
});

function setGoalPercent(percent) {
    const prev = parseFloat(document.getElementById('previous_total').value || 0);
    const newGoal = prev + (prev * percent / 100);
    document.getElementById('sales_goal').value = Math.round(newGoal);
    localStorage.setItem('market_field_sales_goal', Math.round(newGoal));
}

let isManualMode = false;
function toggleMarketMode() {
    isManualMode = !isManualMode;
    const slider = document.getElementById('toggleSlider');
    const details = document.getElementById('market_event_details');
    const labelSelect = document.getElementById('labelSelect');
    const labelManual = document.getElementById('labelManual');

    if (isManualMode) {
        slider.style.left = '50%';
        details.style.display = 'block';
        labelSelect.style.color = 'white';
        labelManual.style.color = 'black';
    } else {
        slider.style.left = '0';
        details.style.display = 'none';
        labelSelect.style.color = 'black';
        labelManual.style.color = 'white';
    }
}

function startMarketTracker() {
    const goalInput = document.getElementById('sales_goal');
    const goal = parseFloat(goalInput.value) || 0;

    if (goal === 0) {
        alert('Please enter a goal before starting the tracker.');
        return;
    }

    document.getElementById('tracker_table').style.display = 'block';
    const tableBody = document.getElementById('tracker_rows');
    tableBody.innerHTML = '';

    const start = document.getElementById('start_time').value;
    const end = document.getElementById('end_time').value;

    if (!start || !end) {
        alert('Please enter start and end times.');
        return;
    }

    const startHour = parseInt(start.split(':')[0]);
    const endHour = parseInt(end.split(':')[0]);
    const totalHours = endHour - startHour;
    const hourlyGoal = goal / totalHours;

    for (let i = 0; i < totalHours; i++) {
        const hour = (startHour + i).toString().padStart(2, '0') + ":00";
        const row = document.createElement('tr');

        row.innerHTML = `
            <td style="font-size:13px;">${hour}</td>
            <td class="hourly-goal" style="font-size:13px;">$${hourlyGoal.toFixed(2)}</td>
            <td><input type="number" class="form-control form-control-sm actual-sales hourly-input no-spinner"></td>
            <td class="difference" style="font-size:13px;">0.00</td>
            <td class="status" style="font-size:13px;">–</td>
        `;

        tableBody.appendChild(row);
    }

    document.querySelectorAll('.actual-sales').forEach(input => {
        input.addEventListener('input', function () {
            const row = this.closest('tr');
            const goal = parseFloat(row.querySelector('.hourly-goal').innerText.replace('$', '')) || 0;
            const actual = parseFloat(this.value) || 0;
            const diff = actual - goal;

            row.querySelector('.difference').innerText = diff.toFixed(2);
            const status = row.querySelector('.status');
            if (diff > 0) status.innerText = '📈 Up';
            else if (diff < 0) status.innerText = '📉 Down';
            else status.innerText = '✅ On Track';
        });
    });
}
</script>