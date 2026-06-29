<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
  <title>Volume Bangun Ruang Ã¢â‚¬â€œ SD Integra</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      overscroll-behavior: contain;
      touch-action: manipulation;
    }
    .btn-shape {
      width: 100px;
      height: 100px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      color: white;
      margin: 8px;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.1s;
    }
    .btn-shape:active {
      transform: scale(0.95);
    }
    .slider-container {
      margin: 16px 0;
    }
    .keyboard-btn {
      width: 60px;
      height: 60px;
      margin: 4px;
      font-size: 1.2rem;
      font-weight: bold;
      background: #e2e8f0;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
    }
    .keyboard-btn:active {
      background: #cbd5e1;
    }
    .hidden { display: none; }
  </style>
</head>
<body class="bg-green-50 font-sans">

  <div class="container mx-auto px-4 py-6 max-w-4xl">
    <h1 class="text-3xl font-bold text-center text-green-800 mb-2">Hitung Volume Bangun Ruang</h1>
    <p class="text-center text-gray-600 mb-6">Sentuh bangun, atur ukuran, lalu hitung volumenya!</p>

    <!-- Pilih Bangun -->
    <div id="shape-select" class="text-center">
      <div class="flex flex-wrap justify-center">
        <div class="btn-shape bg-amber-500" onclick="selectShape('kubus')"> Kubus </div>
        <div class="btn-shape bg-emerald-500" onclick="selectShape('balok')"> Balok </div>
        <div class="btn-shape bg-rose-500" onclick="selectShape('tabung')"> Tabung </div>
        <div class="btn-shape bg-green-500" onclick="selectShape('prisma')"> Prisma </div>
      </div>
    </div>

    <!-- Konfigurasi Bangun -->
    <div id="config-section" class="hidden mt-6 bg-white p-6 rounded-xl shadow">
      <h2 id="shape-title" class="text-xl font-bold text-center mb-4"></h2>
      
      <!-- Visualisasi Bangun -->
      <div id="shape-visual" class="flex justify-center items-center h-64 bg-gray-50 rounded-lg mb-6 border border-gray-200 overflow-hidden relative">
        <!-- SVG will be injected here -->
      </div>

      <div id="sliders"></div>
      <div class="mt-4">
        <label class="block font-medium mb-2">Jawaban Anda (cmÃ‚Â³):</label>
        <input type="text" id="user-answer" class="w-full p-3 border rounded-lg text-center text-xl" readonly />
        <div class="mt-3 flex flex-wrap justify-center" id="virtual-keyboard"></div>
      </div>
      <div class="mt-4 text-center">
        <button class="btn bg-green-600 text-white px-6 py-3 rounded-lg font-bold" onclick="checkAnswer()">Hitung Volumenya!</button>
      </div>
    </div>

    <!-- Hasil & Umpan Balik -->
    <div id="feedback" class="hidden mt-6 p-6 rounded-xl text-center">
      <div id="feedback-text" class="text-xl font-bold mb-4"></div>
      <button class="btn bg-green-600 text-white px-6 py-2 rounded-lg" onclick="resetActivity()">Coba Lagi</button>
    </div>
  </div>

  <script>
    let selectedShape = '';
    let dimensions = {};

    const shapes = {
      kubus: { name: "Kubus", params: ['sisi'], formula: "s Ãƒâ€” s Ãƒâ€” s" },
      balok: { name: "Balok", params: ['panjang', 'lebar', 'tinggi'], formula: "p Ãƒâ€” l Ãƒâ€” t" },
      tabung: { name: "Tabung", params: ['jari-jari', 'tinggi'], formula: "Ãâ‚¬ Ãƒâ€” rÃ‚Â² Ãƒâ€” t (Ãâ‚¬ Ã¢â€°Ë† 3.14)" },
      prisma: { name: "Prisma Segitiga", params: ['alas', 'tinggi segitiga', 'tinggi prisma'], formula: "(Ã‚Â½ Ãƒâ€” a Ãƒâ€” tÃ¢â€šÂ) Ãƒâ€” tÃ¢â€šâ€š" }
    };

    function selectShape(shape) {
      selectedShape = shape;
      const shapeData = shapes[shape];
      document.getElementById('shape-title').textContent = shapeData.name;
      dimensions = {};
      
      // Buat slider
      let sliderHTML = '';
      shapeData.params.forEach(param => {
        const label = param.replace('tinggi segitiga', 'tinggi ÃŽâ€').replace('tinggi prisma', 'tinggi');
        sliderHTML += `
          <div class="slider-container">
            <label class="block font-medium">${label.charAt(0).toUpperCase() + label.slice(1)} (cm): <span id="val-${param}">5</span></label>
            <input type="range" min="1" max="15" value="5" 
                   class="w-full" 
                   oninput="updateDimension('${param}', this.value)"
                   ontouchstart="this.focus()"
                   ontouchend="this.blur()" />
          </div>
        `;
        dimensions[param] = 5;
      });
      document.getElementById('sliders').innerHTML = sliderHTML;
      
      // Buat keyboard virtual
      let kb = '';
      for (let i = 1; i <= 9; i++) {
        kb += `<button class="keyboard-btn" onclick="keyPress('${i}')">${i}</button>`;
      }
      kb += `<button class="keyboard-btn" onclick="keyPress('0')">0</button>`;
      kb += `<button class="keyboard-btn bg-red-200" onclick="keyPress('clear')">C</button>`;
      document.getElementById('virtual-keyboard').innerHTML = kb;

      document.getElementById('user-answer').value = '';
      document.getElementById('shape-select').classList.add('hidden');
      document.getElementById('config-section').classList.remove('hidden');
      document.getElementById('feedback').classList.add('hidden');
    }

    function updateDimension(param, value) {
      dimensions[param] = parseInt(value);
      document.getElementById(`val-${param}`).textContent = value;
      renderShape();
    }

    function renderShape() {
      const container = document.getElementById('shape-visual');
      let svgContent = '';
      const scale = 10; // Skala untuk visualisasi
      const cx = 150; // Pusat X canvas (300 width)
      const cy = 130; // Pusat Y canvas (250 height)
      
      if (selectedShape === 'kubus') {
        const s = dimensions.sisi * scale;
        // Isometric Cube
        const x = cx;
        const y = cy + s/2; 
        
        // Titik-titik sudut (proyeksi isometrik sederhana)
        // Pusat bawah = (x, y)
        // Kanan bawah = (x + s*0.866, y - s*0.5)
        // Kiri bawah = (x - s*0.866, y - s*0.5)
        // Atas pusat = (x, y - s)
        // Kanan atas = (x + s*0.866, y - s*1.5)
        // Kiri atas = (x - s*0.866, y - s*1.5)
        // Puncak = (x, y - s*2) -> salah, kubus isometrik semua sisi sama panjang di 2D
        
        // Gunakan pendekatan isometrik standar: 30 derajat
        const cos30 = 0.866;
        const sin30 = 0.5;
        
        // Titik pusat pertemuan 3 garis (tengah)
        const cX = cx;
        const cY = cy;
        
        // Titik-titik
        const p0 = {x: cX, y: cY}; // Center
        const p1 = {x: cX, y: cY - s}; // Top Vertical
        const p2 = {x: cX + s*cos30, y: cY + s*sin30}; // Right Bottom
        const p3 = {x: cX - s*cos30, y: cY + s*sin30}; // Left Bottom
        
        const p4 = {x: cX, y: cY + s}; // Bottom Vertical (tidak terlihat penuh, tapi untuk balok) - Sisi bawah kubus biasanya v shape
        // Kubus isometrik: 3 muka terlihat (Atas, Kiri Depan, Kanan Depan)
        
        // Titik luar
        const pTop = {x: cX, y: cY - 2*s*sin30}; // Top Corner
        const pLeft = {x: cX - s*cos30, y: cY - s*sin30}; // Left Top Corner
        const pRight = {x: cX + s*cos30, y: cY - s*sin30}; // Right Top Corner
        const pBot = {x: cX, y: cY + s}; // Bottom Corner
        
        // Recalculate based on true isometric projection logic
        // Center: (cx, cy)
        // Vertical edges go straight up/down? No, usually Y axis is vertical.
        // Let's draw:
        // Front Vertical Edge: (cx, cy + s/2) to (cx, cy - s/2)
        const h_2 = s/2;
        const w_2 = s * cos30;
        const v_off = s * sin30;

        // Center Point of the drawing
        const ccx = cx;
        const ccy = cy;

        // Vertices
        // Center Vertex (hidden inside usually, but this is the front corner for solid cube)
        const vCenter = {x: ccx, y: ccy}; 
        const vTop = {x: ccx, y: ccy - s};
        const vBot = {x: ccx, y: ccy + s};
        const vTR = {x: ccx + w_2, y: ccy - h_2}; // Top Right
        const vTL = {x: ccx - w_2, y: ccy - h_2}; // Top Left
        const vBR = {x: ccx + w_2, y: ccy + h_2}; // Bottom Right
        const vBL = {x: ccx - w_2, y: ccy + h_2}; // Bottom Left
        
        // Solid Cube Faces
        // Top Face
        svgContent += `<path d="M${vCenter.x},${vCenter.y} L${vTR.x},${vTR.y} L${vTop.x},${vTop.y} L${vTL.x},${vTL.y} Z" fill="#60a5fa" stroke="#1e3a8a" stroke-width="2" />`;
        // Right Face
        svgContent += `<path d="M${vCenter.x},${vCenter.y} L${vTR.x},${vTR.y} L${vBR.x},${vBR.y} L${vBot.x},${vBot.y} Z" fill="#3b82f6" stroke="#1e3a8a" stroke-width="2" />`;
        // Left Face
        svgContent += `<path d="M${vCenter.x},${vCenter.y} L${vTL.x},${vTL.y} L${vBL.x},${vBL.y} L${vBot.x},${vBot.y} Z" fill="#2563eb" stroke="#1e3a8a" stroke-width="2" />`;

        // Labels
        svgContent += `<text x="${vBR.x + 5}" y="${vBR.y}" fill="#1e40af" font-size="14" font-weight="bold">${dimensions.sisi}</text>`;
        svgContent += `<text x="${vBL.x - 20}" y="${vBL.y}" fill="#1e40af" font-size="14" font-weight="bold">${dimensions.sisi}</text>`;
        svgContent += `<text x="${vTop.x}" y="${vTop.y - 10}" fill="#1e40af" font-size="14" font-weight="bold" text-anchor="middle">${dimensions.sisi}</text>`;
        
      } else if (selectedShape === 'balok') {
        const p = dimensions.panjang * scale;
        const l = dimensions.lebar * scale;
        const t = dimensions.tinggi * scale;
        
        const cos30 = 0.866;
        const sin30 = 0.5;
        
        const ccx = cx;
        const ccy = cy; // Center of the front vertical edge
        
        // Proyeksi:
        // Panjang ke kanan bawah (30 derajat)
        // Lebar ke kiri bawah (30 derajat) -> atau sebaliknya
        // Tinggi vertikal
        
        // Vertices relative to center (front-top corner of the bottom face? No, let's center the whole object)
        
        // Let's use vCenter as the center of the object volume approx
        
        // Front Corner (vCenter)
        const vC = {x: ccx, y: ccy};
        
        // Directions
        const dx_p = p * cos30; // x component for panjang
        const dy_p = p * sin30; // y component for panjang (down)
        
        const dx_l = l * cos30; // x component for lebar
        const dy_l = l * sin30; // y component for lebar (down)
        
        // Vertices
        // Central Vertical Line Top and Bottom
        const vTop = {x: ccx, y: ccy - t/2};
        const vBot = {x: ccx, y: ccy + t/2};
        
        // Right side (Panjang)
        const vR_Top = {x: ccx + dx_p, y: ccy - t/2 - dy_p};
        const vR_Bot = {x: ccx + dx_p, y: ccy + t/2 - dy_p};
        
        // Left side (Lebar)
        const vL_Top = {x: ccx - dx_l, y: ccy - t/2 - dy_l};
        const vL_Bot = {x: ccx - dx_l, y: ccy + t/2 - dy_l};
        
        // Top Back Corner
        const vBack_Top = {x: ccx + dx_p - dx_l, y: ccy - t/2 - dy_p - dy_l};
        
        // Draw Faces (Painter's algorithm: Back to Front? No, just visible faces)
        // Top Face
        svgContent += `<path d="M${vTop.x},${vTop.y} L${vR_Top.x},${vR_Top.y} L${vBack_Top.x},${vBack_Top.y} L${vL_Top.x},${vL_Top.y} Z" fill="#34d399" stroke="#065f46" stroke-width="2" />`;
        // Right Face (Panjang x Tinggi)
        svgContent += `<path d="M${vTop.x},${vTop.y} L${vR_Top.x},${vR_Top.y} L${vR_Bot.x},${vR_Bot.y} L${vBot.x},${vBot.y} Z" fill="#10b981" stroke="#065f46" stroke-width="2" />`;
        // Left Face (Lebar x Tinggi)
        svgContent += `<path d="M${vTop.x},${vTop.y} L${vL_Top.x},${vL_Top.y} L${vL_Bot.x},${vL_Bot.y} L${vBot.x},${vBot.y} Z" fill="#059669" stroke="#065f46" stroke-width="2" />`;

        // Labels
        svgContent += `<text x="${(vBot.x + vR_Bot.x)/2 + 5}" y="${(vBot.y + vR_Bot.y)/2 + 15}" fill="#065f46" font-size="14" font-weight="bold">p=${dimensions.panjang}</text>`;
        svgContent += `<text x="${(vBot.x + vL_Bot.x)/2 - 30}" y="${(vBot.y + vL_Bot.y)/2 + 15}" fill="#065f46" font-size="14" font-weight="bold">l=${dimensions.lebar}</text>`;
        svgContent += `<text x="${vBot.x - 5}" y="${(vBot.y + vTop.y)/2}" fill="#065f46" font-size="14" font-weight="bold" text-anchor="end">t=${dimensions.tinggi}</text>`;
        
      } else if (selectedShape === 'tabung') {
        const r = dimensions['jari-jari'] * scale;
        const t = dimensions.tinggi * scale;
        
        const ccx = cx;
        const ccy = cy;
        
        const topY = ccy - t/2;
        const botY = ccy + t/2;
        
        // Ellipse ry (perspective) usually r * 0.3 or so
        const ry = r * 0.4;
        
        // Bottom Ellipse
        svgContent += `<ellipse cx="${ccx}" cy="${botY}" rx="${r}" ry="${ry}" fill="#fb7185" stroke="#be123c" stroke-width="2" />`;
        // Body Rect (behind bottom half ellipse? No, draw body then top)
        // Rect covering the middle
        svgContent += `<path d="M${ccx - r},${topY} L${ccx + r},${topY} L${ccx + r},${botY} L${ccx - r},${botY} Z" fill="#f43f5e" stroke="none" />`;
        // Side Lines
        svgContent += `<line x1="${ccx - r}" y1="${topY}" x2="${ccx - r}" y2="${botY}" stroke="#be123c" stroke-width="2" />`;
        svgContent += `<line x1="${ccx + r}" y1="${topY}" x2="${ccx + r}" y2="${botY}" stroke="#be123c" stroke-width="2" />`;
        
        // Top Ellipse
        svgContent += `<ellipse cx="${ccx}" cy="${topY}" rx="${r}" ry="${ry}" fill="#fda4af" stroke="#be123c" stroke-width="2" />`;
        
        // Labels
        // Radius
        svgContent += `<line x1="${ccx}" y1="${topY}" x2="${ccx + r}" y2="${topY}" stroke="#881337" stroke-dasharray="4" />`;
        svgContent += `<text x="${ccx + r/2}" y="${topY - 5}" fill="#881337" font-size="14" font-weight="bold" text-anchor="middle">r=${dimensions['jari-jari']}</text>`;
        // Height
        svgContent += `<text x="${ccx + r + 10}" y="${ccy}" fill="#881337" font-size="14" font-weight="bold">t=${dimensions.tinggi}</text>`;
        
      } else if (selectedShape === 'prisma') {
        const a = dimensions.alas * scale;
        const t_tri = dimensions['tinggi segitiga'] * scale;
        const t_p = dimensions['tinggi prisma'] * scale;
        
        const ccx = cx;
        const ccy = cy;
        
        // Prisma Segitiga (Tidur atau Berdiri? Biasanya berdiri untuk volume alas x tinggi)
        // Alas Segitiga di bawah
        
        // Segitiga Alas (Bottom)
        // Center of bottom triangle base
        const botY = ccy + t_p/2;
        const topY = ccy - t_p/2;
        
        // Coordinates for bottom triangle
        const b_p1 = {x: ccx - a/2, y: botY + t_tri/3}; // Left
        const b_p2 = {x: ccx + a/2, y: botY + t_tri/3}; // Right
        const b_p3 = {x: ccx, y: botY - 2*t_tri/3}; // Top (Back) - perspective is tricky
        
        // Let's draw it "laying down" or "standing up with perspective"
        // Let's do standing up.
        // Front face is a rectangle? No, Base is triangle.
        // Let's make the front face the triangle for easier visibility?
        // Or standard: Base at bottom.
        
        // Simple perspective:
        // Bottom Triangle:
        // p1 (Front Left), p2 (Front Right), p3 (Back Center)
        
        const p1_b = {x: ccx - a/2, y: botY};
        const p2_b = {x: ccx + a/2, y: botY};
        const p3_b = {x: ccx, y: botY - t_tri * 0.7}; // 0.7 for perspective foreshortening
        
        // Top Triangle
        const p1_t = {x: ccx - a/2, y: topY};
        const p2_t = {x: ccx + a/2, y: topY};
        const p3_t = {x: ccx, y: topY - t_tri * 0.7};
        
        // Draw Back Faces first
        // Back Left Face (p1_b, p3_b, p3_t, p1_t)
        // Back Right Face (p2_b, p3_b, p3_t, p2_t)
        
        // Fill Back faces (darker)
        svgContent += `<path d="M${p1_b.x},${p1_b.y} L${p3_b.x},${p3_b.y} L${p3_t.x},${p3_t.y} L${p1_t.x},${p1_t.y} Z" fill="#22c55e" stroke="#14532d" stroke-width="1" opacity="0.5" />`;
        svgContent += `<path d="M${p2_b.x},${p2_b.y} L${p3_b.x},${p3_b.y} L${p3_t.x},${p3_t.y} L${p2_t.x},${p2_t.y} Z" fill="#22c55e" stroke="#14532d" stroke-width="1" opacity="0.5" />`;

        // Front Face (Rectangle) -> No, it's a triangle prism.
        // The front is usually just the edge connecting p1 and p2?
        // Wait, if p3 is back, then p1-p2 is the front edge of the base.
        // So the front face is actually the rectangle p1_b, p2_b, p2_t, p1_t.
        svgContent += `<path d="M${p1_b.x},${p1_b.y} L${p2_b.x},${p2_b.y} L${p2_t.x},${p2_t.y} L${p1_t.x},${p1_t.y} Z" fill="#818cf8" stroke="#14532d" stroke-width="2" />`;
        
        // Top Triangle
        svgContent += `<path d="M${p1_t.x},${p1_t.y} L${p2_t.x},${p2_t.y} L${p3_t.x},${p3_t.y} Z" fill="#a5b4fc" stroke="#14532d" stroke-width="2" />`;
        
        // Bottom Triangle (Visible edges)
        // p1_b to p2_b is drawn by front face.
        // p1_b to p3_b and p2_b to p3_b are hidden or dashed.
        
        // Labels
        // Alas (a)
        svgContent += `<text x="${ccx}" y="${botY + 20}" fill="#14532d" font-size="14" font-weight="bold" text-anchor="middle">a=${dimensions.alas}</text>`;
        // Tinggi Prisma (tp)
        svgContent += `<text x="${p2_b.x + 10}" y="${ccy}" fill="#14532d" font-size="14" font-weight="bold">tp=${dimensions['tinggi prisma']}</text>`;
        // Tinggi Segitiga (t_tri) - Show on top face
        svgContent += `<line x1="${ccx}" y1="${p3_t.y}" x2="${ccx}" y2="${topY}" stroke="#14532d" stroke-dasharray="4" />`;
        svgContent += `<text x="${ccx + 5}" y="${topY - t_tri * 0.35}" fill="#14532d" font-size="12" font-weight="bold">tÃŽâ€=${dimensions['tinggi segitiga']}</text>`;

      }

      container.innerHTML = `<svg width="300" height="260" viewBox="0 0 300 260" xmlns="http://www.w3.org/2000/svg">${svgContent}</svg>`;
    }

    function keyPress(key) {
      const input = document.getElementById('user-answer');
      if (key === 'clear') {
        input.value = '';
      } else if (input.value.length < 6) {
        input.value += key;
      }
    }

    function checkAnswer() {
      const userAns = parseInt(document.getElementById('user-answer').value);
      if (isNaN(userAns)) {
        alert("Masukkan angka terlebih dahulu!");
        return;
      }

      let correct = 0;
      if (selectedShape === 'kubus') {
        correct = Math.pow(dimensions.sisi, 3);
      } else if (selectedShape === 'balok') {
        correct = dimensions.panjang * dimensions.lebar * dimensions.tinggi;
      } else if (selectedShape === 'tabung') {
        correct = Math.round(3.14 * Math.pow(dimensions['jari-jari'], 2) * dimensions.tinggi);
      } else if (selectedShape === 'prisma') {
        correct = Math.round(0.5 * dimensions.alas * dimensions['tinggi segitiga'] * dimensions['tinggi prisma']);
      }

      const feedbackEl = document.getElementById('feedback-text');
      if (userAns === correct) {
        feedbackEl.innerHTML = 'Ã¢Å“â€¦ <span class="text-green-700">Benar!</span><br>Volume = ' + correct + ' cmÃ‚Â³';
      } else {
        feedbackEl.innerHTML = 'Ã¢ÂÅ’ <span class="text-red-700">Belum tepat.</span><br>Rumus: ' + shapes[selectedShape].formula + '<br>Volume seharusnya = <strong>' + correct + ' cmÃ‚Â³</strong>';
      }

      document.getElementById('config-section').classList.add('hidden');
      document.getElementById('feedback').classList.remove('hidden');
    }

    function resetActivity() {
      document.getElementById('feedback').classList.add('hidden');
      document.getElementById('config-section').classList.add('hidden');
      document.getElementById('shape-select').classList.remove('hidden');
    }
  </script>
</body>
</html>
