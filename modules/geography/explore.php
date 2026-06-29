<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Jelajahi Indonesia â€“ SD Integra</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      overscroll-behavior: contain;
      touch-action: manipulation;
    }
    /* Style untuk peta SVG yang di-inject */
    /* Target semua path di dalam grup yang memiliki ID di dalam Indonesia-Map */
    /* Kecuali Lautan dan Outsider */
    svg g[id] path {
      transition: fill 0.2s;
      cursor: pointer;
      stroke: #16a34a; /* Green-600 */
      stroke-width: 0.5px;
    }

    /* Default warna provinsi (menimpa style inline dari SVG) */
    /* Kita gunakan !important untuk memastikan override style inline */
    #Indonesia-Map g[id] path {
      fill: #86efac !important; /* Green-300 */
    }

    /* Hover & Active states */
    #Indonesia-Map g[id]:hover path, 
    #Indonesia-Map g[id].active path {
      fill: #15803d !important; /* Green-700 */
    }

    /* Lautan background */
    #Lautan rect {
      fill: #dcfce7 !important; /* Sky-100 */
      stroke: none;
    }

    .info-box {
      max-width: 300px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      padding: 16px;
      margin-top: 16px;
    }
    .hidden { display: none; }
    .quiz-option {
      display: block;
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      background: #f1f5f9;
      border: 2px solid #cbd5e1;
      border-radius: 8px;
      text-align: left;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
    }
    .quiz-option:hover { background: #e2e8f0; }
    .quiz-option.selected { border-color: #0d9488; background: #f0fdf4; }
    .btn {
      background: #0d9488;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn:hover { background: #0b7a75; }
  </style>
</head>
<body class="bg-green-50 font-sans">

  <div class="container mx-auto px-4 py-6 max-w-6xl">
    <h1 class="text-3xl font-bold text-center text-emerald-800 mb-2">Jelajahi Kenampakan Alam Indonesia</h1>
    <p class="text-center text-gray-600 mb-6">Sentuh provinsi untuk belajar, lalu uji pengetahuanmu!</p>

    <!-- Main View: Map -->
    <div id="map-view" class="block">
      <div class="flex flex-col lg:flex-row items-center gap-6">
        <div class="w-full lg:w-3/4">
          <div style="overflow: hidden; border-radius: 12px; background: #dcfce7; padding: 4px; border: 4px solid #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <!-- Inject SVG Here -->
            <?php
              $svgPath = 'indonesia.svg';
              if (file_exists($svgPath)) {
                  $svgContent = file_get_contents($svgPath);
                  // Remove XML declaration and Doctype to avoid validation issues
                  $svgContent = preg_replace('/^<\?xml[^>]*>/', '', $svgContent);
                  $svgContent = preg_replace('/^<!DOCTYPE[^>]*>/', '', $svgContent);
                  echo $svgContent;
              } else {
                  echo '<p class="text-red-500 text-center p-4">Peta tidak ditemukan. Pastikan file indonesia.svg ada di folder modules/geography.</p>';
              }
            ?>
          </div>
        </div>

        <div class="w-full lg:w-1/4">
          <div id="default-info" class="info-box text-center w-full">
            <p class="text-gray-500">Sentuh provinsi di peta untuk melihat informasi kenampakan alamnya!</p>
            <button id="start-quiz-btn" class="btn mt-4 w-full">ðŸ§  Mulai Kuis!</button>
          </div>
          <div id="province-info" class="info-box hidden w-full">
            <h3 id="info-title" class="text-lg font-bold text-emerald-800 border-b pb-2 mb-2"></h3>
            <p id="info-desc" class="text-sm text-gray-600"></p>
            <p id="info-feature" class="mt-3 font-medium text-emerald-700 bg-emerald-50 p-2 rounded"></p>
            <button id="quiz-btn" class="btn mt-4 w-full">ðŸ§  Uji Pengetahuanmu!</button>
          </div>
        </div>
      </div> <!-- ./flex -->
    </div> <!-- /#map-view -->

    <!-- Quiz View -->
    <div id="quiz-view" class="hidden flex flex-col items-center">
      <h2 class="text-2xl font-bold text-emerald-800 mb-6">Kuis: Kenampakan Alam Indonesia</h2>
      <div id="quiz-content" class="w-full max-w-2xl">
        <!-- Soal akan diisi via JS -->
      </div>
      <div id="result" class="hidden text-center mt-6 p-6 rounded-xl bg-white shadow max-w-lg w-full">
        <h3 class="text-2xl font-bold" id="score-text"></h3>
        <p id="feedback" class="mt-2 text-lg text-gray-700"></p>
        <button id="restart-btn" class="btn mt-6 w-full sm:w-auto">Ulangi Eksplorasi</button>
      </div>
    </div>

  </div>

  <script>
    // Data provinsi (disesuaikan dengan ID di indonesia.svg)
    const provinceData = {
      "Sumatera-Utara": { name: "Sumatra Utara", feature: "Danau Toba" },
      "Jawa-Barat": { name: "Jawa Barat", feature: "Gunung Tangkuban Perahu" },
      "Jawa-Tengah": { name: "Jawa Tengah", feature: "Candi Borobudur" },
      "Jawa-Timur": { name: "Jawa Timur", feature: "Gunung Bromo" },
      "Bali": { name: "Bali", feature: "Sawah Terasering Tegallalang" },
      "Kalimantan-Barat": { name: "Kalimantan Barat", feature: "Taman Nasional Danau Sentarum" },
      "Sulawesi-Selatan": { name: "Sulawesi Selatan", feature: "Taman Laut Takabonerate" },
      "Papua": { name: "Papua", feature: "Raja Ampat" },
      "Aceh": { name: "Aceh", feature: "Masjid Raya Baiturrahman" }, // Pengganti Jakarta
      "Daerah-Istimewa-Yogyakarta": { name: "DI Yogyakarta", feature: "Pantai Parangtritis" }
    };

    // Soal kuis (acak otomatis)
    const quizQuestions = [
      {
        question: "Provinsi mana yang memiliki Danau Toba?",
        correct: "Sumatera-Utara",
        options: ["Jawa-Barat", "Sumatera-Utara", "Jawa-Timur", "Bali"]
      },
      {
        question: "Gunung Bromo berada di provinsi...",
        correct: "Jawa-Timur",
        options: ["Jawa-Tengah", "Daerah-Istimewa-Yogyakarta", "Jawa-Timur", "Jawa-Barat"]
      },
      {
        question: "Destinasi bawah laut terkenal Raja Ampat ada di...",
        correct: "Papua",
        options: ["Sulawesi-Selatan", "Kalimantan-Barat", "Papua", "Jawa-Tengah"]
      },
      {
        question: "Di mana letak Candi Borobudur?",
        correct: "Jawa-Tengah",
        options: ["Jawa-Tengah", "Jawa-Timur", "Daerah-Istimewa-Yogyakarta", "Bali"]
      },
      {
        question: "Masjid Raya Baiturrahman adalah ikon dari provinsi...",
        correct: "Aceh",
        options: ["Sumatera-Utara", "Aceh", "Jawa-Barat", "Sulawesi-Selatan"]
      }
    ];

    // DOM
    const mapView = document.getElementById('map-view');
    const quizView = document.getElementById('quiz-view');
    const quizContent = document.getElementById('quiz-content');
    const resultBox = document.getElementById('result');

    // Event: Mulai kuis
    document.getElementById('start-quiz-btn')?.addEventListener('click', startQuiz);
    document.getElementById('quiz-btn')?.addEventListener('click', startQuiz);
    document.getElementById('restart-btn')?.addEventListener('click', () => {
      resultBox.classList.add('hidden');
      quizView.classList.add('hidden');
      mapView.classList.remove('hidden');
      resetMapHighlights();
    });

    // Reset highlight peta
    function resetMapHighlights() {
      document.querySelectorAll('#Indonesia-Map g[id]').forEach(el => {
        el.classList.remove('active');
      });
    }

    // Tampilkan info provinsi
    function showProvinceInfo(id) {
      const data = provinceData[id];
      if (!data) return; // Ignore clicks on unmapped provinces

      // Highlight active province
      resetMapHighlights();
      const el = document.getElementById(id);
      if(el) el.classList.add('active');

      document.getElementById('info-title').textContent = data.name;
      document.getElementById('info-desc').textContent = "Pelajari kenampakan alam unggulannya!";
      document.getElementById('info-feature').textContent = "ðŸ“ " + data.feature;
      document.getElementById('default-info').classList.add('hidden');
      document.getElementById('province-info').classList.remove('hidden');
    }

    // Inisialisasi Peta
    document.addEventListener('DOMContentLoaded', () => {
        // Ambil semua ID yang ada di data
        const mappedIds = Object.keys(provinceData);
        
        // Loop untuk menambahkan event listener
        mappedIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                // Tambahkan style pointer
                el.style.cursor = 'pointer';
                
                // Event Click
                el.addEventListener('click', () => showProvinceInfo(id));
                
                // Event Touch
                el.addEventListener('touchstart', (e) => { 
                    e.preventDefault(); // Mencegah scroll saat tap peta
                    showProvinceInfo(id); 
                });
            } else {
                console.warn(`Elemen dengan ID '${id}' tidak ditemukan di SVG.`);
            }
        });

        // Optional: Tambahkan tooltip sederhana atau hover effect tambahan via JS jika perlu
    });

    // === KUIS ===
    let currentQuestion = 0;
    let score = 0;

    function startQuiz() {
      currentQuestion = 0;
      score = 0;
      mapView.classList.add('hidden');
      quizView.classList.remove('hidden');
      resultBox.classList.add('hidden');
      loadQuestion();
    }

    function loadQuestion() {
      if (currentQuestion >= quizQuestions.length) {
        showResult();
        return;
      }

      const q = quizQuestions[currentQuestion];
      let html = `<div class="bg-white p-6 rounded-xl shadow mb-6">
        <h3 class="text-xl font-bold mb-4">Pertanyaan ${currentQuestion + 1} dari ${quizQuestions.length}</h3>
        <p class="mb-4 text-lg">${q.question}</p>
        <div id="options">`;

      // Acak opsi
      const shuffled = [...q.options].sort(() => Math.random() - 0.5);
      shuffled.forEach(opt => {
        const prov = provinceData[opt];
        const provName = prov ? prov.name : opt; // Fallback name
        html += `<button class="quiz-option" onclick="selectAnswer('${opt}', '${q.correct}')">
          ${provName}
        </button>`;
      });

      html += `</div></div>`;
      quizContent.innerHTML = html;
    }

    function selectAnswer(selected, correct) {
      const buttons = document.querySelectorAll('.quiz-option');
      buttons.forEach(btn => btn.disabled = true);

      // Cari button yang dipilih dan yang benar
      let selectedBtn, correctBtn;
      buttons.forEach(btn => {
         const provName = provinceData[selected] ? provinceData[selected].name : selected;
         const correctName = provinceData[correct] ? provinceData[correct].name : correct;
         
         if (btn.innerText.trim() === provName) selectedBtn = btn;
         if (btn.innerText.trim() === correctName) correctBtn = btn;
      });

      if (selected === correct) {
        score++;
        if(selectedBtn) {
            selectedBtn.style.background = '#dcfce7'; // Green-100
            selectedBtn.style.borderColor = '#16a34a'; // Green-600
        }
        setTimeout(() => {
          currentQuestion++;
          loadQuestion();
        }, 1000);
      } else {
        if(selectedBtn) {
            selectedBtn.style.background = '#fee2e2'; // Red-100
            selectedBtn.style.borderColor = '#ef4444'; // Red-500
        }
        if(correctBtn) {
            correctBtn.style.background = '#dcfce7'; // Green-100
            correctBtn.style.borderColor = '#16a34a'; // Green-600
        }
        setTimeout(() => {
          currentQuestion++;
          loadQuestion();
        }, 2000);
      }
    }

    function showResult() {
      quizContent.innerHTML = '';
      const percentage = Math.round((score / quizQuestions.length) * 100);
      document.getElementById('score-text').textContent = `Skor: ${score} dari ${quizQuestions.length}`;
      
      let feedback = "";
      if (percentage === 100) feedback = "ðŸŽ‰ Luar biasa! Kamu Juara Integritas Geografi!";
      else if (percentage >= 70) feedback = "ðŸ‘ Hebat! Kamu sudah mengenal Indonesia dengan baik.";
      else feedback = "ðŸ’¡ Terus belajar, ya! Setiap wilayah Indonesia punya keajaiban.";

      document.getElementById('feedback').textContent = feedback;
      resultBox.classList.remove('hidden');
    }
  </script>
</body>
</html>
