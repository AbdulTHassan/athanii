<?php
/**
 * Fatwa System Helpers
 */

function getFatwaCategories($pdo) {
    $stmt = $pdo->query("
        SELECT id, name, slug, description
        FROM fatwa_categories
        WHERE status = 'active'
        ORDER BY id ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPublishedFatwas($pdo, $limit = 50, $offset = 0, $search = '', $categoryId = 0, $language = '') {
    $where = ["f.status = 'published'"];
    $params = [];
    
    if (!empty($search)) {
        $where[] = "(f.question LIKE :search OR f.answer LIKE :search OR f.source LIKE :search OR f.scholar LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }
    
    if ($categoryId > 0) {
        $where[] = "f.category_id = :category_id";
        $params[':category_id'] = $categoryId;
    }
    
    if (!empty($language) && in_array($language, ['en', 'ha', 'ar'])) {
        $where[] = "f.language = :language";
        $params[':language'] = $language;
    }
    
    $whereSql = implode(' AND ', $where);
    
    $sql = "
        SELECT f.*, c.name AS category_name, c.slug AS category_slug
        FROM fatwas f
        LEFT JOIN fatwa_categories c ON c.id = f.category_id
        WHERE {$whereSql}
        ORDER BY f.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getFatwaById($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT f.*, c.name AS category_name, c.slug AS category_slug,
               u.full_name AS author_name
        FROM fatwas f
        LEFT JOIN fatwa_categories c ON c.id = f.category_id
        LEFT JOIN users u ON u.id = f.created_by
        WHERE f.id = ? AND f.status = 'published'
        LIMIT 1
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUserQuestions($pdo, $status = 'pending', $limit = 50, $offset = 0) {
    $sql = "
        SELECT q.*, c.name AS category_name,
               u.full_name AS answered_by_name
        FROM user_questions q
        LEFT JOIN fatwa_categories c ON c.id = q.category_id
        LEFT JOIN users u ON u.id = q.answered_by
    ";
    
    if (!empty($status)) {
        $sql .= " WHERE q.workflow_status = :status";
    }
    
    $sql .= " ORDER BY q.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    if (!empty($status)) {
        $stmt->bindValue(':status', $status);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserQuestionById($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT q.*, c.name AS category_name
        FROM user_questions q
        LEFT JOIN fatwa_categories c ON c.id = q.category_id
        WHERE q.id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function countPublishedFatwas($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM fatwas WHERE status = 'published'");
    return (int)$stmt->fetchColumn();
}

function countPendingQuestions($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM user_questions WHERE workflow_status = 'pending'");
    return (int)$stmt->fetchColumn();
}