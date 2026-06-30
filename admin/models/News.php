<?php
class News {
    private $conn;
    private $table_name = "news";

    public $id;
    public $title;
    public $slug;
    public $content;
    public $excerpt;
    public $featured_image;
    public $category;
    public $status;
    public $author_id;
    public $views;
    public $is_featured;
    public $published_at;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all news with pagination
    public function getAll($limit = 10, $offset = 0, $search = '', $category = '', $status = '') {
        // Ensure limit and offset are integers
        $limit = (int)$limit;
        $offset = (int)$offset;
        $query = "SELECT n.*, u.full_name as author_name 
                  FROM " . $this->table_name . " n
                  LEFT JOIN admin_users u ON n.author_id = u.id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (n.title LIKE ? OR n.content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($category)) {
            $query .= " AND n.category = ?";
            $params[] = $category;
        }
        
        if (!empty($status)) {
            $query .= " AND n.status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY n.created_at DESC LIMIT $limit OFFSET $offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Count total records for pagination
    public function count($search = '', $category = '', $status = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (title LIKE ? OR content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if (!empty($category)) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        
        if (!empty($status)) {
            $query .= " AND status = ?";
            $params[] = $status;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Get single news by ID
    public function getById($id) {
        $query = "SELECT n.*, u.full_name as author_name FROM " . $this->table_name . " n 
                  LEFT JOIN admin_users u ON n.author_id = u.id WHERE n.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get single news by slug
    public function getBySlug($slug) {
        $query = "SELECT n.*, u.full_name as author_name FROM " . $this->table_name . " n 
                  LEFT JOIN admin_users u ON n.author_id = u.id WHERE n.slug = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create new news
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (title, slug, content, excerpt, featured_image, category, status, author_id, is_featured, published_at, created_at) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Generate published_at if status is published
        $published_at = ($this->status === 'published') ? date('Y-m-d H:i:s') : null;
        
        $result = $stmt->execute([
            $this->title,
            $this->slug,
            $this->content,
            $this->excerpt,
            $this->featured_image,
            $this->category,
            $this->status,
            $this->author_id,
            $this->is_featured ? 1 : 0,
            $published_at
        ]);
        
        if ($result) {
            $this->id = $this->conn->lastInsertId();
        }
        
        return $result;
    }

    // Update existing news
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = ?, slug = ?, content = ?, excerpt = ?, featured_image = ?, 
                      category = ?, status = ?, is_featured = ?, published_at = ?, updated_at = NOW()
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Update published_at if status changed to published
        $current_data = $this->getById($this->id);
        $published_at = $current_data['published_at'];
        
        if ($this->status === 'published' && $current_data['status'] !== 'published') {
            $published_at = date('Y-m-d H:i:s');
        } elseif ($this->status !== 'published') {
            $published_at = null;
        }
        
        return $stmt->execute([
            $this->title,
            $this->slug,
            $this->content,
            $this->excerpt,
            $this->featured_image,
            $this->category,
            $this->status,
            $this->is_featured ? 1 : 0,
            $published_at,
            $this->id
        ]);
    }

    // Delete news
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->id]);
    }

    // Check if slug exists (for validation)
    public function slugExists($slug, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE slug = ?";
        $params = [$slug];
        
        if ($exclude_id) {
            $query .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }

    // Get published news for frontend
    public function getPublished($limit = 10, $offset = 0, $category = '') {
        // Ensure limit and offset are integers
        $limit = (int)$limit;
        $offset = (int)$offset;
        $query = "SELECT n.*, u.full_name as author_name FROM " . $this->table_name . " n 
                  LEFT JOIN admin_users u ON n.author_id = u.id 
                  WHERE n.status = 'published'";
        
        $params = [];
        
        if (!empty($category)) {
            $query .= " AND n.category = ?";
            $params[] = $category;
        }
        
        $query .= " ORDER BY n.published_at DESC LIMIT $limit OFFSET $offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get featured news
    public function getFeatured($limit = 5) {
        // Ensure limit is integer
        $limit = (int)$limit;
        $query = "SELECT n.*, u.full_name as author_name FROM " . $this->table_name . " n 
                  LEFT JOIN admin_users u ON n.author_id = u.id 
                  WHERE n.status = 'published' AND n.is_featured = 1
                  ORDER BY n.published_at DESC 
                  LIMIT $limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Increment views
    public function incrementViews($id) {
        $query = "UPDATE " . $this->table_name . " SET views = views + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Get categories with count
    public function getCategories() {
        $query = "SELECT category, COUNT(*) as count 
                  FROM " . $this->table_name . " 
                  WHERE status = 'published'
                  GROUP BY category 
                  ORDER BY category";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get related news
    public function getRelated($current_id, $category, $limit = 5) {
        // Ensure limit is integer
        $limit = (int)$limit;
        $query = "SELECT n.*, u.full_name as author_name FROM " . $this->table_name . " n 
                  LEFT JOIN admin_users u ON n.author_id = u.id 
                  WHERE n.status = 'published' 
                  AND n.category = ? 
                  AND n.id != ?
                  ORDER BY n.published_at DESC 
                  LIMIT $limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$category, $current_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Validate news data
    public function validate($data, $is_update = false) {
        $errors = [];
        
        // Required fields
        if (empty($data['title'])) {
            $errors[] = 'Judul berita harus diisi';
        }
        
        if (empty($data['content'])) {
            $errors[] = 'Konten berita harus diisi';
        }
        
        if (empty($data['category'])) {
            $errors[] = 'Kategori harus dipilih';
        }
        
        // Validate slug
        if (!empty($data['title'])) {
            $slug = createSlug($data['title']);
            $exclude_id = $is_update ? $this->id : null;
            
            if ($this->slugExists($slug, $exclude_id)) {
                $errors[] = 'Judul sudah digunakan, silakan gunakan judul yang berbeda';
            }
        }
        
        // Validate category
        $allowed_categories = ['umum', 'prestasi', 'kegiatan', 'pengumuman'];
        if (!empty($data['category']) && !in_array($data['category'], $allowed_categories)) {
            $errors[] = 'Kategori tidak valid';
        }
        
        // Validate status
        $allowed_statuses = ['draft', 'published', 'archived'];
        if (!empty($data['status']) && !in_array($data['status'], $allowed_statuses)) {
            $errors[] = 'Status tidak valid';
        }
        
        return $errors;
    }
}
?>
