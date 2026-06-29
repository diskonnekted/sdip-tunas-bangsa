<?php
/**
 * Settings Helper Functions
 * Fungsi untuk mengambil dan mengelola settings dari database
 */

require_once __DIR__ . '/../admin/config/database.php';

class Settings {
    private static $cache = [];
    private static $db = null;
    
    private static function getDB() {
        if (self::$db === null) {
            $database = new Database();
            self::$db = $database->getConnection();
        }
        return self::$db;
    }
    
    /**
     * Get school settings from school_settings table
     */
    private static function getSchoolSettings() {
        if (!empty(self::$cache)) {
            return self::$cache;
        }
        
        try {
            $db = self::getDB();
            $stmt = $db->query('SELECT * FROM school_settings ORDER BY id DESC LIMIT 1');
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Cache the result
                self::$cache = $result;
                return $result;
            }
            
            return [];
        } catch (Exception $e) {
            error_log('Settings::getSchoolSettings error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single setting value
     */
    public static function get($key, $default = '') {
        $settings = self::getSchoolSettings();
        return isset($settings[$key]) ? $settings[$key] : $default;
    }
    
    /**
     * Get all settings as associative array
     */
    public static function getAll() {
        return self::getSchoolSettings();
    }
    
    /**
     * Get school information
     */
    public static function getSchoolInfo() {
        return [
            'name' => self::get('school_name', 'SDIP Tunas Bangsa'),
            'address' => self::get('school_address', 'Jl. Pendidikan No. 123, Jakarta'),
            'phone' => self::get('school_phone', '(021) 12345678'),
            'email' => self::get('school_email', 'info@sdintegraiv.sch.id'),
            'description' => self::get('school_description', 'Membentuk generasi cerdas dan berkarakter untuk masa depan Indonesia yang lebih baik.'),
            'motto' => self::get('school_motto', 'Cerdas, Berkarakter, dan Berintegritas'),
            'logo' => self::get('school_logo', ''),
            'website' => self::get('school_website', 'https://sdintegraiv.sch.id'),
            'principal_name' => self::get('principal_name', 'Kepala Sekolah'),
            'principal_photo' => self::get('principal_photo', ''),
            'established_year' => self::get('established_year', '2010'),
            'npsn' => self::get('npsn', ''),
            'accreditation' => self::get('accreditation', 'A'),
            'facebook' => self::get('facebook_url', '#'),
            'instagram' => self::get('instagram_url', '#'),
            'youtube' => self::get('youtube_url', '#'),
            'twitter' => self::get('twitter_url', '#')
        ];
    }
    
    /**
     * Get contact information  
     */
    public static function getContactInfo() {
        return [
            'address' => self::get('school_address', 'Jl. Pendidikan No. 123, Jakarta'),
            'phone' => self::get('school_phone', '(021) 12345678'),
            'email' => self::get('school_email', 'info@sdintegraiv.sch.id'),
            'operating_hours' => 'Senin - Jumat: 07:00 - 16:00'
        ];
    }
    
    /**
     * Get social media links
     */
    public static function getSocialMedia() {
        return [
            'facebook' => self::get('facebook_url', '#'),
            'instagram' => self::get('instagram_url', '#'),  
            'youtube' => self::get('youtube_url', '#'),
            'twitter' => self::get('twitter_url', '#'),
            'whatsapp' => '#'
        ];
    }
    
    /**
     * Clear cache (useful after settings update)
     */
    public static function clearCache() {
        self::$cache = [];
    }
    
    /**
     * Check if setting exists
     */
    public static function exists($key) {
        try {
            $db = self::getDB();
            $stmt = $db->prepare('SELECT COUNT(*) FROM settings WHERE setting_key = ?');
            $stmt->execute([$key]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Convenience functions for templates
function getSetting($key, $default = '') {
    return Settings::get($key, $default);
}

function getSchoolInfo() {
    return Settings::getSchoolInfo();
}

function getContactInfo() {
    return Settings::getContactInfo();
}

function getSocialMedia() {
    return Settings::getSocialMedia();
}
?>
