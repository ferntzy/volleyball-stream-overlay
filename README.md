# Volleyball Livestream Overlay System

Real-time scoreboard overlay for volleyball broadcasts using Node.js, WebSockets, and OBS.

---

## File Structure

```
volleyball-overlay/
├── server.js           ← Node.js backend (Express + WebSocket)
├── package.json
└── public/
    ├── controller.html ← Match control interface
    ├── overlay.html    ← OBS browser source overlay (1920×1080)
    ├── style.css       ← Shared styles
    └── client.js       ← Shared WebSocket client
```

---

## Setup & Installation

### 1. Install dependencies

```bash
cd volleyball-overlay
npm install
```

### 2. Start the server

```bash
node server.js
```

You'll see output like:

```
🏐 Volleyball Overlay Server Running

  Controller : http://192.168.1.42:3000/controller
  Overlay    : http://192.168.1.42:3000/overlay

  Local      : http://localhost:3000/controller
```

---

## Usage

### Controller
Open in any browser (phone, tablet, laptop):
```
http://192.168.x.x:3000/controller
```

### Overlay (OBS)
1. In OBS, add a **Browser Source**
2. Set URL to:
   ```
   http://192.168.x.x:3000/overlay
   ```
3. Set **Width: 1920** · **Height: 1080**
4. Check **"Shutdown source when not visible"** = OFF
5. Check **"Refresh browser when scene becomes active"** = ON

---

## LAN Access (Multiple Devices)

All devices must be on the **same WiFi network**.

| Device | URL |
|--------|-----|
| OBS PC (overlay) | `http://192.168.x.x:3000/overlay` |
| Phone (controller) | `http://192.168.x.x:3000/controller` |
| Tablet (controller) | `http://192.168.x.x:3000/controller` |

The local IP is printed in the terminal when you start the server.

---

## Volleyball Rules Implemented

### Best of 3
- Each set: first to **25**, win by **2**
- Match winner: first to win **2 sets**

### Best of 5
- Sets 1–4: first to **25**, win by **2**
- Set 5: first to **15**, win by **2**
- Match winner: first to win **3 sets**

---

## Controller Features

| Feature | Description |
|---------|-------------|
| Tournament Name/Subtitle | Updates overlay banner instantly |
| Team Names & Logos | Accepts image URLs |
| Score +1 / −1 | Per team, updates serve indicator automatically |
| Serve Toggle | Manually set which team is serving |
| Next Set | Advances to next set, resets scores |
| Reset Set | Resets current set scores only |
| Reset Match | Full reset (confirm dialog) |
| Format Select | Switch between Bo3 and Bo5 |

---

## Customization

All colors are defined as CSS variables in `style.css`:

```css
:root {
  --yellow: #ffe135;
  --blue:   #5ab4ff;
  /* ... */
}
```

Change these to match your team or broadcast branding.
