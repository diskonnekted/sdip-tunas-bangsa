<?php
@mkdir('admin/uploads', 0777, true);
copy('images/logo.png', 'admin/uploads/logo.png');

$db = new PDO('sqlite:admin/config/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Cek apakah key 'logo' sudah ada atau gunakan nama yang sesuai di getSchoolInfo()
// Wait, is it 'school_logo' or 'logo'? Let me check includes/settings.php if possible. 
// Ah, the code in header.php says: $school_info['logo']
// Let's just insert/update both 'logo' and 'school_logo' to be safe.

$stmt = $db->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type, is_public) VALUES ('logo', 'logo.png', 'image', 1) ON CONFLICT(setting_key) DO UPDATE SET setting_value='logo.png'");
$stmt->execute();

$stmt2 = $db->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_type, is_public) VALUES ('school_logo', 'logo.png', 'image', 1) ON CONFLICT(setting_key) DO UPDATE SET setting_value='logo.png'");
$stmt2->execute();

echo 'Logo set!';
?>
