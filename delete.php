<?php
require_once "auth.php";
require_once "db.php";

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM certificates WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
}

header("Location: index.php?msg=" . urlencode("Certificate deleted."));
exit;
