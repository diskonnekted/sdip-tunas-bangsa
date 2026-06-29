<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit;
}

try {
    require_once '../admin/config/database.php';
    require_once '../admin/models/ContactMessage.php';

    // Get database connection
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception('Database connection failed');
    }

    // Initialize ContactMessage model
    $contactMessage = new ContactMessage($db);

    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // If JSON decoding failed, try to get from $_POST
    if (!$input) {
        $input = $_POST;
    }

    // Validate required fields (matching frontend form)
    $required_fields = ['name', 'email', 'subject', 'message'];
    $errors = [];

    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            $errors[] = ucfirst($field) . ' harus diisi';
        }
    }

    // Validate email format
    if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }

    // Validate phone number if provided
    if (!empty($input['phone'])) {
        $phone = preg_replace('/[\s-]/', '', $input['phone']);
        if (!preg_match('/^(\+62|62|0)[0-9]{8,13}$/', $phone)) {
            $errors[] = 'Format nomor telepon tidak valid';
        }
    }

    // Check for spam (simple validation)
    if (!empty($input['message']) && strlen($input['message']) < 10) {
        $errors[] = 'Pesan terlalu pendek';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Data tidak valid',
            'errors' => $errors
        ]);
        exit;
    }

    // Get client info
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // Create contact message using model
    $result = $contactMessage->create(
        $input['name'],
        $input['email'], 
        $input['phone'] ?? '',
        $input['subject'] ?? 'Pesan Baru', // Subject might be empty for teacher contact
        $input['message'],
        $ip_address,
        $user_agent,
        $input['recipient_type'] ?? 'general',
        $input['recipient_id'] ?? null,
        $input['student_name'] ?? null
    );

    if ($result['success']) {
        // You could add email notification here
        // sendNotificationEmail($input['name'], $input['email'], $input['subject'], $input['message']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Pesan Anda berhasil dikirim! Kami akan segera menghubungi Anda kembali.',
            'data' => [
                'id' => $result['id'],
                'name' => $input['name'],
                'email' => $input['email'],
                'subject' => $input['subject'],
                'submitted_at' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        throw new Exception($result['message'] ?? 'Gagal menyimpan pesan ke database');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
    ]);
}

// Optional: Function to send email notification
function sendNotificationEmail($name, $email, $subject, $message) {
    // You can implement email sending here using PHPMailer or similar
    // This is just a placeholder for the functionality
    
    $to = 'info@sdiptunasbangsa.sch.id';
    $emailSubject = 'Pesan Baru dari Website: ' . $subject;
    $emailBody = "
        Pesan baru dari website SDIP Tunas Bangsa:
        
        Nama: $name
        Email: $email
        Subjek: $subject
        
        Pesan:
        $message
        
        ---
        Dikirim pada: " . date('d/m/Y H:i:s') . "
        IP Address: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "
    ";
    
    $headers = [
        'From: noreply@sdiptunasbangsa.sch.id',
        'Reply-To: ' . $email,
        'X-Mailer: PHP/' . phpversion(),
        'Content-Type: text/plain; charset=UTF-8'
    ];
    
    // Uncomment to enable email sending
    // mail($to, $emailSubject, $emailBody, implode("\r\n", $headers));
}
?>
