<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
  <title>Gaya dan Gerak Ã¢â‚¬â€œ SD Integra</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      overscroll-behavior: contain;
      touch-action: none;
      background: #f0fdf4;
    }
    .hidden { display: none; }
    #playground {
      width: 100%;
      height: 300px;
      background: #dcfce7;
      border: 2px dashed #22c55e;
      border-radius: 12px;
      position: relative;
      overflow: hidden;
      margin: 16px 0;
    }
    .object {
      position: absolute;
      width: 60px;
      height: 60px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: white;
      user-select: none;
      cursor: grab;
      transition: transform 0.1s;
    }
    .object:active {
      cursor: grabbing;
      transform: scale(1.1);
    }
    .surface-rough { background: repeating-linear-gradient(45deg, #a3a3a3, #a3a3a3 10px, #d4d4d4 10px, #d4d4d4 20px); }
    .surface-smooth { background: linear-gradient(to bottom, #bae6fd, #dbeafe); }
    .info-box {
      background: white;
      padding: 16px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-top: 16px;
    }
    .btn-group {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin: 12px 0;
    }
    .btn-choice {
      padding: 10px 16px;
      background: #dbeafe;
      border: 2px solid #93c5fd;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-choice:hover {
      background: #bfdbfe;
    }
    .btn-choice.active {
      background: #bfdbfe;
      border-color: #3b82f6;
      color: #1e40af;
    }
    /* Disable text selection */
    .no-select {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
  </style>
</head>
<body class="font-sans">

  <div class="container mx-auto px-4 py-6 max-w-4xl no-select">
    <h1 class="text-3xl font-bold text-center text-green-800 mb-2">Eksperimen Gaya dan Gerak</h1>
    <p class="text-center text-gray-600 mb-6">Tarik benda dengan jari/mouse, lalu amati gerakannya!</p>

    <!-- Pilih Benda -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
      <h2 class="font-bold mb-2 text-green-900">Pilih Benda:</h2>
      <div class="btn-group">
        <div class="btn-choice active" onclick="selectObject('kotak', this)"> Kotak Kayu </div>
        <div class="btn-choice" onclick="selectObject('bola', this)"> Bola Besi </div>
        <div class="btn-choice" onclick="selectObject('balon', this)"> Balon </div>
      </div>
    </div>

    <!-- Pilih Permukaan -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
      <h2 class="font-bold mb-2 text-green-900">Pilih Permukaan:</h2>
      <div class="btn-group">
        <div class="btn-choice active" onclick="selectSurface('smooth', this)"> Licin (Es) </div>
        <div class="btn-choice" onclick="selectSurface('rough', this)"> Kasar (Karpet) </div>
      </div>
    </div>

    <!-- Playground -->
    <div id="playground" class="surface-smooth shadow-inner">
      <div id="moving-object" class="object bg-amber-500 text-3xl">Ã°Å¸â€œÂ¦</div>
      <div id="force-arrow" style="position: absolute; display: none; color: #ef4444; font-size: 32px; font-weight: bold; pointer-events: none; z-index: 10;">Ã¢â€ â€™</div>
    </div>

    <!-- Info Dinamis -->
    <div class="info-box border-l-4 border-green-500">
      <p id="info-text" class="text-lg">Sentuh dan tahan benda, lalu geser untuk menarik!</p>
    </div>

    <!-- Kuis Mini -->
    <div class="mt-8 bg-green-50 p-6 rounded-xl border border-green-100">
      <h3 class="font-bold text-lg mb-3 text-green-900">Ã°Å¸Â§Â  Kuis Cepat:</h3>
      <p class="mb-3 text-gray-700">Agar benda bergerak lurus ke kanan, gaya harus diberikan ke arah...</p>
      <div class="flex gap-3">
        <button class="btn-choice bg-white" onclick="checkQuiz('kiri')">Kiri</button>
        <button class="btn-choice bg-white" onclick="checkQuiz('kanan')">Kanan</button>
        <button class="btn-choice bg-white" onclick="checkQuiz('atas')">Atas</button>
      </div>
      <p id="quiz-feedback" class="mt-3 font-medium hidden p-2 rounded"></p>
    </div>
  </div>

  <script>
    let currentObject = 'kotak';
    let currentSurface = 'smooth';
    let isDragging = false;
    let startX, startY;
    let objX = 100, objY = 120;
    
    // Physics constants
    const frictionFactors = {
        'smooth': 0.05,
        'rough': 0.3
    };
    
    const massFactors = {
        'kotak': 1.0,
        'bola': 2.0, // Berat
        'balon': 0.2 // Ringan
    };

    const obj = document.getElementById('moving-object');
    const arrow = document.getElementById('force-arrow');
    const playground = document.getElementById('playground');
    const infoText = document.getElementById('info-text');

    // Initialize position
    resetObject();

    function selectObject(type, btn) {
      currentObject = type;
      
      // Update active state for buttons in the same group
      const group = btn.parentElement;
      group.querySelectorAll('.btn-choice').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      // Ganti ikon dan warna
      obj.classList.remove('bg-amber-500', 'bg-gray-600', 'bg-red-400');
      
      if (type === 'kotak') {
          obj.innerHTML = 'Ã°Å¸â€œÂ¦';
          obj.classList.add('bg-amber-500');
      } else if (type === 'bola') {
          obj.innerHTML = 'Ã¢Å¡Â½';
          obj.classList.add('bg-gray-600');
      } else {
          obj.innerHTML = 'Ã°Å¸Å½Ë†';
          obj.classList.add('bg-red-400');
      }
      
      resetObject();
    }

    function selectSurface(type, btn) {
      currentSurface = type;
      
      // Update active state
      const group = btn.parentElement;
      group.querySelectorAll('.btn-choice').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      playground.className = `surface-${type} shadow-inner`;
      resetObject();
    }

    function resetObject() {
      // Center vertically, left aligned slightly
      objX = 50;
      objY = (playground.offsetHeight - 60) / 2;
      
      updateObjectPosition();
      arrow.style.display = 'none';
      infoText.textContent = "Sentuh dan tahan benda, lalu geser untuk menarik!";
    }
    
    function updateObjectPosition() {
        obj.style.left = objX + 'px';
        obj.style.top = objY + 'px';
    }

    // Touch/Drag Logic
    obj.addEventListener('touchstart', handleStart, { passive: false });
    // Attach move/end to window to handle dragging outside the object
    window.addEventListener('touchmove', handleMove, { passive: false });
    window.addEventListener('touchend', handleEnd);

    obj.addEventListener('mousedown', (e) => {
      isDragging = true;
      startX = e.clientX;
      startY = e.clientY;
      e.preventDefault(); // Prevent text selection
    });

    window.addEventListener('mousemove', (e) => {
      if (!isDragging) return;
      applyForce(e.clientX, e.clientY);
    });

    window.addEventListener('mouseup', () => {
      if (isDragging) {
          isDragging = false;
          releaseObject();
      }
    });

    function handleStart(e) {
      isDragging = true;
      const touch = e.touches[0];
      startX = touch.clientX;
      startY = touch.clientY;
      // Prevent scrolling while dragging object
      if (e.cancelable) e.preventDefault();
    }

    function handleMove(e) {
      if (!isDragging) return;
      const touch = e.touches[0];
      applyForce(touch.clientX, touch.clientY);
      // Prevent scrolling
      if (e.cancelable) e.preventDefault();
    }

    function handleEnd() {
      if (isDragging) {
          isDragging = false;
          releaseObject();
      }
    }

    function applyForce(endX, endY) {
      const dx = endX - startX;
      const dy = endY - startY;
      
      // Update start point for next frame to make it follow the mouse/finger relative movement
      // But for this specific logic, it seems we want to "pull" it.
      // The original code uses (dx, dy) to calculate force. 
      // Let's stick to the original logic: "Tarik benda" -> movement based on drag distance
      
      // Tampilkan panah gaya
      // Arrow direction should be based on drag direction
      arrow.style.left = (objX + 60) + 'px';
      arrow.style.top = (objY + 15) + 'px';
      arrow.style.display = 'block';
      
      // Rotate arrow based on angle
      const angle = Math.atan2(dy, dx) * 180 / Math.PI;
      arrow.style.transform = `rotate(${angle}deg)`;
      arrow.innerHTML = 'Ã¢â€ â€™'; 

      // Physics Simulation Logic
      const mass = massFactors[currentObject];
      const friction = frictionFactors[currentSurface];
      
      // Force is proportional to drag distance (Hooke's law spring-like or just direct mapping)
      // Let's assume direct mapping for simplicity as in the original code, but improved
      // Force = distance dragged? 
      // Original code: moveX = objX + dx * 0.3
      // This actually moves the object *with* the mouse but with a lag factor (0.3).
      // Let's make it feel like dragging a heavy object.
      
      let moveFactor = 0.8; // Base movement factor
      
      // Higher mass -> harder to move
      moveFactor = moveFactor / mass;
      
      // Rough surface -> harder to move
      if (currentSurface === 'rough') {
          moveFactor = moveFactor * 0.6;
      }
      
      let moveX = objX + dx * moveFactor;
      let moveY = objY + dy * moveFactor;

      // Batas playground
      moveX = Math.max(0, Math.min(playground.offsetWidth - 60, moveX));
      moveY = Math.max(0, Math.min(playground.offsetHeight - 60, moveY));

      objX = moveX;
      objY = moveY;
      updateObjectPosition();
      
      // Update Start positions so it doesn't accelerate infinitely, just follows
      startX = endX;
      startY = endY;

      // Info dinamis
      const frictionText = currentSurface === 'rough' ? 'besar' : 'kecil';
      const massText = currentObject === 'bola' ? 'besar' : (currentObject === 'balon' ? 'kecil' : 'sedang');
      
      infoText.innerHTML = `
        <span class="font-bold text-green-600">Status Fisika:</span><br>
        Ã¢â‚¬Â¢ Gaya Gesek: <strong>${frictionText}</strong> (Permukaan ${currentSurface === 'rough' ? 'Kasar' : 'Licin'})<br>
        Ã¢â‚¬Â¢ Massa Benda: <strong>${massText}</strong> (${currentObject === 'kotak' ? 'Kotak Kayu' : (currentObject === 'bola' ? 'Bola Besi' : 'Balon')})
      `;
    }

    function releaseObject() {
      arrow.style.display = 'none';
      // Reset info after delay
      // Optional: Add some "slide" effect (inertia) here if we wanted to be fancy
    }

    // Kuis
    function checkQuiz(answer) {
      const fb = document.getElementById('quiz-feedback');
      fb.classList.remove('hidden', 'bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800');
      
      if (answer === 'kanan') {
        fb.innerHTML = 'Ã¢Å“â€¦ <strong>Benar!</strong> Gaya dorong/tarik ke kanan menyebabkan benda bergerak ke kanan.';
        fb.classList.add('bg-green-100', 'text-green-800');
      } else {
        fb.innerHTML = 'Ã¢ÂÅ’ <strong>Kurang tepat.</strong> Ingat, benda bergerak searah dengan gaya resultan yang diberikan.';
        fb.classList.add('bg-red-100', 'text-red-800');
      }
      fb.classList.remove('hidden');
    }
  </script>
</body>
</html>