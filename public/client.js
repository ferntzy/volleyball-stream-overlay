// ===========================
// client.js
// Shared WebSocket client logic
// Used by both controller and overlay
// ===========================

// ===========================
// WebSocket Connection
// Connects to the same host
// that served this page
// ===========================
const protocol = location.protocol === "https:" ? "wss" : "ws";
const socket = new WebSocket(`${protocol}://${location.host}`);

// Re-expose send as a helper
function sendAction(type, payload = {}) {
  if (socket.readyState === WebSocket.OPEN) {
    socket.send(JSON.stringify({ type, payload }));
  }
}

// ===========================
// Connection Events
// Log status to console
// ===========================
socket.addEventListener("open", () => {
  console.log("[WS] Connected to server");
});

socket.addEventListener("close", () => {
  console.warn("[WS] Disconnected. Attempting reconnect in 3s...");
  setTimeout(() => location.reload(), 3000);
});

socket.addEventListener("error", (e) => {
  console.error("[WS] Error:", e);
});