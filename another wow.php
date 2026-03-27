<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Volleyball Overlay · Controller</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;700;800;900&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  
</head>
<body class="ctrl-body">

  <!-- ===========================
       Page Header
       =========================== -->
  <header class="ctrl-header">
    <h1>Scoreboard Controller</h1>
    <p>Volleyball Livestream Overlay System</p>
  </header>

  <!-- Connection Status -->
  <div class="status-bar">
    <span class="status-dot" id="statusDot"></span>
    <span id="statusLabel">Connecting…</span>
  </div>

  <!-- ===========================
       Main Control Grid
       =========================== -->
  <div class="ctrl-grid">

    <!-- ===========================
         Tournament Info Card
         =========================== -->
    <div class="card full-width">
      <div class="card-title">Tournament Info</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="field">
          <label>Tournament Name</label>
          <input type="text" id="tournamentName" placeholder="e.g. Regional League 2025" />
        </div>
        <div class="field">
          <label>Tournament Subtitle</label>
          <input type="text" id="tournamentSubtitle" placeholder="e.g. Finals · Pool A" />
        </div>
        <div class="field">
          <label>Match Format</label>
          <select id="matchFormat">
            <option value="3">Best of 3 Sets</option>
            <option value="5">Best of 5 Sets</option>
          </select>
        </div>
      </div>
    </div>

    <!-- ===========================
         Team A Card
         =========================== -->
    <div class="card">
      <div class="card-title" style="color: var(--yellow-bright)">Team A</div>
      <div class="field">
        <label>Team Name</label>
        <input type="text" id="teamAName" placeholder="TEAM ALPHA" />
      </div>
      <div class="field">
        <label>Logo URL (optional)</label>
        <input type="text" id="teamALogo" placeholder="https://…/logo.png" />
      </div>

      <!-- Score Controls -->
      <div class="card-title" style="margin-top:14px">Score</div>
      <div class="score-row">
        <button class="btn btn-minus" onclick="adjustScore('A', -1)">−</button>
        <span class="score-display" id="scoreA">0</span>
        <button class="btn btn-plus" onclick="adjustScore('A', 1)">+</button>
      </div>
      <div class="sets-display" id="setsA">Sets won: <span class="set-badge">0</span></div>

      <!-- Serve Toggle -->
      <div style="text-align:center;margin-top:12px">
        <button class="btn-serve" id="serveA" onclick="setServe('A')">● Serving</button>
      </div>
    </div>

    <!-- ===========================
         Team B Card
         =========================== -->
    <div class="card">
      <div class="card-title" style="color: var(--blue-bright)">⚡ Team B</div>
      <div class="field">
        <label>Team Name</label>
        <input type="text" id="teamBName" placeholder="TEAM BETA" />
      </div>
      <div class="field">
        <label>Logo URL (optional)</label>
        <input type="text" id="teamBLogo" placeholder="https://…/logo.png" />
      </div>

      <!-- Score Controls -->
      <div class="card-title" style="margin-top:14px">Score</div>
      <div class="score-row">
        <button class="btn btn-minus" onclick="adjustScore('B', -1)">−</button>
        <span class="score-display" id="scoreB">0</span>
        <button class="btn btn-plus" onclick="adjustScore('B', 1)">+</button>
      </div>
      <div class="sets-display" id="setsB">Sets won: <span class="set-badge">0</span></div>

      <!-- Serve Toggle -->
      <div style="text-align:center;margin-top:12px">
        <button class="btn-serve" id="serveB" onclick="setServe('B')">● Serving</button>
      </div>
    </div>

    <!-- ===========================
         Match Controls Card
         =========================== -->
    <div class="card full-width">
      <div class="card-title">Match Controls</div>
      <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <span style="font-size:0.8rem;color:rgba(255,255,255,0.4);letter-spacing:0.08em">
          CURRENT SET: <strong id="currentSet" style="color:var(--blue-bright)">1</strong>
        </span>
        <div class="action-row" style="flex:1">
          <button class="btn btn-blue" onclick="nextSet()">▶ Next Set</button>
          <button class="btn btn-ghost" onclick="resetSet()">↺ Reset Set</button>
          <button class="btn btn-danger" onclick="resetMatch()">⚠ Reset Match</button>
        </div>
      </div>
      <!-- Match winner banner (shown when match ends) -->
      <div class="match-winner-banner" id="matchWinnerBanner"></div>
    </div>

  </div><!-- end ctrl-grid -->

  <!-- Load shared WS client -->
  <script src="client.js"></script>

  <script>

    
    // ===========================
    // Controller UI Logic
    // Sends actions to server
    // via WebSocket
    // ===========================

    // ===========================
    // Debounced Input Listeners
    // Sends text field changes
    // after user stops typing
    // =========================== 
    let infoTimer;
    function debounceInfo() {
      clearTimeout(infoTimer);
      infoTimer = setTimeout(sendInfo, 400);
    }

    function sendInfo() {
      sendAction("updateInfo", {
        tournamentName: document.getElementById("tournamentName").value,
        tournamentSubtitle: document.getElementById("tournamentSubtitle").value,
      });
    }

    // ===========================
    // Team field watchers
    // =========================== 
    let teamTimers = {};
    function debounceTeam(team, field, value) {
      clearTimeout(teamTimers[team + field]);
      teamTimers[team + field] = setTimeout(() => {
        sendAction("updateTeam", { team, field, value });
      }, 400);
    }

    // ===========================
    // Score Adjustment
    // Sends +1 / -1 actions
    // =========================== 
    function adjustScore(team, delta) {
      sendAction("score", { team, delta });
    }

    // ===========================
    // Serve Control
    // =========================== 
    function setServe(team) {
      sendAction("serve", { team });
    }

    // ===========================
    // Set / Match Controls
    // =========================== 
    function nextSet()    { sendAction("nextSet"); }
    function resetSet()   { sendAction("resetSet"); }
    function resetMatch() {
      if (confirm("Reset the full match? This will clear all scores and sets.")) {
        sendAction("resetMatch");
      }
    }

    // ===========================
    // Format Change
    // =========================== 
    document.getElementById("matchFormat").addEventListener("change", (e) => {
      sendAction("format", { format: parseInt(e.target.value) });
    });

    // ===========================
    // Attach text input listeners
    // =========================== 
    document.getElementById("tournamentName").addEventListener("input",    debounceInfo);
    document.getElementById("tournamentSubtitle").addEventListener("input", debounceInfo);

    document.getElementById("teamAName").addEventListener("input", (e) => debounceTeam("A", "name", e.target.value));
    document.getElementById("teamBName").addEventListener("input", (e) => debounceTeam("B", "name", e.target.value));
    document.getElementById("teamALogo").addEventListener("input", (e) => debounceTeam("A", "logo", e.target.value));
    document.getElementById("teamBLogo").addEventListener("input", (e) => debounceTeam("B", "logo", e.target.value));

    // ===========================
    // Render State to Controller UI
    // Called whenever WS state arrives
    // =========================== 
    function renderState(state) {
      // Tournament fields — only update if not focused
      const tnEl = document.getElementById("tournamentName");
      if (document.activeElement !== tnEl) tnEl.value = state.tournamentName;
      const tsEl = document.getElementById("tournamentSubtitle");
      if (document.activeElement !== tsEl) tsEl.value = state.tournamentSubtitle;

      // Team name/logo fields
      ["A","B"].forEach(t => {
        const nEl = document.getElementById(`team${t}Name`);
        const lEl = document.getElementById(`team${t}Logo`);
        const key = t === "A" ? "teamA" : "teamB";
        if (document.activeElement !== nEl) nEl.value = state[key].name;
        if (document.activeElement !== lEl) lEl.value = state[key].logo;
      });

      // Scores
      document.getElementById("scoreA").textContent = state.teamA.score;
      document.getElementById("scoreB").textContent = state.teamB.score;

      // Sets won
      document.getElementById("setsA").innerHTML = `Sets won: <span class="set-badge">${state.teamA.sets}</span>`;
      document.getElementById("setsB").innerHTML = `Sets won: <span class="set-badge">${state.teamB.sets}</span>`;

      // Current set
      document.getElementById("currentSet").textContent = state.currentSet;

      // Serve indicators
      document.getElementById("serveA").classList.toggle("active", state.servingTeam === "A");
      document.getElementById("serveB").classList.toggle("active", state.servingTeam === "B");

      // Format selector
      document.getElementById("matchFormat").value = String(state.format);

      // Match winner banner
      const banner = document.getElementById("matchWinnerBanner");
      if (state.matchOver) {
        const winnerName = state.teamA.sets > state.teamB.sets ? state.teamA.name : state.teamB.name;
        banner.textContent = `🏆 MATCH WINNER: ${winnerName}`;
        banner.classList.add("visible");
      } else {
        banner.classList.remove("visible");
      }
    }

    // ===========================
    // WebSocket State Handler
    // =========================== 
    socket.addEventListener("message", (event) => {
      const msg = JSON.parse(event.data);
      if (msg.type === "state") renderState(msg.data);
    });

    // ===========================
    // Connection Status Indicator
    // =========================== 
    const dot   = document.getElementById("statusDot");
    const label = document.getElementById("statusLabel");

    socket.addEventListener("open",  () => { dot.classList.add("connected"); label.textContent = "Connected"; });
    socket.addEventListener("close", () => { dot.classList.remove("connected"); label.textContent = "Disconnected — reconnecting…"; });
    
  </script>
</body>
</html>