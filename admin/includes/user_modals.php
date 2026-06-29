<!-- Create User Modal -->
<div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-xl md:max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="bg-green-600 px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold text-white">Tambah User Baru</h3>
            </div>
            <form method="POST" class="p-6" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create">
                
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                           pattern="[a-zA-Z0-9_]{3,50}"
                           title="Username harus 3-50 karakter (huruf, angka, underscore)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required minlength="6"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                </div>
                
                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select id="role" name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="demo">Demo</option>
                        <option value="guru">Guru</option>
                        <option value="staf">Staf</option>
                        <option value="orang_tua">Wali Murid / Orang Tua</option>
                        <option value="admin">Admin</option>
                        <?php if (Auth::getUserRole() === 'super_admin'): ?>
                        <option value="super_admin">Super Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran / Jabatan</label>
                    <input type="text" id="subject" name="subject"
                           placeholder="Contoh: Matematika, Bahasa Indonesia, Kepala Tata Usaha"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-4">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Max 2MB.</p>
                </div>

                <div class="mb-4">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Biografi Singkat</label>
                    <textarea id="bio" name="bio" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label for="education" class="block text-sm font-medium text-gray-700 mb-2">Info Kelulusan (Opsional)</label>
                    <textarea id="education" name="education" rows="3"
                              placeholder="Riwayat pendidikan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label for="achievements" class="block text-sm font-medium text-gray-700 mb-2">Prestasi (Opsional)</label>
                    <textarea id="achievements" name="achievements" rows="3"
                              placeholder="Prestasi yang pernah dicapai..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label for="certificates" class="block text-sm font-medium text-gray-700 mb-2">Sertifikat Kompetensi (Opsional)</label>
                    <textarea id="certificates" name="certificates" rows="3"
                              placeholder="Sertifikat yang dimiliki..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label for="training" class="block text-sm font-medium text-gray-700 mb-2">Pelatihan/Diklat (Opsional)</label>
                    <textarea id="training" name="training" rows="3"
                              placeholder="Pelatihan yang pernah diikuti..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 sticky bottom-0 bg-white pt-4">
                    <button type="button" onclick="closeCreateModal()" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-xl md:max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="bg-green-600 px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold text-white">Edit User</h3>
            </div>
            <form method="POST" class="p-6" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="edit_user_id" name="user_id">
                
                <div class="mb-4">
                    <label for="edit_username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" id="edit_username" name="username" required
                           pattern="[a-zA-Z0-9_]{3,50}"
                           title="Username harus 3-50 karakter (huruf, angka, underscore)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="edit_email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label for="edit_full_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" id="edit_full_name" name="full_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="mb-4">
                    <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select id="edit_role" name="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="demo">Demo</option>
                        <option value="guru">Guru</option>
                        <option value="staf">Staf</option>
                        <option value="orang_tua">Wali Murid / Orang Tua</option>
                        <option value="admin">Admin</option>
                        <?php if (Auth::getUserRole() === 'super_admin'): ?>
                        <option value="super_admin">Super Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="edit_subject" class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran / Jabatan</label>
                    <input type="text" id="edit_subject" name="subject"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="mb-4">
                    <label for="edit_photo" class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                    <input type="file" id="edit_photo" name="photo" accept="image/jpeg,image/png,image/jpg"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah foto. Max 2MB.</p>
                </div>

                <div class="mb-4">
                    <label for="edit_bio" class="block text-sm font-medium text-gray-700 mb-2">Biografi Singkat</label>
                    <textarea id="edit_bio" name="bio" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label for="edit_education" class="block text-sm font-medium text-gray-700 mb-2">Info Kelulusan (Opsional)</label>
                    <textarea id="edit_education" name="education" rows="3"
                              placeholder="Riwayat pendidikan..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label for="edit_achievements" class="block text-sm font-medium text-gray-700 mb-2">Prestasi (Opsional)</label>
                    <textarea id="edit_achievements" name="achievements" rows="3"
                              placeholder="Prestasi yang pernah dicapai..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label for="edit_certificates" class="block text-sm font-medium text-gray-700 mb-2">Sertifikat Kompetensi (Opsional)</label>
                    <textarea id="edit_certificates" name="certificates" rows="3"
                              placeholder="Sertifikat yang dimiliki..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-4">
                    <label for="edit_training" class="block text-sm font-medium text-gray-700 mb-2">Pelatihan/Diklat (Opsional)</label>
                    <textarea id="edit_training" name="training" rows="3"
                              placeholder="Pelatihan yang pernah diikuti..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="mb-6">
                    <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="edit_status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
                
                <div class="flex justify-end space-x-3 sticky bottom-0 bg-white pt-4">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Info User Modal -->
<div id="infoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-green-500 px-6 py-4 rounded-t-lg flex justify-between items-center">
                <h3 class="text-lg font-semibold text-white">Detail User</h3>
                <button onclick="closeInfoModal()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Info -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Informasi Dasar</h4>
                        <div class="space-y-3">
                             <div class="flex flex-col items-center mb-4">
                                <img id="info_photo" src="" alt="Profile Photo" class="w-24 h-24 rounded-full object-cover border-2 border-gray-200">
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Nama Lengkap</label>
                                <p id="info_full_name" class="font-medium text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Username</label>
                                <p id="info_username" class="font-medium text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Email</label>
                                <p id="info_email" class="font-medium text-gray-900"></p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Role</label>
                                <span id="info_role" class="px-2 py-1 text-xs font-semibold rounded-full"></span>
                            </div>
                             <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Status</label>
                                <span id="info_status" class="px-2 py-1 text-xs font-semibold rounded-full"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2">Detail Profil</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Mata Pelajaran / Jabatan</label>
                                <p id="info_subject" class="text-sm text-gray-700">-</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Biografi</label>
                                <p id="info_bio" class="text-sm text-gray-700 whitespace-pre-wrap">-</p>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Pendidikan</label>
                                <p id="info_education" class="text-sm text-gray-700 whitespace-pre-wrap">-</p>
                            </div>
                             <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Prestasi</label>
                                <p id="info_achievements" class="text-sm text-gray-700 whitespace-pre-wrap">-</p>
                            </div>
                             <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Sertifikat</label>
                                <p id="info_certificates" class="text-sm text-gray-700 whitespace-pre-wrap">-</p>
                            </div>
                             <div>
                                <label class="text-xs text-gray-500 uppercase tracking-wide">Pelatihan</label>
                                <p id="info_training" class="text-sm text-gray-700 whitespace-pre-wrap">-</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end">
                    <button onclick="closeInfoModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="passwordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="bg-yellow-600 px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold text-white">Ubah Password</h3>
            </div>
            <form method="POST" class="p-6">
                <input type="hidden" name="action" value="update_password">
                <input type="hidden" id="password_user_id" name="user_id">
                
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password" required minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 pr-10">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                onclick="togglePasswordVisibility('new_password', 'toggle_new_password')">
                            <i class="fas fa-eye text-gray-400" id="toggle_new_password"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                </div>
                
                <div class="mb-6">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 pr-10">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                onclick="togglePasswordVisibility('confirm_password', 'toggle_confirm_password')">
                            <i class="fas fa-eye text-gray-400" id="toggle_confirm_password"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closePasswordModal()" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">Batal</button>
                    <button type="submit" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePasswordVisibility(inputId, iconId) {
    const passwordField = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
