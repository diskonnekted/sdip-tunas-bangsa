<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once '../admin/config/database.php';
    require_once '../admin/models/News.php';
    require_once '../admin/includes/functions.php';

    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $news = new News($db);
    
    $action = $_GET['action'] ?? 'list';
    $limit = isset($_GET['limit']) ? max(1, min(50, (int)$_GET['limit'])) : 10;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $category = $_GET['category'] ?? '';
    $slug = $_GET['slug'] ?? '';
    $search = $_GET['search'] ?? '';

    $offset = ($page - 1) * $limit;

    switch($action) {
        case 'list':
            $newsData = $news->getPublished($limit, $offset, $category);
            
            // Get total count
            $totalQuery = "SELECT COUNT(*) as total FROM news WHERE status = 'published'";
            $params = [];
            if (!empty($category)) {
                $totalQuery .= " AND category = ?";
                $params[] = $category;
            }
            
            $stmt = $db->prepare($totalQuery);
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            $formattedNews = [];
            foreach ($newsData as $item) {
                $formattedNews[] = [
                    'id' => (int)$item['id'],
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'excerpt' => $item['excerpt'] ?: substr(strip_tags($item['content']), 0, 150) . '...',
                    'category' => $item['category'],
                    'featured_image' => $item['featured_image'] ? 'admin/uploads/' . $item['featured_image'] : null,
                    'views' => (int)$item['views'],
                    'is_featured' => (bool)$item['is_featured'],
                    'published_at' => $item['published_at'],
                    'formatted_date' => formatTanggal($item['published_at'] ?: $item['created_at']),
                    'author_name' => $item['author_name'] ?? 'Admin'
                ];
            }
            
            $response = [
                'success' => true,
                'data' => $formattedNews,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($total / $limit),
                    'total_records' => (int)$total,
                    'per_page' => $limit
                ]
            ];
            break;
            
        case 'featured':
            $featuredNews = $news->getFeatured($limit);
            $formattedFeatured = [];
            
            foreach ($featuredNews as $item) {
                $formattedFeatured[] = [
                    'id' => (int)$item['id'],
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'excerpt' => $item['excerpt'] ?: substr(strip_tags($item['content']), 0, 150) . '...',
                    'category' => $item['category'],
                    'featured_image' => $item['featured_image'] ? 'admin/uploads/' . $item['featured_image'] : null,
                    'views' => (int)$item['views'],
                    'formatted_date' => formatTanggal($item['published_at']),
                    'author_name' => $item['author_name'] ?? 'Admin'
                ];
            }
            
            $response = [
                'success' => true,
                'data' => $formattedFeatured
            ];
            break;
            
        case 'detail':
            if (empty($slug)) {
                throw new Exception('Slug parameter required');
            }
            
            $newsDetail = $news->getBySlug($slug);
            if (!$newsDetail || $newsDetail['status'] !== 'published') {
                throw new Exception('News not found', 404);
            }
            
            // Increment views
            $news->incrementViews($newsDetail['id']);
            
            // Get related news
            $related = $news->getRelated($newsDetail['id'], $newsDetail['category'], 4);
            $formattedRelated = [];
            
            foreach ($related as $item) {
                $formattedRelated[] = [
                    'title' => $item['title'],
                    'slug' => $item['slug'],
                    'excerpt' => substr(strip_tags($item['content']), 0, 100) . '...',
                    'featured_image' => $item['featured_image'] ? 'admin/uploads/' . $item['featured_image'] : null,
                    'formatted_date' => formatTanggal($item['published_at']),
                    'author_name' => $item['author_name'] ?? 'Admin'
                ];
            }
            
            $response = [
                'success' => true,
                'data' => [
                    'id' => (int)$newsDetail['id'],
                    'title' => $newsDetail['title'],
                    'slug' => $newsDetail['slug'],
                    'content' => $newsDetail['content'],
                    'excerpt' => $newsDetail['excerpt'],
                    'category' => $newsDetail['category'],
                    'featured_image' => $newsDetail['featured_image'] ? 'admin/uploads/' . $newsDetail['featured_image'] : null,
                    'views' => (int)$newsDetail['views'] + 1,
                    'formatted_date' => formatTanggal($newsDetail['published_at'] ?: $newsDetail['created_at']),
                    'author_name' => $newsDetail['author_name'] ?? 'Admin'
                ],
                'related' => $formattedRelated
            ];
            break;
            
        case 'categories':
            $categories = $news->getCategories();
            $response = [
                'success' => true,
                'data' => $categories
            ];
            break;
            
        case 'stats':
            // Get statistics for news dashboard
            $statsQuery = "
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN category = 'prestasi' THEN 1 ELSE 0 END) as prestasi,
                    SUM(CASE WHEN category = 'kegiatan' THEN 1 ELSE 0 END) as kegiatan,
                    SUM(CASE WHEN category = 'pengumuman' THEN 1 ELSE 0 END) as pengumuman,
                    SUM(CASE WHEN category = 'umum' THEN 1 ELSE 0 END) as umum
                FROM news 
                WHERE status = 'published'
            ";
            
            $stmt = $db->prepare($statsQuery);
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $response = [
                'success' => true,
                'data' => [
                    'total' => (int)$stats['total'],
                    'prestasi' => (int)$stats['prestasi'],
                    'kegiatan' => (int)$stats['kegiatan'],
                    'pengumuman' => (int)$stats['pengumuman'],
                    'umum' => (int)$stats['umum']
                ]
            ];
            break;
            
        default:
            throw new Exception('Invalid action parameter');
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch(Exception $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
