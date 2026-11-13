<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$pageTitle = 'Login — Travel Guide';
$baseUrl   = '../../';
include '../../view/layout/header.php';

$errors = $_SESSION['errors'] ?? [];
$old    = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="container">
    <div class="card auth-card" style="max-width:450px;margin:0 auto;">
        <span class="eyebrow">WELCOME BACK</span><h1>Log in to Travel Guide</h1><p class="auth-subtitle">Pick up where your next trip left off.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="../../controller/auth/AuthController.php?action=login" novalidate>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="you@example.com">
                <div class="error-msg" id="err-email"></div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-field"><input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password"><button class="password-toggle" type="button" data-password-target="password" aria-label="Show password" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg></button></div>
                <div class="error-msg" id="err-password"></div>
            </div>

            <div style="display:flex;align-items:center;gap:8px;margin-bottom:18px;">
                <input type="checkbox" id="remember_me" name="remember_me" style="width:16px;height:16px;">
                <label for="remember_me" style="font-size:14px;font-weight:normal;margin:0;">Remember me for 30 days</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Login</button>
        </form>

        <p style="margin-top:16px;font-size:14px;text-align:center;">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let valid = true;

    document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');

    const email    = document.getElementById('email');
    const password = document.getElementById('password');

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email.value.trim())) {
        document.getElementById('err-email').textContent = 'Enter a valid email address.';
        valid = false;
    }

    if (password.value.trim() === '') {
        document.getElementById('err-password').textContent = 'Password is required.';
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>

<?php include '../../view/layout/footer.php'; ?>
