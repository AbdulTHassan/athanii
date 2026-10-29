<?php
/**
 * Public Pages Initialization
 */

require_once __DIR__ . '/config.php';

// ============ HELPER FUNCTIONS ============

/**
 * Samun duk active categories
 */
function getPublicCategories($pdo) {
    $stmt = $pdo->query("
        SELECT id, name, slug, description, icon, sort_order
        FROM knowledge_categories
        WHERE status = 'active'
        ORDER BY sort_order ASC, name ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Samun featured articles
 */
function getFeaturedArticles($pdo, $limit = 3) {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name AS category_name, c.slug AS category_slug
        FROM knowledge_articles a
        LEFT JOIN knowledge_categories c ON c.id = a.category_id
        WHERE a.status = 'published' AND a.is_featured = 1
        ORDER BY a.featured_order ASC, a.created_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Samun latest articles
 */
function getLatestArticles($pdo, $limit = 6) {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name AS category_name, c.slug AS category_slug
        FROM knowledge_articles a
        LEFT JOIN knowledge_categories c ON c.id = a.category_id
        WHERE a.status = 'published'
        ORDER BY a.created_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Samun article ɗaya ta slug
 */
function getArticleBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name AS category_name, c.slug AS category_slug,
               u.full_name AS author_name
        FROM knowledge_articles a
        LEFT JOIN knowledge_categories c ON c.id = a.category_id
        LEFT JOIN users u ON u.id = a.created_by
        WHERE a.slug = ? AND a.status = 'published'
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Samun articles na category
 */
function getArticlesByCategory($pdo, $categoryId, $limit = 50) {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name AS category_name, c.slug AS category_slug
        FROM knowledge_articles a
        LEFT JOIN knowledge_categories c ON c.id = a.category_id
        WHERE a.category_id = ? AND a.status = 'published'
        ORDER BY a.created_at DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, (int)$categoryId, PDO::PARAM_INT);
    $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Samun category ta slug
 */
function getCategoryBySlug($pdo, $slug) {
    $stmt = $pdo->prepare("
        SELECT id, name, slug, description, icon, sort_order
        FROM knowledge_categories
        WHERE slug = ? AND status = 'active'
        LIMIT 1
    ");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Ƙara view count
 */
function incrementArticleViews($pdo, $articleId) {
    $stmt = $pdo->prepare("
        UPDATE knowledge_articles 
        SET view_count = view_count + 1 
        WHERE id = ?
    ");
    $stmt->execute([(int)$articleId]);
}

/**
 * Bincike articles
 */
function searchArticles($pdo, $query, $limit = 20) {
    $stmt = $pdo->prepare("
        SELECT a.*, c.name AS category_name
        FROM knowledge_articles a
        LEFT JOIN knowledge_categories c ON c.id = a.category_id
        WHERE a.status = 'published'
          AND (a.title LIKE ? OR a.excerpt LIKE ? OR a.content LIKE ?)
        ORDER BY a.created_at DESC
        LIMIT ?
    ");
    $like = '%' . $query . '%';
    $stmt->bindValue(1, $like, PDO::PARAM_STR);
    $stmt->bindValue(2, $like, PDO::PARAM_STR);
    $stmt->bindValue(3, $like, PDO::PARAM_STR);
    $stmt->bindValue(4, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>