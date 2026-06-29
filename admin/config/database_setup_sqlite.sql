-- Database setup untuk SD Integra IV



-- Tabel untuk admin users
CREATE TABLE IF NOT EXISTS admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL,
    subject VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel untuk berita sekolah
CREATE TABLE IF NOT EXISTS news (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    category TEXT DEFAULT 'umum',
    status TEXT DEFAULT 'draft',
    author_id INT,
    views INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    published_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Tabel untuk program akademik
CREATE TABLE IF NOT EXISTS academic_programs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    grade_level TEXT NOT NULL,
    curriculum_type TEXT DEFAULT 'nasional',
    subjects TEXT, -- JSON format untuk daftar mata pelajaran
    learning_methods TEXT, -- JSON format untuk metode pembelajaran
    assessment_methods TEXT, -- JSON format untuk metode penilaian
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel untuk informasi umum
CREATE TABLE IF NOT EXISTS general_info (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type TEXT NOT NULL,
    priority TEXT DEFAULT 'sedang',
    expiry_date DATE NULL,
    attachment VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel untuk inovasi pembelajaran
CREATE TABLE IF NOT EXISTS innovations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category TEXT NOT NULL,
    implementation_year YEAR NOT NULL,
    benefits TEXT, -- JSON format untuk daftar manfaat
    features TEXT, -- JSON format untuk daftar fitur
    image VARCHAR(255),
    video_url VARCHAR(500),
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel untuk galeri/media
CREATE TABLE IF NOT EXISTS media_gallery (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type TEXT NOT NULL,
    file_size INT NOT NULL,
    alt_text VARCHAR(255),
    category VARCHAR(100),
    is_public BOOLEAN DEFAULT TRUE,
    uploaded_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Tabel untuk konfigurasi website
CREATE TABLE IF NOT EXISTS site_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type TEXT DEFAULT 'text',
    description VARCHAR(255),
    is_public BOOLEAN DEFAULT TRUE,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel untuk pesan kontak
CREATE TABLE IF NOT EXISTS contact_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status TEXT DEFAULT 'new',
    ip_address VARCHAR(45),
    user_agent TEXT,
    replied_by INT NULL,
    replied_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, email, password, full_name, role) VALUES 
('admin', 'admin@sdiptunasbangsa.sch.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'super_admin');

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, description, is_public) VALUES 
('site_name', 'SDIP Tunas Bangsa', 'text', 'Nama sekolah', true),
('site_tagline', 'Membentuk Generasi Cerdas Untuk Masa Depan Cerah', 'text', 'Tagline sekolah', true),
('school_address', 'Jl. Pendidikan Raya No. 123, Jakarta Selatan', 'text', 'Alamat sekolah', true),
('school_phone', '(021) 12345678', 'text', 'Nomor telepon sekolah', true),
('school_email', 'info@sdiptunasbangsa.sch.id', 'text', 'Email sekolah', true),
('school_founded', '2009', 'text', 'Tahun berdiri', true),
('total_students', '500', 'number', 'Jumlah siswa aktif', true),
('total_teachers', '25', 'number', 'Jumlah guru', true),
('graduation_rate', '98', 'number', 'Tingkat kelulusan (%)', true),
('facebook_url', '#', 'text', 'Link Facebook', true),
('instagram_url', '#', 'text', 'Link Instagram', true),
('youtube_url', '#', 'text', 'Link YouTube', true),
('whatsapp_number', '6281234567890', 'text', 'Nomor WhatsApp', true);

-- Sample data untuk testing
INSERT INTO news (title, slug, content, excerpt, category, status, author_id, is_featured, published_at) VALUES 
('Prestasi Siswa dalam Olimpiade Matematika', 'prestasi-olimpiade-matematika-2024', 'Siswa-siswa SD Integra IV berhasil meraih prestasi membanggakan dalam Olimpiade Matematika tingkat kota...', 'Prestasi membanggakan siswa dalam kompetisi matematika', 'prestasi', 'published', 1, true, CURRENT_TIMESTAMP),
('Kegiatan Belajar Mengajar Semester Baru', 'kegiatan-belajar-semester-baru', 'Semester baru telah dimulai dengan semangat dan antusiasme tinggi dari seluruh siswa...', 'Dimulainya aktivitas pembelajaran semester baru', 'kegiatan', 'published', 1, false, CURRENT_TIMESTAMP);

INSERT INTO academic_programs (title, description, grade_level, curriculum_type, subjects, learning_methods, is_active) VALUES 
('Program Literasi Digital', 'Program pembelajaran berbasis teknologi untuk meningkatkan kemampuan digital siswa', 'semua', 'nasional', '["Komputer", "Internet Safety", "Digital Citizenship"]', '["Project Based Learning", "Collaborative Learning"]', true),
('Program Bahasa Internasional', 'Program penguatan bahasa Inggris dengan native speaker', '4', 'internasional', '["English Communication", "English Literature", "Presentation Skills"]', '["Immersive Learning", "Role Playing"]', true);

INSERT INTO innovations (title, description, category, implementation_year, benefits, features, is_featured, is_active) VALUES 
('Smart Classroom Technology', 'Ruang kelas pintar dengan teknologi interaktif terdepan', 'teknologi', 2023, '["Pembelajaran lebih interaktif", "Meningkatkan engagement siswa", "Efisiensi waktu belajar"]', '["Interactive Whiteboard", "Tablet untuk setiap siswa", "Real-time Assessment"]', true, true),
('Metode pembelajaran STEAM', 'Pendekatan pembelajaran Science, Technology, Engineering, Arts, and Mathematics', 'metode', 2022, '["Mengembangkan kreativitas", "Problem solving skills", "Kolaborasi tim"]', '["Project Based Learning", "Hands-on Activities", "Cross-curricular Integration"]', true, true);
CREATE TABLE IF NOT EXISTS teacher_profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INT NOT NULL,
    subject VARCHAR(255) NULL,
    photo_filename VARCHAR(255) NULL,
    bio TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id) ON DELETE CASCADE
);


