<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

if ($id <= 0) {
    header("Location: list_publication.php");
    exit;
}


if (empty($_GET['id'])) {
    header("Location: list_publication.php");
    exit;
}

$id = (int)$_GET['id'];

// Tasdiqlangan bo'lsa - O'chirish
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmt = $link->prepare("SELECT fayl1, fayl2, fayl3 FROM publication WHERE id=? AND user_id=? LIMIT 1");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/my_files/";
        if (!empty($row['fayl1']) && file_exists($upload_dir . $row['fayl1'])) unlink($upload_dir . $row['fayl1']);
        if (!empty($row['fayl2']) && file_exists($upload_dir . $row['fayl2'])) unlink($upload_dir . $row['fayl2']);
        if (!empty($row['fayl3']) && file_exists($upload_dir . $row['fayl3'])) unlink($upload_dir . $row['fayl3']);

        $del = $link->prepare("DELETE FROM publication WHERE id=? AND user_id=?");
        $del->bind_param("ii", $id, $uid);
        $del->execute();
        $del->close();
    }
    header("Location: list_publication.php?res=ok");
    exit;
}

// Tasdiqlash oynasini ko'rsatish
$stmt = $link->prepare("SELECT nom, jurnal FROM publication WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    header("Location: list_publication.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nashrni o'chirish</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body class="bg-light">
    <?php include_once("menu.php"); ?>
    
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-7">
                <div class="form-card animatsiya1 text-center p-5 shadow-lg border-0">
                    <div class="mb-4">
                        <i class="fa-solid fa-book-journal-whills text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="fw-bold mb-3">Nashrni o'chirish?</h2>
                    <p class="text-muted fs-5 mb-4">Haqiqatan ham ushbu nashr (maqola) ma'lumotlarini o'chirib tashlamoqchimisiz? Biriktirilgan fayllar ham o'chib ketadi.</p>

                    <div class="p-4 bg-light rounded-4 mb-4 border shadow-sm text-start">
                        <div class="fw-bold fs-5 text-dark mb-1"><?= htmlspecialchars($row['nom']) ?></div>
                        <?php if (!empty($row['jurnal'])): ?>
                            <div class="text-muted small"><i class="fa-solid fa-newspaper me-1"></i> <?= htmlspecialchars($row['jurnal']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="delete_publication.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger btn-lg px-4 shadow">
                            <i class="fa-solid fa-trash-can me-2"></i> Ha, o'chirilsin
                        </a>
                        <a href="list_publication.php" class="btn btn-light btn-lg px-4 border shadow-sm">
                            <i class="fa-solid fa-xmark me-2"></i> Bekor qilish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

