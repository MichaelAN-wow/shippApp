<div id="trace-widget">
    <!-- Floating Gear Button -->
    <button id="traceWidgetBtn" onclick="toggleTraceWidget()">
        <img src="/images/trace_icon.png" alt="Trace Icon">
    </button>

    <!-- Widget Panel -->
    <div id="traceWidgetPanel">
        <div class="trace-header">
            <div class="trace-header-text">
                <div class="trace-title">TRACE</div>
                <div class="trace-subtitle">Tactical Response & Automated Command Engine</div>
            </div>
            <button class="trace-close-btn" onclick="toggleTraceWidget()">×</button>
        </div>

        <div class="trace-body" id="traceMessageContainer">
            <div class="trace-msg trace-bot">Hey there 👋 How can I help?</div>
        </div>

        <div class="trace-footer">
            <input type="text" id="traceCommandInput" placeholder="Type your message…" onkeydown="if(event.key === 'Enter') sendTraceCommand()">
            <button onclick="sendTraceCommand()">➤</button>
        </div>
    </div>
</div>

<style>
#trace-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

#traceWidgetBtn {
    background: #111;
    border: none;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

#traceWidgetBtn img {
    width: 36px;
    height: 36px;
}

#traceWidgetPanel {
    display: none;
    flex-direction: column;
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 320px;
    background: #1e1e1e;
    border: 2px solid #ffcc00;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,0,0,0.4);
    font-family: 'Segoe UI', sans-serif;
}

.trace-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #111;
    padding: 10px 14px;
    color: #fff;
}

.trace-header-text {
    display: flex;
    flex-direction: column;
}

.trace-title {
    font-size: 14px;
    font-weight: bold;
}

.trace-subtitle {
    font-size: 11px;
    color: #ffcc00;
    margin-top: 2px;
}

.trace-close-btn {
    background: transparent;
    border: none;
    color: #ccc;
    font-size: 20px;
    cursor: pointer;
    margin-left: 8px;
}

.trace-close-btn:hover {
    color: #fff;
}

.trace-body {
    background: #2b2b2b;
    padding: 10px;
    height: 220px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.trace-msg {
    padding: 10px 14px;
    border-radius: 6px;
    max-width: 80%;
    font-size: 14px;
    word-wrap: break-word;
}

.trace-bot {
    background: #3d3d3d;
    color: #fff;
    align-self: flex-start;
}

.trace-user {
    background: #ffcc00;
    color: #111;
    font-weight: bold;
    align-self: flex-end;
}

.trace-footer {
    display: flex;
    border-top: 1px solid #333;
    background: #1e1e1e;
}

.trace-footer input {
    flex: 1;
    padding: 10px;
    background: #111;
    border: none;
    color: #fff;
    font-size: 14px;
    outline: none;
    border-top-left-radius: 6px;
}

.trace-footer button {
    padding: 10px 16px;
    background: #ffcc00;
    color: #111;
    border: none;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    border-top-right-radius: 6px;
    transition: background 0.2s ease-in-out;
}

.trace-footer button:hover {
    background: #ffd633;
}
</style>

<script>
function toggleTraceWidget() {
    const panel = document.getElementById('traceWidgetPanel');
    panel.style.display = panel.style.display === 'flex' ? 'none' : 'flex';
}

function sendTraceCommand() {
    const input = document.getElementById('traceCommandInput');
    const command = input.value.trim();
    if (!command) return;

    const container = document.getElementById('traceMessageContainer');

    // Show user message
    const userMsg = document.createElement('div');
    userMsg.className = 'trace-msg trace-user';
    userMsg.innerText = command;
    container.appendChild(userMsg);
    input.value = '';
    container.scrollTop = container.scrollHeight;

    // Loading message
    const loadingMsg = document.createElement('div');
    loadingMsg.className = 'trace-msg trace-bot';
    loadingMsg.innerText = 'Trace is thinking... 🤖';
    container.appendChild(loadingMsg);
    container.scrollTop = container.scrollHeight;

    fetch("{{ route('trace.command') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ command })
    })
    .then(res => res.json())
    .then(data => {
loadingMsg.innerText = data.message || data.status || '✅ Done.';
container.scrollTop = container.scrollHeight;
    })
    .catch(err => {
        loadingMsg.innerText = '❌ Error: ' + err.message;
    });
}
</script>