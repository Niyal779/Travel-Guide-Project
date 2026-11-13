<?php
require_once __DIR__ . '/../db.php';

function getAdminDashboardStats(): array
{
    $db = getConnection();
    return [
        'pending_requests' => (int)$db->query("SELECT COUNT(*) FROM post_requests WHERE status='pending'")->fetchColumn(),
        'pending_users' => (int)$db->query("SELECT COUNT(*) FROM users WHERE is_verified=0 AND role IN ('user','scout')")->fetchColumn(),
        'published_places' => (int)$db->query("SELECT COUNT(*) FROM posts WHERE status='approved'")->fetchColumn(),
        'travellers' => (int)$db->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
    ];
}

function getAdminPendingRequests(): array
{
    $db = getConnection();
    $stmt = $db->query("SELECT r.id, r.scout_id, r.post_data, r.requested_at, u.name AS scout_name, u.email AS scout_email
        FROM post_requests r LEFT JOIN users u ON u.id=r.scout_id
        WHERE r.status='pending' ORDER BY r.requested_at ASC");
    return $stmt->fetchAll();
}

function getAdminPendingUsers(): array
{
    $db = getConnection();
    $stmt = $db->query("SELECT id, name, email, role FROM users WHERE is_verified=0 AND role IN ('user','scout') ORDER BY id ASC");
    return $stmt->fetchAll();
}

function approveAdminPostRequest(int $requestId): bool
{
    $db = getConnection();
    $db->beginTransaction();
    try {
        $stmt = $db->prepare("SELECT * FROM post_requests WHERE id=? AND status='pending' FOR UPDATE");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch();
        if (!$request) { $db->rollBack(); return false; }
        $data = json_decode($request['post_data'], true);
        if (!is_array($data)) { $db->rollBack(); return false; }
        $history = $data['short_history'] ?? $data['history'] ?? '';
        $travel = $data['travel_medium_info'] ?? $data['travel'] ?? '';
        $originalId = (int)($data['original_post_id'] ?? 0);
        if ($originalId > 0) {
            $stmt = $db->prepare("UPDATE posts SET title=?, short_history=?, country=?, genre=?, cost_level=?, travel_medium_info=?, image=COALESCE(?, image), updated_at=NOW() WHERE id=? AND scout_id=?");
            $stmt->execute([$data['title'] ?? '', $history, $data['country'] ?? '', $data['genre'] ?? '', $data['cost_level'] ?? 'medium', $travel, $data['image'] ?? null, $originalId, $request['scout_id']]);
            if ($stmt->rowCount() === 0) {
                $check = $db->prepare("SELECT id FROM posts WHERE id=? AND scout_id=?");
                $check->execute([$originalId, $request['scout_id']]);
                if (!$check->fetch()) { $db->rollBack(); return false; }
            }
        } else {
            $stmt = $db->prepare("INSERT INTO posts (scout_id,title,short_history,country,genre,cost_level,travel_medium_info,image,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?, 'approved', NOW(), NOW())");
            $stmt->execute([$request['scout_id'], $data['title'] ?? '', $history, $data['country'] ?? '', $data['genre'] ?? '', $data['cost_level'] ?? 'medium', $travel, $data['image'] ?? null]);
        }
        $stmt = $db->prepare("UPDATE post_requests SET status='approved' WHERE id=? AND status='pending'");
        $stmt->execute([$requestId]);
        $db->commit();
        return true;
    } catch (Throwable $error) {
        if ($db->inTransaction()) $db->rollBack();
        throw $error;
    }
}

function rejectAdminPostRequest(int $requestId): bool
{
    $db = getConnection();
    $stmt = $db->prepare("UPDATE post_requests SET status='rejected' WHERE id=? AND status='pending'");
    $stmt->execute([$requestId]);
    return $stmt->rowCount() > 0;
}

function verifyAdminUser(int $userId): bool
{
    $db = getConnection();
    $stmt = $db->prepare("UPDATE users SET is_verified=1 WHERE id=? AND is_verified=0 AND role IN ('user','scout')");
    $stmt->execute([$userId]);
    return $stmt->rowCount() > 0;
}
