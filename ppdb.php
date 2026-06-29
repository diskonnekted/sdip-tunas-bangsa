<?php
require_once 'includes/settings.php';
require_once 'admin/config/database.php';

$page_title = 'PPDB Online';
$body_class = 'bg-slate-50 font-jakarta';
$nav_theme = 'light';

// Custom Tailwind Config
$extra_head = <<<EOT
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'jakarta': ['Plus Jakarta Sans', 'sans-serif'],
                        'inter': ['Inter', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e', // Sky blue
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e', // Sky blue
                            600: '#16a34a',
                            700: '#15803d',
                            900: '#14532d',
                        },
                        accent: {
                            500: '#f59e0b', // Amber
                            600: '#d97706',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.5s ease-out',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
EOT;

$registration_data = [];
$success_message = false;
$error_message = '';

include 'includes/header.php';
?>

<!-- Custom CSS for Wizard -->
<style>
    .step-active {
        @apply bg-brand-600 text-white border-brand-600;
    }
    .step-completed {
        @apply bg-green-500 text-white border-green-500;
    }
    .step-inactive {
        @apply bg-white text-slate-400 border-slate-200;
    }
    
    /* Progress Bar Animation */
    .progress-bar {
        transition: width 0.5s ease-in-out;
    }

    /* Fade Animation for Steps */
    .step-content {
        animation: fadeIn 0.5s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="min-h-screen pt-24 pb-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        
        <!-- Success State -->
        <div id="success-state" class="bg-white rounded-3xl shadow-xl p-8 text-center animate-fade-in-up hidden">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-4xl text-green-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-slate-900 mb-4">Pendaftaran Berhasil!</h2>
            <p class="text-lg text-slate-600 mb-8">
                Terima kasih telah mendaftar. Nomor pendaftaran Anda adalah:
                <br>
                <span id="success-reg-number" class="text-2xl font-mono font-bold text-brand-600 block mt-2"></span>
            </p>
            
            <div class="bg-green-50 border border-green-100 rounded-2xl p-6 mb-8 text-left">
                <div class="flex items-start">
                    <i class="fab fa-whatsapp text-2xl text-green-500 mt-1 mr-4"></i>
                    <div>
                        <h4 class="font-bold text-slate-900 mb-1">Informasi Selanjutnya</h4>
                        <p class="text-slate-600 text-sm">
                            Informasi status pendaftaran dan jadwal seleksi akan kami kirimkan melalui WhatsApp ke nomor orang tua: 
                            <span id="success-phone" class="font-semibold text-slate-900"></span>.
                            Pastikan nomor tersebut aktif.
                        </p>
                    </div>
                </div>
            </div>

            <a href="index.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-brand-600 hover:bg-brand-700 transition-all shadow-lg hover:shadow-brand-500/30">
                <i class="fas fa-home mr-2"></i> Kembali ke Beranda
            </a>
        </div>

        <!-- Wizard Header -->
        <div id="wizard-header" class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Penerimaan Peserta Didik Baru</h1>
            <p class="text-slate-600">Silakan lengkapi formulir di bawah ini untuk mendaftarkan putra/putri Anda.</p>
        </div>

        <!-- Wizard Container -->
        <div id="wizard-container" class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
            <!-- Progress Steps -->
            <div class="bg-slate-50 px-8 py-6 border-b border-slate-100">
                <div class="relative">
                    <!-- Progress Line Background -->
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-200 -translate-y-1/2 rounded-full z-0"></div>
                    <!-- Active Progress Line -->
                    <div id="progress-line" class="absolute top-1/2 left-0 h-1 bg-brand-600 -translate-y-1/2 rounded-full z-0 progress-bar" style="width: 0%;"></div>

                    <div class="relative z-10 flex justify-between">
                        <!-- Step 1 Indicator -->
                        <div class="flex flex-col items-center cursor-pointer" onclick="goToStep(1)">
                            <div id="step-indicator-1" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 bg-brand-600 text-white border-2 border-brand-600 shadow-lg shadow-brand-500/30">1</div>
                            <span class="text-xs font-semibold mt-2 text-slate-700">Data Anak</span>
                        </div>
                        
                        <!-- Step 2 Indicator -->
                        <div class="flex flex-col items-center cursor-pointer" onclick="goToStep(2)">
                            <div id="step-indicator-2" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 bg-white text-slate-400 border-2 border-slate-200">2</div>
                            <span class="text-xs font-semibold mt-2 text-slate-400">Data Orang Tua</span>
                        </div>
                        
                        <!-- Step 3 Indicator -->
                        <div class="flex flex-col items-center cursor-pointer" onclick="goToStep(3)">
                            <div id="step-indicator-3" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 bg-white text-slate-400 border-2 border-slate-200">3</div>
                            <span class="text-xs font-semibold mt-2 text-slate-400">Konfirmasi</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form id="ppdbForm" method="POST" action="" class="p-8 md:p-10">
                
                <?php if ($error_message): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>

                <!-- Step 1: Data Anak -->
                <div id="step-1" class="step-content">
                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                        <i class="fas fa-child text-brand-500 mr-3"></i> Identitas Anak
                    </h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap Anak <span class="text-red-500">*</span></label>
                            <input type="text" name="child_name" id="child_name" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="Masukkan nama lengkap sesuai akta kelahiran">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Lahir <span class="text-red-500">*</span></label>
                            <input type="date" name="dob" id="dob" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <select name="gender" id="gender" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Asal Sekolah</label>
                            <input type="text" name="previous_school" id="previous_school" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="Masukkan asal sekolah (opsional)">
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="button" onclick="nextStep(2)" class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-brand-500/30 flex items-center">
                            Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Data Orang Tua -->
                <div id="step-2" class="step-content hidden">
                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                        <i class="fas fa-user-friends text-brand-500 mr-3"></i> Identitas Orang Tua / Wali
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap Orang Tua <span class="text-red-500">*</span></label>
                            <input type="text" name="parent_name" id="parent_name" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="Nama Ayah/Ibu">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="contoh@email.com">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                            <input type="tel" name="parent_phone" id="parent_phone" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="08xxxxxxxxxx">
                            <p class="text-xs text-amber-600 mt-1"><i class="fas fa-info-circle"></i> Pastikan nomor aktif dan terhubung WhatsApp.</p>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <textarea name="address" id="address" required rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring focus:ring-brand-200 transition-all outline-none" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota"></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" onclick="prevStep(1)" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </button>
                        <button type="button" onclick="nextStep(3)" class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-brand-500/30 flex items-center">
                            Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Review & Submit -->
                <div id="step-3" class="step-content hidden">
                    <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                        <i class="fas fa-clipboard-check text-brand-500 mr-3"></i> Konfirmasi Data
                    </h3>

                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200 space-y-4 mb-6">
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-slate-500">Nama Anak</div>
                            <div class="col-span-2 font-semibold text-slate-900" id="review_child_name">-</div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-slate-500">Tanggal Lahir</div>
                            <div class="col-span-2 font-semibold text-slate-900" id="review_dob">-</div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-slate-500">Jenis Kelamin</div>
                            <div class="col-span-2 font-semibold text-slate-900" id="review_gender">-</div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-slate-500">Asal Sekolah</div>
                            <div class="col-span-2 font-semibold text-slate-900" id="review_previous_school">-</div>
                        </div>
                        <div class="border-t border-slate-200 my-2"></div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-slate-500">Orang Tua</div>
                            <div class="col-span-2 font-semibold text-slate-900" id="review_parent_name">-</div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-slate-500">Email</div>
                            <div class="col-span-2 font-semibold text-slate-900" id="review_email">-</div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-slate-500">No. Telp/WA</div>
                            <div class="col-span-2 font-semibold text-slate-900" id="review_phone">-</div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div class="text-slate-500">Alamat</div>
                            <div class="col-span-2 font-semibold text-slate-900" id="review_address">-</div>
                        </div>
                    </div>

                    <div class="flex items-start mb-6">
                        <div class="flex items-center h-5">
                            <input id="terms" type="checkbox" required class="w-4 h-4 text-brand-600 border-slate-300 rounded focus:ring-brand-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-slate-700">Saya menyatakan bahwa data yang diisi adalah benar dan dapat dipertanggungjawabkan.</label>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-between">
                        <button type="button" onclick="prevStep(2)" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                        </button>
                        <button type="submit" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-green-500/30 flex items-center transform hover:-translate-y-1">
                            Kirim Pendaftaran <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 3;

    function updateUI() {
        // Hide all steps
        for(let i = 1; i <= totalSteps; i++) {
            document.getElementById(`step-${i}`).classList.add('hidden');
            
            // Reset indicators
            const indicator = document.getElementById(`step-indicator-${i}`);
            indicator.className = "w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 border-2";
            
            if (i < currentStep) {
                // Completed
                indicator.classList.add('bg-green-500', 'text-white', 'border-green-500');
                indicator.innerHTML = '<i class="fas fa-check"></i>';
            } else if (i === currentStep) {
                // Active
                indicator.classList.add('bg-brand-600', 'text-white', 'border-brand-600', 'shadow-lg', 'shadow-brand-500/30');
                indicator.innerHTML = i;
            } else {
                // Inactive
                indicator.classList.add('bg-white', 'text-slate-400', 'border-slate-200');
                indicator.innerHTML = i;
            }
        }
        
        // Show current step
        document.getElementById(`step-${currentStep}`).classList.remove('hidden');
        
        // Update progress bar
        const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
        document.getElementById('progress-line').style.width = `${progress}%`;

        // Scroll to top of wizard
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateStep(step) {
        let valid = true;
        const currentStepEl = document.getElementById(`step-${step}`);
        const inputs = currentStepEl.querySelectorAll('input[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.classList.add('border-red-500', 'ring', 'ring-red-200');
            } else {
                input.classList.remove('border-red-500', 'ring', 'ring-red-200');
            }
        });
        
        return valid;
    }

    function nextStep(step) {
        if (validateStep(step - 1)) {
            // Update review data if going to last step
            if (step === 3) {
                document.getElementById('review_child_name').textContent = document.getElementById('child_name').value;
                document.getElementById('review_dob').textContent = document.getElementById('dob').value;
                
                const gender = document.getElementById('gender').value;
                document.getElementById('review_gender').textContent = gender === 'L' ? 'Laki-laki' : 'Perempuan';
                
                const prevSchool = document.getElementById('previous_school').value;
                document.getElementById('review_previous_school').textContent = prevSchool ? prevSchool : '-';

                document.getElementById('review_parent_name').textContent = document.getElementById('parent_name').value;
                document.getElementById('review_email').textContent = document.getElementById('email').value;
                document.getElementById('review_phone').textContent = document.getElementById('parent_phone').value;
                document.getElementById('review_address').textContent = document.getElementById('address').value;
            }
            
            currentStep = step;
            updateUI();
        }
    }

    function prevStep(step) {
        currentStep = step;
        updateUI();
    }
    
    function goToStep(step) {
        if (step < currentStep) {
            currentStep = step;
            updateUI();
        }
    }

    // Initialize
    updateUI();

    // Handle Form Submission
    document.getElementById('ppdbForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengirim...';
        
        try {
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            const response = await fetch('api/ppdb_register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Hide wizard and show success message
                document.getElementById('wizard-header').classList.add('hidden');
                document.getElementById('wizard-container').classList.add('hidden');
                
                const successState = document.getElementById('success-state');
                successState.classList.remove('hidden');
                
                document.getElementById('success-reg-number').textContent = result.data.registration_number;
                document.getElementById('success-phone').textContent = result.data.parent_phone;
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                throw new Error(result.message || 'Terjadi kesalahan saat mengirim data');
            }
            
        } catch (error) {
            alert(error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
</script>

<?php include 'includes/footer.php'; ?>