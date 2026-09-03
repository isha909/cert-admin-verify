<?php
require_once "auth.php";
require_once "db.php";

$username_error = "";
$username_success = "";
$password_error = "";
$password_success = "";

// Fetch current admin info
$stmt = mysqli_prepare($conn, "SELECT username, password_hash FROM admin_users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $_SESSION['admin_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

// ---------- Handle username update ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_username') {
    $new_username = trim($_POST['new_username'] ?? '');
    $confirm_password_for_username = $_POST['confirm_password_for_username'] ?? '';

    if ($new_username === '') {
        $username_error = "Username cannot be empty.";
    } elseif (!password_verify($confirm_password_for_username, $admin['password_hash'])) {
        $username_error = "Password is incorrect.";
    } else {
        // Check if username is already taken by someone else
        $stmt = mysqli_prepare($conn, "SELECT id FROM admin_users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($stmt, "si", $new_username, $_SESSION['admin_id']);
        mysqli_stmt_execute($stmt);
        $check = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($check) > 0) {
            $username_error = "That username is already taken.";
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE admin_users SET username = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "si", $new_username, $_SESSION['admin_id']);
            mysqli_stmt_execute($stmt);

            $_SESSION['admin_username'] = $new_username;
            $admin['username'] = $new_username;
            $username_success = "Username updated successfully.";
        }
    }
}

// ---------- Handle password update ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!password_verify($current_password, $admin['password_hash'])) {
        $password_error = "Current password is incorrect.";
    } elseif (strlen($new_password) < 8) {
        $password_error = "New password must be at least 8 characters.";
    } elseif ($new_password !== $confirm_password) {
        $password_error = "New password and confirmation do not match.";
    } else {
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE admin_users SET password_hash = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_hash, $_SESSION['admin_id']);
        mysqli_stmt_execute($stmt);
        $password_success = "Password updated successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Account Settings | CertPro Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="admin-style.css">
</head>
<body>

<div class="topbar">
    <div class="brand"><span class="dot"></span> CertPro Admin</div>
    <a href="index.php" class="btn btn-outline btn-sm">← Back to list</a>
</div>

<div class="container">
    <div class="page-header">
        <div>
            <h1>Account Settings</h1>
            <p>Update your username and password</p>
        </div>
    </div>

    <div class="settings-grid">
    <!-- Username form -->
    <div class="form-card">
        <h2 style="font-size:16px; margin:0 0 18px;">Change Username</h2>
        <p style="color:var(--text-grey); font-size:13px; margin:-12px 0 18px;">
            Current username: <strong style="color:var(--text-white);"><?php echo htmlspecialchars($admin['username']); ?></strong>
        </p>

        <?php if ($username_error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($username_error); ?></div>
        <?php endif; ?>
        <?php if ($username_success): ?>
            <div class="success-msg"><?php echo htmlspecialchars($username_success); ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <input type="hidden" name="action" value="update_username">
            <div class="field">
                <label>New Username</label>
                <input type="text" name="new_username" required>
            </div>
            <div class="field">
                <label>Confirm With Your Password</label>
                <input type="password" name="confirm_password_for_username" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Update Username</button>
            </div>
        </form>
    </div>

    <!-- Password form -->
    <div class="form-card">
        <h2 style="font-size:16px; margin:0 0 18px;">Change Password</h2>

        <?php if ($password_error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($password_error); ?></div>
        <?php endif; ?>
        <?php if ($password_success): ?>
            <div class="success-msg"><?php echo htmlspecialchars($password_success); ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <input type="hidden" name="action" value="update_password">
            <div class="field">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="field">
                <label>New Password</label>
                <input type="password" name="new_password" required minlength="8">
            </div>
            <div class="field">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required minlength="8">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Update Password</button>
            </div>
        </form>
    </div>
    </div>
</div>

</body>
</html>
