<?php
require_once "auth.php";
require_once "db.php";

$search = trim($_GET['q'] ?? '');

if ($search !== '') {
    $like = "%" . $search . "%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM certificates WHERE certificate_code LIKE ? OR student_name LIKE ? OR program_name LIKE ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM certificates ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Dashboard | CertPro</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="admin-style.css">
</head>
<body>

<div class="topbar">
    <div class="brand"><span class="dot"></span> CertPro Admin</div>
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="profile.php" class="btn btn-outline btn-sm">Account Settings</a>
        <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
    </div>
</div>

<div class="container">

    <div class="page-header">
        <div>
            <h1>Certificates</h1>
            <p>Manage all issued certificates</p>
        </div>
        <a href="add.php" class="btn btn-primary btn-sm">+ Add Certificate</a>
    </div>

    <form method="GET" style="margin-bottom:20px;">
        <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by code, name, or program..." style="max-width:360px;">
    </form>

    <?php if (isset($_GET['msg'])): ?>
        <div class="success-msg"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Student</th>
                    <th>Program</th>
                    <th>Issue Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><span class="code-pill"><?php echo htmlspecialchars($row['certificate_code']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['program_name']); ?></td>
                            <td><?php echo $row['issue_date'] ? date('d M Y', strtotime($row['issue_date'])) : '—'; ?></td>
                            <td>
                                <div class="actions-cell">
                                    <a href="edit.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-outline btn-sm">Edit</a>
                                    <a href="delete.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete certificate <?php echo htmlspecialchars($row['certificate_code']); ?>? This cannot be undone.');">
                                       Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">No certificates found.</div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
