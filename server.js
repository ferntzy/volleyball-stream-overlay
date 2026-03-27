// ===========================
// Volleyball Overlay Server
// Express + WebSocket backend
// ===========================

const express = require("express");
const http = require("http");
const WebSocket = require("ws");
const path = require("path");
const os = require("os");

const app = express();
const server = http.createServer(app);

// ===========================
// WebSocket Server Setup
// ===========================
const wss = new WebSocket.Server({ server });

// ===========================
// Initial Match State
// ===========================
let matchState = {
  tournamentName: "VOLLEYBALL CHAMPIONSHIP",
  tournamentSubtitle: "Regional League · Season 2025",

  format: 3, // Best of 3 or Best of 5

  teamA: {
    name: "TEAM ALPHA",
    logo: "",
    score: 0,
    sets: 0,
  },

  teamB: {
    name: "TEAM BETA",
    logo: "",
    score: 0,
    sets: 0,
  },

  currentSet: 1,
  servingTeam: "A",

  matchOver: false,
  setWinner: null,
};

// ===========================
// Determine Target Score
// ===========================
function getTargetScore(state) {

  // Best of 3 → Set 3 = 15
  if (state.format === 3 && state.currentSet === 3) {
    return 15;
  }

  // Best of 5 → Set 5 = 15
  if (state.format === 5 && state.currentSet === 5) {
    return 15;
  }

  return 25;
}

// ===========================
// Volleyball Set Win Logic
// ===========================
function checkSetWinner(state) {

  const scoreA = state.teamA.score;
  const scoreB = state.teamB.score;

  const target = getTargetScore(state);

  // Team A wins
  if (scoreA >= target && scoreA - scoreB >= 2) {
    return "A";
  }

  // Team B wins
  if (scoreB >= target && scoreB - scoreA >= 2) {
    return "B";
  }

  return null;
}

// ===========================
// Match Winner Check
// ===========================
function checkMatchWinner(state) {

  const setsNeeded = Math.ceil(state.format / 2);

  if (state.teamA.sets >= setsNeeded) return "A";
  if (state.teamB.sets >= setsNeeded) return "B";

  return null;
}

// ===========================
// Broadcast to all clients
// ===========================
function broadcast(data) {

  const payload = JSON.stringify(data);

  wss.clients.forEach((client) => {

    if (client.readyState === WebSocket.OPEN) {
      client.send(payload);
    }

  });

}

// ===========================
// WebSocket Connection Handler
// ===========================
wss.on("connection", (ws) => {

  // Send current state
  ws.send(JSON.stringify({ type: "state", data: matchState }));

  ws.on("message", (raw) => {

    let msg;

    try {
      msg = JSON.parse(raw);
    } catch {
      return;
    }

    const { type, payload } = msg;

    // ======================
    // SCORE ACTION
    // ======================
    if (type === "score") {

      if (matchState.matchOver) return;

      const { team, delta } = payload;

      const key = team === "A" ? "teamA" : "teamB";

      matchState[key].score = Math.max(
        0,
        matchState[key].score + delta
      );

      // Rally winner serves
      if (delta > 0) {
        matchState.servingTeam = team;
      }

      // Check set winner
      const winner = checkSetWinner(matchState);

      if (winner) {

        matchState.setWinner = winner;

        matchState[winner === "A" ? "teamA" : "teamB"].sets++;

        const matchWinner = checkMatchWinner(matchState);

        if (matchWinner) {

          matchState.matchOver = true;

        } else {

          // Automatically go to next set
          matchState.currentSet++;

          // Reset scores
          matchState.teamA.score = 0;
          matchState.teamB.score = 0;

          matchState.setWinner = null;

        }

      } else {

        matchState.setWinner = null;

      }

    }

    // ======================
    // RESET CURRENT SET
    // ======================
    if (type === "resetSet") {

      matchState.teamA.score = 0;
      matchState.teamB.score = 0;

      matchState.setWinner = null;

    }

    // ======================
    // RESET MATCH
    // ======================
    if (type === "resetMatch") {

      matchState.teamA.score = 0;
      matchState.teamB.score = 0;

      matchState.teamA.sets = 0;
      matchState.teamB.sets = 0;

      matchState.currentSet = 1;

      matchState.servingTeam = "A";

      matchState.matchOver = false;

      matchState.setWinner = null;

    }

    // ======================
    // SERVE TOGGLE
    // ======================
    if (type === "serve") {

      matchState.servingTeam = payload.team;

    }

    // ======================
    // UPDATE TOURNAMENT INFO
    // ======================
    if (type === "updateInfo") {

      Object.assign(matchState, payload);

    }

    // ======================
    // UPDATE TEAM INFO
    // ======================
    if (type === "updateTeam") {

      const { team, field, value } = payload;

      const key = team === "A" ? "teamA" : "teamB";

      matchState[key][field] = value;

    }

    // ======================
    // CHANGE FORMAT
    // ======================
    if (type === "format") {

      matchState.format = payload.format;

    }

    // Broadcast update
    broadcast({ type: "state", data: matchState });

  });

});

// ===========================
// Static Files
// ===========================
app.use(express.static(path.join(__dirname, "public")));

app.get("/controller", (req, res) => {

  res.sendFile(
    path.join(__dirname, "public", "controller.html")
  );

});

app.get("/overlay", (req, res) => {

  res.sendFile(
    path.join(__dirname, "public", "overlay.html")
  );

});

// ===========================
// Start Server
// ===========================
const PORT = 3000;

server.listen(PORT, "0.0.0.0", () => {

  const interfaces = os.networkInterfaces();

  let localIP = "localhost";

  for (const iface of Object.values(interfaces)) {

    for (const alias of iface) {

      if (alias.family === "IPv4" && !alias.internal) {

        localIP = alias.address;

        break;

      }

    }

  }

  console.log("\n Volleyball Overlay Server Running\n");

  console.log(`Controller : http://${localIP}:${PORT}/controller`);
  console.log(`Overlay    : http://${localIP}:${PORT}/overlay`);
  console.log(`Local      : http://localhost:${PORT}/controller`);

  console.log("─".repeat(50));

});