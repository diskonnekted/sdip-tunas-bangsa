<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Petualangan Penyerbukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            touch-action: none; /* Prevent scrolling while dragging */
            overflow: hidden;
            background: linear-gradient(to bottom, #87CEEB 0%, #E0F7FA 100%);
        }
        
        .game-container {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        /* Scenery Animations */
        @keyframes float {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(20px); }
        }
        
        @keyframes pulse-sun {
            0%, 100% { transform: scale(1); box-shadow: 0 0 40px rgba(253, 224, 71, 0.6); }
            50% { transform: scale(1.1); box-shadow: 0 0 60px rgba(253, 224, 71, 0.8); }
        }

        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-pulse-sun { animation: pulse-sun 4s ease-in-out infinite; }

        .flower {
            position: absolute;
            transition: transform 0.3s;
        }

        .flower-target {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            /* border: 2px dashed rgba(0,0,0,0.1); */
            border-radius: 50%;
        }

        .bee {
            position: absolute;
            z-index: 50;
            cursor: grab;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
            transition: transform 0.1s;
        }

        .bee:active {
            cursor: grabbing;
            transform: scale(1.1);
        }

        .pollen-particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #FFD700;
            border-radius: 50%;
            pointer-events: none;
            animation: fall 1s linear forwards;
        }

        @keyframes fall {
            to { transform: translateY(50px); opacity: 0; }
        }

        .fruit {
            transform: scale(0);
            transition: transform 1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .fruit.grown {
            transform: scale(1);
        }

        /* Quiz Overlay */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
    </style>
</head>
<body>

<div class="game-container" id="gameArea">
    <!-- UI Header -->
    <div class="absolute top-4 left-4 right-4 flex justify-between items-start pointer-events-none z-40">
        <div class="bg-white/90 backdrop-blur rounded-xl p-3 shadow-lg pointer-events-auto">
            <h1 class="text-lg font-bold text-primary-800">Petualangan Penyerbukan</h1>
            <p class="text-sm text-gray-600">Bantu lebah memindahkan serbuk sari!</p>
        </div>
        <div class="bg-white/90 backdrop-blur rounded-xl p-3 shadow-lg pointer-events-auto">
            <div class="text-center">
                <span class="text-xs text-gray-500 uppercase">Skor</span>
                <div class="text-2xl font-bold text-green-600" id="scoreDisplay">0</div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div id="tutorial" class="absolute inset-0 flex items-center justify-center z-50 bg-black/60">
        <div class="bg-white p-8 rounded-2xl max-w-md mx-4 text-center shadow-2xl animate-bounce-in">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-hand-pointer text-4xl text-yellow-600"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Cara Bermain</h2>
            <p class="text-gray-600 mb-6">
                1. Seret <i class="fas fa-bug text-yellow-600"></i> Lebah ke <span class="text-pink-500 font-bold">Bunga Jantan</span> untuk mengambil serbuk sari.<br>
                2. Bawa ke <span class="text-purple-500 font-bold">Bunga Betina</span> untuk membuahi.<br>
                3. Lihat buah tumbuh!
            </p>
            <button onclick="startGame()" class="px-8 py-3 bg-green-500 text-white rounded-full font-bold text-lg hover:bg-green-600 transition shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                Mulai Petualangan!
            </button>
        </div>
    </div>

    <!-- Game Elements (Positioned via JS or absolute CSS) -->
    
    <!-- Male Flower (Source) -->
    <div id="maleFlower" class="flower absolute bottom-20 left-[10%] flex flex-col items-center group">
        <div class="flower-target relative">
            <i class="fas fa-sun text-6xl text-pink-500 animate-pulse"></i>
            <div class="absolute -top-2 -right-2 bg-yellow-400 text-xs font-bold px-2 py-1 rounded-full text-yellow-900 border border-yellow-500">
                Jantan
            </div>
        </div>
        <div class="h-24 w-2 bg-green-600 mt-[-10px] rounded-full"></div>
        <div class="w-16 h-8 bg-green-500 rounded-full -mt-16 -ml-12 rotate-[-45deg]"></div>
    </div>

    <!-- Female Flower (Target) -->
    <div id="femaleFlower" class="flower absolute bottom-20 right-[10%] flex flex-col items-center">
        <div class="flower-target relative">
            <i class="fas fa-spa text-6xl text-purple-500"></i>
            <!-- Fruit (Hidden initially) -->
            <div id="fruit" class="fruit absolute inset-0 flex items-center justify-center bg-white/80 rounded-full hidden">
                <i class="fas fa-apple-alt text-5xl text-red-500 drop-shadow-lg"></i>
            </div>
            <div class="absolute -top-2 -right-2 bg-purple-400 text-xs font-bold px-2 py-1 rounded-full text-white border border-purple-500">
                Betina
            </div>
        </div>
        <div class="h-24 w-2 bg-green-600 mt-[-10px] rounded-full"></div>
        <div class="w-16 h-8 bg-green-500 rounded-full -mt-16 -mr-12 rotate-[45deg]"></div>
    </div>

    <!-- The Bee -->
    <div id="bee" class="bee absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 touch-none">
        <div class="relative">
            <i class="fas fa-bug text-5xl text-yellow-500"></i>
            <!-- Pollen Indicator -->
            <div id="pollenLoad" class="absolute -bottom-2 -right-2 w-4 h-4 bg-yellow-300 rounded-full border border-yellow-600 hidden animate-bounce"></div>
        </div>
    </div>

    <!-- Quiz Modal -->
    <div id="quizModal" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center">
        <div class="bg-white p-6 rounded-2xl max-w-sm w-full mx-4 shadow-2xl">
            <div class="text-center mb-4">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-question text-3xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Kuis Kilat!</h3>
                <p class="text-gray-600 mt-2">Apa yang terjadi pada tumbuhan jika tidak ada lebah atau penyerbuk?</p>
            </div>
            
            <div class="space-y-3">
                <button onclick="answerQuiz(false)" class="w-full p-3 text-left bg-gray-50 hover:bg-red-50 border border-gray-200 rounded-xl transition flex items-center group">
                    <div class="w-8 h-8 rounded-full bg-gray-200 group-hover:bg-red-200 flex items-center justify-center mr-3 font-bold text-gray-600">A</div>
                    <span class="text-sm font-medium text-gray-700">Tumbuhan akan tumbuh lebih cepat</span>
                </button>
                <button onclick="answerQuiz(true)" class="w-full p-3 text-left bg-gray-50 hover:bg-green-50 border border-gray-200 rounded-xl transition flex items-center group">
                    <div class="w-8 h-8 rounded-full bg-gray-200 group-hover:bg-green-200 flex items-center justify-center mr-3 font-bold text-gray-600">B</div>
                    <span class="text-sm font-medium text-gray-700">Tidak akan terbentuk buah dan biji baru</span>
                </button>
                <button onclick="answerQuiz(false)" class="w-full p-3 text-left bg-gray-50 hover:bg-red-50 border border-gray-200 rounded-xl transition flex items-center group">
                    <div class="w-8 h-8 rounded-full bg-gray-200 group-hover:bg-red-200 flex items-center justify-center mr-3 font-bold text-gray-600">C</div>
                    <span class="text-sm font-medium text-gray-700">Daun akan berubah warna menjadi biru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center">
        <div class="bg-white p-8 rounded-2xl max-w-sm mx-4 text-center shadow-2xl animate-bounce-in">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trophy text-4xl text-green-600"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-green-700">Luar Biasa!</h2>
            <p class="text-gray-600 mb-6">
                Kamu berhasil membantu penyerbukan! Buah apel telah tumbuh berkat bantuanmu.
            </p>
            <div class="flex space-x-3 justify-center">
                <button onclick="location.reload()" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-full font-semibold hover:bg-gray-200">
                    Main Lagi
                </button>
                <!-- In a real scenario, this would close the iframe or navigate back -->
            </div>
        </div>
    </div>
</div>

<script>
    const bee = document.getElementById('bee');
    const maleFlower = document.getElementById('maleFlower');
    const femaleFlower = document.getElementById('femaleFlower');
    const pollenLoad = document.getElementById('pollenLoad');
    const scoreDisplay = document.getElementById('scoreDisplay');
    const fruit = document.getElementById('fruit');
    
    let gameState = {
        hasPollen: false,
        score: 0,
        completed: false
    };

    // Initial Bee Position
    let beePos = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
    
    function updateBeePosition() {
        bee.style.left = beePos.x + 'px';
        bee.style.top = beePos.y + 'px';
    }

    function startGame() {
        document.getElementById('tutorial').style.display = 'none';
        
        // Reset position to Beehive (Start Point)
        // Hive is at left: 8 (approx 32px) + width/2, Top: 20% + height
        const hive = document.getElementById('beehive');
        const hiveRect = hive.getBoundingClientRect();
        
        beePos = { 
            x: hiveRect.left + hiveRect.width / 2, 
            y: hiveRect.top + hiveRect.height / 2 + 20 
        };
        
        updateBeePosition();
        
        // Add a "Fly out" animation hint
        bee.classList.add('transition-all', 'duration-1000');
        setTimeout(() => {
            beePos.x += 100;
            beePos.y += 50;
            updateBeePosition();
            setTimeout(() => {
                bee.classList.remove('transition-all', 'duration-1000');
            }, 1000);
        }, 100);
    }

    // Drag Logic
    let isDragging = false;
    let offset = { x: 0, y: 0 };

    function startDrag(e) {
        e.preventDefault(); // Prevent scrolling
        isDragging = true;
        
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        
        // Get current bee visual position (rect)
        const rect = bee.getBoundingClientRect();
        
        // Calculate offset from center of bee
        offset.x = clientX - (rect.left + rect.width/2);
        offset.y = clientY - (rect.top + rect.height/2);
        
        bee.style.transition = 'none';
    }

    function drag(e) {
        if (!isDragging) return;
        e.preventDefault();

        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;

        beePos.x = clientX - offset.x;
        beePos.y = clientY - offset.y;

        // Boundary checks
        beePos.x = Math.max(20, Math.min(window.innerWidth - 20, beePos.x));
        beePos.y = Math.max(20, Math.min(window.innerHeight - 20, beePos.y));

        updateBeePosition();
        checkCollisions();
    }

    function endDrag() {
        isDragging = false;
        bee.style.transition = 'transform 0.1s, top 0.1s, left 0.1s';
    }

    // Event Listeners
    bee.addEventListener('mousedown', startDrag);
    bee.addEventListener('touchstart', startDrag);

    window.addEventListener('mousemove', drag);
    window.addEventListener('touchmove', drag, { passive: false });

    window.addEventListener('mouseup', endDrag);
    window.addEventListener('touchend', endDrag);

    function checkCollisions() {
        if (gameState.completed) return;

        const beeRect = bee.getBoundingClientRect();
        const maleRect = maleFlower.querySelector('.flower-target').getBoundingClientRect();
        const femaleRect = femaleFlower.querySelector('.flower-target').getBoundingClientRect();

        // Check collision with Male Flower (Get Pollen)
        if (!gameState.hasPollen && isColliding(beeRect, maleRect)) {
            gameState.hasPollen = true;
            pollenLoad.classList.remove('hidden');
            createParticles(beePos.x, beePos.y, '#FFD700');
            showFloatingText(beePos.x, beePos.y, 'Serbuk Sari Diambil!');
            playSound('pop');
        }

        // Check collision with Female Flower (Deliver Pollen)
        if (gameState.hasPollen && isColliding(beeRect, femaleRect)) {
            completeLevel();
        }
    }

    function isColliding(rect1, rect2) {
        return !(rect1.right < rect2.left || 
                 rect1.left > rect2.right || 
                 rect1.bottom < rect2.top || 
                 rect1.top > rect2.bottom);
    }

    function createParticles(x, y, color) {
        for (let i = 0; i < 5; i++) {
            const p = document.createElement('div');
            p.className = 'pollen-particle';
            p.style.left = (x + (Math.random() - 0.5) * 40) + 'px';
            p.style.top = (y + (Math.random() - 0.5) * 40) + 'px';
            p.style.backgroundColor = color;
            document.body.appendChild(p);
            setTimeout(() => p.remove(), 1000);
        }
    }

    function showFloatingText(x, y, text) {
        const el = document.createElement('div');
        el.className = 'absolute text-yellow-600 font-bold text-sm animate-bounce';
        el.style.left = x + 'px';
        el.style.top = (y - 50) + 'px';
        el.innerText = text;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 1000);
    }

    function completeLevel() {
        gameState.completed = true;
        gameState.hasPollen = false;
        pollenLoad.classList.add('hidden');
        
        // Fruit animation
        fruit.classList.remove('hidden');
        setTimeout(() => fruit.classList.add('grown'), 100);
        
        gameState.score += 50;
        scoreDisplay.innerText = gameState.score;
        
        createParticles(femaleFlower.offsetLeft + 50, femaleFlower.offsetTop, '#FF69B4');
        
        setTimeout(() => {
            document.getElementById('quizModal').classList.remove('hidden');
        }, 1500);
    }

    function answerQuiz(isCorrect) {
        document.getElementById('quizModal').classList.add('hidden');
        if (isCorrect) {
            gameState.score += 50;
            scoreDisplay.innerText = gameState.score;
            document.getElementById('successModal').classList.remove('hidden');
        } else {
            alert('Kurang tepat, coba lagi ya! Ingat, lebah membantu penyerbukan.');
            document.getElementById('quizModal').classList.remove('hidden'); // Show again
        }
    }
    
    // Mock sound function
    function playSound(type) {
        // In a real app, play audio here
    }

</script>
</body>
</html>