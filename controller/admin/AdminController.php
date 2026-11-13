<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/auth/auth.php';
requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../view/admin/dashboard.php'); exit;
}
$sessionToken = $_SESSION['csrf_token'] ?? '';
if ($sessionToken === '' || !hash_equals($sessionToken, $_POST['csrf_token'] ?? '')) {
    $_SESSION['flash'] = 'Your session token expired. Please try again.';
    $_SESSION['flash_type'] = 'error';
    header('Location: ../../view/admin/dashboard.php'); exit;
}

require_once __DIR__ . '/../../model/admin/AdminModel.php';
$action = $_POST['action'] ?? '';
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$success = false;
if ($id > 0 && $action === 'approve_request') $success = approveAdminPostRequest($id);
elseif ($id > 0 && $action === 'reject_request') $success = rejectAdminPostRequest($id);
elseif ($id > 0 && $action === 'verify_user') $success = verifyAdminUser($id);

$_SESSION['flash'] = $success ? 'The change has been saved.' : 'Nothing changed. The item may already have been reviewed.';
$_SESSION['flash_type'] = $success ? 'success' : 'warning';
header('Location: ../../view/admin/dashboard.php');
exit;
