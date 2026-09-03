<?php
require_once "auth.php";
require_once "db.php";

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$error = "";

// Load existing record
$stmt = mysqli_prepare($conn, "SELECT * FROM certificates WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cert = mysqli_fetch_assoc($result);

if (!$cert) {
    header("Location: index.php?msg=" . urlencode("Certificate not found."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $certificate_code = trim($_POST['certificate_code'] ?? '');
    $student_name = trim($_POST['student_name'] ?? '');
    $program_name = trim($_POST['program_name'] ?? '');
    $issue_date = trim($_POST['issue_date'] ?? '');
    $issue_date = $issue_date !== '' ? $issue_date : null;

    if ($certificate_code === '' || $student_name === '' || $program_name === '') {
        $error = "Code, student name, and program are required.";
        $cert = ['id' => $id, 'certificate_code' => $certificate_code, 'student_name' => $student_name, 'program_name' => $program_name, 'issue_date' => $issue_date];
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE certificates SET certificate_code = ?, student_name = ?, program_name = ?, issue_date = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssi", $certificate_code, $student_name, $program_name, $issue_date, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?msg=" . urlencode("Certificate updated successfully."));
            exit;
        } else {
            if (mysqli_errno($conn) == 1062) {
                $error = "Another certificate already uses this code.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $cert = ['id' => $id, 'certificate_code' => $certificate_code, 'student_name' => $student_name, 'program_name' => $program_name, 'issue_date' => $issue_date];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Edit Certificate | CertPro Admin</title>
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
            <h1>Edit Certificate</h1>
            <p>Update record details</p>
        </div>
    </div>

    <div class="form-card">
        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="field">
                <label>Certificate Code</label>
                <input type="text" name="certificate_code" value="<?php echo htmlspecialchars($cert['certificate_code']); ?>" required>
            </div>
            <div class="field">
                <label>Student Name</label>
                <input type="text" name="student_name" value="<?php echo htmlspecialchars($cert['student_name']); ?>" required>
            </div>
            <div class="field">
                <label>Program Name</label>
                <input type="text" name="program_name" value="<?php echo htmlspecialchars($cert['program_name']); ?>" required>
            </div>
            <div class="field">
                <label>Issue Date</label>
                <input type="date" name="issue_date" value="<?php echo htmlspecialchars($cert['issue_date'] ?? ''); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
