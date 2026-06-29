<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perubahan Apa Ini?</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            touch-action: none;
            overflow: hidden;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card-stack {
            perspective: 1000px;
        }

        .card {
            position: absolute;
            width: 300px;
            height: 400px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease, opacity 0.3s ease;
            cursor: grab;
            user-select: none;
            left: 50%;
            top: 50%;
            margin-left: -150px; /* Half of width */
            margin-top: -200px; /* Half of height */
        }

        .card:active {
            cursor: grabbing;
        }

        .drop-zone {
            transition: all 0.3s ease;
        }

        .drop-zone.drag-over {
            transform: scale(1.05);
            border-width: 4px;
        }

        /* Animations for card content */
        @keyframes melt {
            0% { transform: scaleY(1); }
            100% { transform: scaleY(0.6) translateY(20px); opacity: 0.7; }
        }
        .anim-melt { animation: melt 3s infinite alternate; }

        @keyframes burn {
            0% { color: #8B4513; transform: scale(1); }
            50% { color: #FF4500; transform: scale(1.1) rotate(2deg); }
            100% { color: #333; transform: scale(0.9); }
        }
        .anim-burn { animation: burn 2s infinite; }

        @keyframes fold {
            0% { transform: skewX(0); }
            50% { transform: skewX(20deg) scaleX(0.8); }
            100% { transform: skewX(0); }
        }
        .anim-fold { animation: fold 3s infinite; }

        @keyframes sour {
            0% { color: #fff; transform: rotate(0); }
            25% { color: #e2e8f0; transform: rotate(5deg); }
            50% { color: #84cc16; transform: rotate(0); } /* Greenish */
            75% { color: #65a30d; transform: rotate(-5deg); }
            100% { color: #3f6212; transform: rotate(0); }
        }
        .anim-sour { animation: sour 4s infinite; }

        .zone-physics { background-color: rgba(59, 130, 246, 0.1); border-color: #3b82f6; }
        .zone-chemistry { background-color: rgba(239, 68, 68, 0.1); border-color: #ef4444; }
    </style>
</head>
<body class="h-screen w-full overflow-hidden flex flex-col">

    <!-- Header -->
    <div class="w-full p-4 flex justify-between items-center bg-white/80 backdrop-blur shadow-sm z-10">
        <div class="text-xl font-bold text-gray-800">
            <i class="fas fa-flask text-purple-600 mr-2"></i>Perubahan Apa Ini?
        </div>
        <div class="flex items-center space-x-4">
            <div class="bg-green-100 text-green-800 px-4 py-1 rounded-full font-bold">
                Skor: <span id="score">0</span>
            </div>
            <button onclick="restartGame()" class="text-gray-500 hover:text-gray-800">
                <i class="fas fa-redo"></i>
            </button>
        </div>
    </div>

    <!-- Game Area -->
    <div class="flex-1 relative flex overflow-hidden" id="gameArea">
        
        <!-- Left Drop Zone: Fisika -->
        <div id="zone-physics" class="w-1/3 h-full flex flex-col items-center justify-center border-r-4 border-dashed border-transparent transition-all duration-300 zone-physics group">
            <div class="text-6xl text-green-500 mb-4 opacity-50 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-snowflake"></i>
            </div>
            <h2 class="text-2xl font-bold text-green-700 text-center">Perubahan<br>FISIKA</h2>
            <p class="text-sm text-green-600/70 text-center mt-2 px-4">Zat tetap sama,<br>hanya wujud berubah</p>
        </div>

        <!-- Center: Card Stack Area -->
        <div class="w-1/3 h-full relative flex items-center justify-center" id="cardContainer">
            <!-- Cards will be injected here -->
            <div id="startMessage" class="text-center p-6 bg-white/90 rounded-xl shadow-lg">
                <h3 class="text-xl font-bold mb-2">Siap Bermain?</h3>
                <p class="mb-4 text-gray-600">Geser kartu ke kiri untuk Fisika, ke kanan untuk Kimia.</p>
                <button onclick="startGame()" class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">Mulai</button>
            </div>
        </div>

        <!-- Right Drop Zone: Kimia -->
        <div id="zone-chemistry" class="w-1/3 h-full flex flex-col items-center justify-center border-l-4 border-dashed border-transparent transition-all duration-300 zone-chemistry group">
            <div class="text-6xl text-red-500 mb-4 opacity-50 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-fire"></i>
            </div>
            <h2 class="text-2xl font-bold text-red-700 text-center">Perubahan<br>KIMIA</h2>
            <p class="text-sm text-red-600/70 text-center mt-2 px-4">Terbentuk zat baru,<br>tidak bisa kembali</p>
        </div>

    </div>

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl transform transition-all scale-100">
            <div id="feedbackIcon" class="text-5xl text-center mb-4"></div>
            <h3 id="feedbackTitle" class="text-2xl font-bold text-center mb-2"></h3>
            <p id="feedbackText" class="text-center text-gray-600 mb-6"></p>
            <button onclick="closeFeedback()" class="w-full py-3 rounded-xl font-bold text-white transition-colors" id="feedbackButton">Lanjut</button>
        </div>
    </div>

    <!-- Game Over Modal -->
    <div id="gameOverModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden backdrop-blur-sm">
        <div class="bg-white rounded-2xl p-8 max-w-sm w-full mx-4 shadow-2xl text-center">
            <i class="fas fa-trophy text-6xl text-yellow-400 mb-4"></i>
            <h2 class="text-3xl font-bold mb-2">Permainan Selesai!</h2>
            <p class="text-gray-600 mb-6">Skor Akhir Kamu:</p>
            <div class="text-5xl font-bold text-purple-600 mb-8" id="finalScore">0</div>
            <button onclick="restartGame()" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-xl font-bold transition">Main Lagi</button>
        </div>
    </div>

    <script>
        // Game Data
        const questions = [
            {
                id: 1,
                title: "Es Mencair",
                type: "physics",
                icon: "fa-ice-cream",
                animClass: "anim-melt",
                color: "text-green-400",
                explanation: "Es mencair hanya perubahan wujud dari padat ke cair. Airnya tetap air (H2O)!"
            },
            {
                id: 2,
                title: "Kayu Terbakar",
                type: "chemistry",
                icon: "fa-tree",
                animClass: "anim-burn",
                color: "text-amber-700",
                explanation: "Pembakaran kayu menghasilkan zat baru (abu & asap). Tidak bisa jadi kayu lagi!"
            },
            {
                id: 3,
                title: "Susu Basi",
                type: "chemistry",
                icon: "fa-prescription-bottle", // Representation of milk bottle
                animClass: "anim-sour",
                color: "text-gray-400",
                explanation: "Bakteri mengubah rasa dan bau susu. Ini reaksi kimia fermentasi!"
            },
            {
                id: 4,
                title: "Kertas Dilipat",
                type: "physics",
                icon: "fa-scroll",
                animClass: "anim-fold",
                color: "text-yellow-600",
                explanation: "Kertas hanya berubah bentuk. Zat penyusunnya tetap kertas!"
            },
            {
                id: 5,
                title: "Besi Berkarat",
                type: "chemistry",
                icon: "fa-wrench",
                animClass: "",
                color: "text-orange-600",
                explanation: "Besi bereaksi dengan oksigen membentuk karat (zat baru)."
            },
            {
                id: 6,
                title: "Gula Larut",
                type: "physics",
                icon: "fa-cube",
                animClass: "anim-melt",
                color: "text-white bg-green-300 rounded p-2",
                explanation: "Gula hanya terlarut dalam air, rasanya tetap manis. Bisa dipisahkan lagi!"
            }
        ];

        let currentCardIndex = 0;
        let score = 0;
        let currentCard = null;
        let isDragging = false;
        let startX, startY, currentX, currentY;
        let gameActive = false;

        // Audio Context for synthesized sounds
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

        function playSound(type) {
            if (audioCtx.state === 'suspended') audioCtx.resume();
            
            const osc = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            
            osc.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            
            if (type === 'correct') {
                // Applause-ish / Happy sound (Major Arpeggio)
                const now = audioCtx.currentTime;
                
                // Note 1
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(523.25, now); // C5
                gain1.gain.setValueAtTime(0.1, now);
                gain1.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.start(now);
                osc1.stop(now + 0.5);

                // Note 2
                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(659.25, now + 0.1); // E5
                gain2.gain.setValueAtTime(0.1, now + 0.1);
                gain2.gain.exponentialRampToValueAtTime(0.01, now + 0.6);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);
                osc2.start(now + 0.1);
                osc2.stop(now + 0.6);
                
            } else {
                // Wrong sound (Low buzz)
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(150, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(100, audioCtx.currentTime + 0.3);
                
                gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.3);
                
                osc.start();
                osc.stop(audioCtx.currentTime + 0.3);
            }
        }

        function initGame() {
            document.getElementById('startMessage').style.display = 'block';
            document.getElementById('gameOverModal').classList.add('hidden');
        }

        function startGame() {
            gameActive = true;
            score = 0;
            currentCardIndex = 0;
            updateScore();
            document.getElementById('startMessage').style.display = 'none';
            // Shuffle questions
            questions.sort(() => Math.random() - 0.5);
            loadCard();
        }

        function restartGame() {
            startGame();
            document.getElementById('gameOverModal').classList.add('hidden');
        }

        function updateScore() {
            document.getElementById('score').innerText = score;
        }

        function loadCard() {
            if (currentCardIndex >= questions.length) {
                endGame();
                return;
            }

            const data = questions[currentCardIndex];
            const container = document.getElementById('cardContainer');
            
            // Create card element
            const card = document.createElement('div');
            card.className = 'card shadow-xl border border-gray-100';
            card.innerHTML = `
                <div class="flex-1 flex items-center justify-center w-full bg-gray-50 rounded-t-2xl">
                    <i class="fas ${data.icon} text-9xl ${data.color} ${data.animClass}"></i>
                </div>
                <div class="h-1/3 w-full bg-white flex flex-col items-center justify-center rounded-b-2xl border-t border-gray-100 p-4">
                    <h3 class="text-2xl font-bold text-gray-800 text-center">${data.title}</h3>
                    <p class="text-sm text-gray-400 mt-2">Geser ke Kiri (Fisika) atau Kanan (Kimia)</p>
                </div>
            `;

            container.appendChild(card);
            currentCard = card;

            // Add Event Listeners
            card.addEventListener('mousedown', startDrag);
            card.addEventListener('touchstart', startDrag, {passive: false});
        }

        function startDrag(e) {
            if (!gameActive) return;
            isDragging = true;
            
            if (e.type === 'touchstart') {
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
            } else {
                startX = e.clientX;
                startY = e.clientY;
            }

            document.addEventListener('mousemove', drag);
            document.addEventListener('touchmove', drag, {passive: false});
            document.addEventListener('mouseup', endDrag);
            document.addEventListener('touchend', endDrag);
        }

        function drag(e) {
            if (!isDragging) return;
            e.preventDefault();

            let clientX, clientY;
            if (e.type === 'touchmove') {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }

            const deltaX = clientX - startX;
            const deltaY = clientY - startY;
            const rotate = deltaX * 0.1; // Rotate while dragging

            currentCard.style.transform = `translate(${deltaX}px, ${deltaY}px) rotate(${rotate}deg)`;
            
            // Highlight zones
            const zonePhysics = document.getElementById('zone-physics');
            const zoneChemistry = document.getElementById('zone-chemistry');
            
            if (deltaX < -50) {
                zonePhysics.classList.add('bg-green-100', 'border-green-400');
                zoneChemistry.classList.remove('bg-red-100', 'border-red-400');
            } else if (deltaX > 50) {
                zoneChemistry.classList.add('bg-red-100', 'border-red-400');
                zonePhysics.classList.remove('bg-green-100', 'border-green-400');
            } else {
                zonePhysics.classList.remove('bg-green-100', 'border-green-400');
                zoneChemistry.classList.remove('bg-red-100', 'border-red-400');
            }
        }

        function endDrag(e) {
            if (!isDragging) return;
            isDragging = false;
            
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('touchmove', drag);
            document.removeEventListener('mouseup', endDrag);
            document.removeEventListener('touchend', endDrag);

            // Calculate final position logic
            const transform = currentCard.style.transform;
            const match = transform.match(/translate\(([-\d.]+)px/);
            const deltaX = match ? parseFloat(match[1]) : 0;
            const threshold = 100; // Drag threshold

            // Reset Zones
            document.getElementById('zone-physics').classList.remove('bg-green-100', 'border-green-400');
            document.getElementById('zone-chemistry').classList.remove('bg-red-100', 'border-red-400');

            if (deltaX < -threshold) {
                // Dropped Left (Physics)
                handleAnswer('physics');
            } else if (deltaX > threshold) {
                // Dropped Right (Chemistry)
                handleAnswer('chemistry');
            } else {
                // Return to center
                currentCard.style.transform = 'translate(0, 0) rotate(0)';
            }
        }

        function handleAnswer(choice) {
            const data = questions[currentCardIndex];
            const isCorrect = choice === data.type;

            if (isCorrect) {
                score += 10;
                updateScore();
                playSound('correct');
                
                // Fly card away
                const direction = choice === 'physics' ? -1 : 1;
                currentCard.style.transition = 'transform 0.5s ease-in';
                currentCard.style.transform = `translate(${direction * 1000}px, 0) rotate(${direction * 45}deg)`;
                
                setTimeout(() => {
                    currentCard.remove();
                    currentCardIndex++;
                    loadCard();
                }, 300);

            } else {
                playSound('wrong');
                // Return card to center first
                currentCard.style.transform = 'translate(0, 0)';
                
                // Show Explanation
                showFeedback(false, data);
            }
        }

        function showFeedback(isCorrect, data) {
            const modal = document.getElementById('feedbackModal');
            const title = document.getElementById('feedbackTitle');
            const text = document.getElementById('feedbackText');
            const icon = document.getElementById('feedbackIcon');
            const btn = document.getElementById('feedbackButton');

            modal.classList.remove('hidden');

            if (!isCorrect) {
                icon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
                title.textContent = "Kurang Tepat!";
                title.className = "text-2xl font-bold text-center mb-2 text-red-600";
                text.textContent = data.explanation;
                btn.className = "w-full py-3 rounded-xl font-bold text-white transition-colors bg-red-500 hover:bg-red-600";
            }
        }

        function closeFeedback() {
            document.getElementById('feedbackModal').classList.add('hidden');
            // Move to next card anyway after wrong answer? Or retry? 
            // Prompt says: "Jika salah -> penjelasan muncul... Skor ditampilkan di akhir". 
            // Usually in quizzes, you move on. Let's move on.
            currentCard.remove();
            currentCardIndex++;
            loadCard();
        }

        function endGame() {
            gameActive = false;
            document.getElementById('finalScore').textContent = score;
            document.getElementById('gameOverModal').classList.remove('hidden');
        }

        // Init
        // Don't auto start, wait for user click
    </script>
</body>
</html>
