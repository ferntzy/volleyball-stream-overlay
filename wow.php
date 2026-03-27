<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Volleyball Overlay · Controller</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;700;800;900&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  
  <style>
    /* Logo Upload Styles */
    .logo-upload {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      padding: 12px;
      border: 2px dashed rgba(255,255,255,0.2);
      border-radius: 8px;
      background: rgba(255,255,255,0.03);
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .logo-upload:hover {
      border-color: rgba(255,210,0,0.4);
      background: rgba(255,210,0,0.02);
    }
    .logo-upload.dragover {
      border-color: var(--yellow-bright);
      background: rgba(255,210,0,0.08);
      box-shadow: 0 0 20px rgba(255,210,0,0.2);
    }
    .logo-preview {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      object-fit: cover;
      border: 2px solid rgba(255,255,255,0.1);
      background: rgba(255,255,255,0.05);
    }
    .logo-placeholder {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.03));
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      color: rgba(255,255,255,0.3);
      border: 2px dashed rgba(255,255,255,0.2);
    }
    .logo-filename {
      font-size: 12px;
      color: rgba(255,255,255,0.6);
      word-break: break-all;
      max-width: 100%;
      text-align: center;
    }
    .remove-logo {
      position: absolute;
      top: -8px;
      right: -8px;
      width: 20px;
      height: 20px;
      background: rgba(255,0,0,0.8);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      color: white;
      cursor: pointer;
      opacity: 0;
      transition: opacity 0.2s ease;
    }
    .logo-upload.has-logo .remove-logo {
      opacity: 1;
    }
    .logo-upload.has-logo .remove-logo:hover {
      background: rgba(255,0,0,1);
    }
  </style>
