<?php
require_once "auth.php";
require_once "db.php";

$error = "";
$values = [
    'certificate_code' => '',
    'student_name' => '',
    'program_name' => '',
    'issue_date' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $v) {
        $values[$key] = trim($_POST[$key] ?? '');
    }

    if ($values['certificate_code'] === '' || $values['student_name'] === '' || $values['program_name'] === '') {
        $error = "Code, student name, and program are required.";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO certificates (certificate_code, student_name, program_name, issue_date) VALUES (?, ?, ?, ?)");
        $issueDate = $values['issue_date'] !== '' ? $values['issue_date'] : null;
        mysqli_stmt_bind_param($stmt, "ssss", $values['certificate_code'], $values['student_name'], $values['program_name'], $issueDate);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: index.php?msg=" . urlencode("Certificate added successfully."));
            exit;
        } else {
            if (mysqli_errno($conn) == 1062) {
                $error = "A certificate with this code already exists.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Add Certificate | CertPro Admin</title>
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
            <h1>Add Certificate</h1>
            <p>Create a new certificate record</p>
        </div>
    </div>

    <div class="form-card">
        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="field">
                <label>Certificate Code</label>
                <input type="text" name="certificate_code" value="<?php echo htmlspecialchars($values['certificate_code']); ?>" placeholder="e.g. CERT-2026-001" required>
            </div>
            <div class="field">
                <label>Student Name</label>
                <input type="text" name="student_name" value="<?php echo htmlspecialchars($values['student_name']); ?>" placeholder="Full name" required>
            </div>
            <div class="field">
                <label>Program Name</label>
                <input type="text" name="program_name" value="<?php echo htmlspecialchars($values['program_name']); ?>" placeholder="e.g. Frontend Development" required>
            </div>
            <div class="field">
                <label>Issue Date</label>
                <input type="date" name="issue_date" value="<?php echo htmlspecialchars($values['issue_date']); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Certificate</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
