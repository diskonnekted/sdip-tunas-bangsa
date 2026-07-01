<?php
/**
 * Favicon Include File
 * Include this file in the <head> section of all pages
 */
?>
<?php
if (!isset($school_info)) {
    if (function_exists('getSchoolInfo')) {
        $school_info = getSchoolInfo();
    } else {
        // Fetch manually if function is not available
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=sd_integra_iv;charset=utf8mb4", "root", "");
            $stmt = $pdo->query("SELECT * FROM school_info LIMIT 1");
            $school_info = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $school_info = [];
        }
    }
}
$favicon_url = !empty($school_info['logo']) ? '/admin/uploads/' . htmlspecialchars($school_info['logo']) : '/images/favicon/favicon.png';
?>
<!-- Favicon Links -->
<link rel="icon" type="image/png" href="<?php echo $favicon_url; ?>">
<link rel="apple-touch-icon" href="<?php echo $favicon_url; ?>">
<meta name="theme-color" content="#22c55e">