</head>
<body class="ctrl-body">

  <!-- [Previous header and status unchanged] -->
  <header class="ctrl-header">
    <h1>Scoreboard Controller</h1>
    <p>Volleyball Livestream Overlay System</p>
  </header>

  <div class="status-bar">
    <span class="status-dot" id="statusDot"></span>
    <span id="statusLabel">Connecting…</span>
  </div>

  <div class="ctrl-grid">

    <!-- Tournament Info Card [unchanged] -->
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

    <!-- Team A Card - UPDATED WITH FILE UPLOAD -->
    <div class="card">
      <div class="card-title" style="color: var(--yellow-bright)">Team A</div>
      <div class="field">
        <label>Team Name</label>
        <input type="text" id="teamAName" placeholder="TEAM ALPHA" />
      </div>
      <div class="field">
        <label>Team Logo</label>
        <div class="logo-upload" id="logoUploadA">
          <input type="file" id="fileInputA" accept="image/*" style="display:none;">
          <div class="logo-placeholder" id="logoPlaceholderA">📷</div>
          <div class="logo-preview" id="logoPreviewA" style="display:none;"></div>
          <div class="logo-filename" id="logoFilenameA">Click or drag logo image</div>
          <div class="remove-logo" id="removeLogoA" title="Remove logo">×</div>
        </div>
      </div>

      <!-- Rest of Team A unchanged -->
      <div class="card-title" style="margin-top:14px">Score</div>
      <div class="score-row">
        <button class="btn btn-minus" onclick="adjustScore('A', -1)">−</button>
        <span class="score-display" id="scoreA">0</span>
        <button class="btn btn-plus" onclick="adjustScore('A', 1)">+</button>
      </div>
      <div class="sets-display" id="setsA">Sets won: <span class="set-badge">0</span></div>
      <div style="text-align:center;margin-top:12px">
        <button class="btn-serve" id="serveA" onclick="setServe('A')">● Serving</button>
      </div>
    </div>

    <!-- Team B Card - UPDATED WITH FILE UPLOAD -->
    <div class="card">
      <div class="card-title" style="color: var(--blue-bright)">Team B</div>
      <div class="field">
        <label>Team Name</label>
        <input type="text" id="teamBName" placeholder="TEAM BETA" />
      </div>
      <div class="field">
        <label>Team Logo</label>
        <div class="logo-upload" id="logoUploadB">
          <input type="file" id="fileInputB" accept="image/*" style="display:none;">
          <div class="logo-placeholder" id="logoPlaceholderB">📷</div>
          <div class="logo-preview" id="logoPreviewB" style="display:none;"></div>
          <div class="logo-filename" id="logoFilenameB">Click or drag logo image</div>
          <div class="remove-logo" id="removeLogoB" title="Remove logo">×</div>
        </div>
      </div>

      <!-- Rest of Team B unchanged -->
      <div class="card-title" style="margin-top:14px">Score</div>
      <div class="score-row">
        <button class="btn btn-minus" onclick="adjustScore('B', -1)">−</button>
        <span class="score-display" id="scoreB">0</span>
        <button class="btn btn-plus" onclick="adjustScore('B', 1)">+</button>
      </div>
      <div class="sets-display" id="setsB">Sets won: <span class="set-badge">0</span></div>
      <div style="text-align:center;margin-top:12px">
        <button class="btn-serve" id="serveB" onclick="setServe('B')">● Serving</button>
      </div>
    </div>

    <!-- Match Controls [unchanged] -->
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
      <div class="match-winner-banner" id="matchWinnerBanner"></div>
    </div>

  </div>

  <!-- [Previous scripts unchanged until logo handling] -->
  <script src="client.js"></script>

  <script>
    // [Previous functions unchanged...]

    // ===========================
    // NEW: Logo Upload Handlers
    // ===========================
    function setupLogoUpload(team) {
      const uploadEl = document.getElementById(`logoUpload${team}`);
      const fileInput = document.getElementById(`fileInput${team}`);
      const preview = document.getElementById(`logoPreview${team}`);
      const placeholder = document.getElementById(`logoPlaceholder${team}`);
      const filename = document.getElementById(`logoFilename${team}`);
      const removeBtn = document.getElementById(`removeLogo${team}`);

      // Click to open file dialog
      uploadEl.addEventListener('click', () => fileInput.click());

      // File selected
      fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            filename.textContent = file.name;
            uploadEl.classList.add('has-logo');
            
            // Send base64 to server
            sendAction("updateTeam", { 
              team, 
              field: "logo", 
              value: e.target.result 
            });
          };
          reader.readAsDataURL(file);
        }
      });

      // Drag & drop
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadEl.addEventListener(eventName, preventDefaults, false);
      });
      
      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }

      ['dragenter', 'dragover'].forEach(eventName => {
        uploadEl.addEventListener(eventName, () => uploadEl.classList.add('dragover'), false);
      });
      
      ['dragleave', 'drop'].forEach(eventName => {
        uploadEl.addEventListener(eventName, () => uploadEl.classList.remove('dragover'), false);
      });
      
      uploadEl.addEventListener('drop', (e) => {
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            filename.textContent = file.name;
            uploadEl.classList.add('has-logo');
            sendAction("updateTeam", { 
              team, 
              field: "logo", 
              value: e.target.result 
            });
          };
          reader.readAsDataURL(file);
        }
      }, false);

      // Remove logo
      removeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        preview.style.display = 'none';
        placeholder.style.display = 'flex';
        filename.textContent = 'Click or drag logo image';
        uploadEl.classList.remove('has-logo');
        fileInput.value = '';
        sendAction("updateTeam", { team, field: "logo", value: "" });
      });
    }

    // Initialize logo uploads
    setupLogoUpload('A');
    setupLogoUpload('B');

    // [Rest of previous script unchanged - debounceInfo, adjustScore, etc.]
    
    // Update renderState to handle logo preview from server state
    function renderState(state) {
      // [Previous renderState code...]
      
      // NEW: Handle logo preview from server state
      ["A","B"].forEach(t => {
        const key = t === "A" ? "teamA" : "teamB";
        const uploadEl = document.getElementById(`logoUpload${t}`);
        const preview = document.getElementById(`logoPreview${t}`);
        const placeholder = document.getElementById(`logoPlaceholder${t}`);
        const filename = document.getElementById(`logoFilename${t}`);
        
        if (state[key].logo) {
          preview.src = state[key].logo;
          preview.style.display = 'block';
          placeholder.style.display = 'none';
          filename.textContent = 'Logo loaded';
          uploadEl.classList.add('has-logo');
        } else {
          preview.style.display = 'none';
          placeholder.style.display = 'flex';
          filename.textContent = 'Click or drag logo image';
          uploadEl.classList.remove('has-logo');
        }
      });

      // [Rest of renderState unchanged...]
    }

    // [Rest of script unchanged]
  </script>
</body>
</html>