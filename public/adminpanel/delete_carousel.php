<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

if ($id <= 0) {
    header("Location: list_carousel.php");
    exit;
}


if (empty($_GET['id'])) {
    header("Location: list_carousel.php");
    exit;
}

$id = (int)$_GET['id'];

// Tasdiqlangan bo'lsa - O'chirish
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmt = $link->prepare("SELECT rasm FROM nashr_carousel WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $del = $link->prepare("DELETE FROM nashr_carousel WHERE id = ? AND user_id = ?");
        $del->bind_param("ii", $id, $uid);
        if ($del->execute()) {
            if (!empty($row['rasm'])) {
                $rasm_path = $_SERVER['DOCUMENT_ROOT'] . "/nashr_carousel/" . $row['rasm'];
                if (file_exists($rasm_path)) {
                    unlink($rasm_path);
                }
            }
        }
        $del->close();
    }
        header("Location: list_carousel.php?res=ok");
    exit;
}

// Tasdiqlash oynasini ko'rsatish
$stmt = $link->prepare("SELECT nom_uz, rasm FROM nashr_carousel WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    header("Location: list_carousel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carouselni o'chirish</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body class="bg-light">
    <?php include_once("menu.php"); ?>
    
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="form-card animatsiya1 text-center p-5 shadow-lg border-0">
                    <div class="mb-4">
                        <i class="fa-solid fa-images text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="fw-bold mb-3">Rasm o'chirilsinmi?</h2>
                    <p class="text-muted fs-5 mb-4">Haqiqatan ham ushbu karusel rasmini o'chirib tashlamoqchimisiz? Bu amalni qaytarib bo'lmaydi.</p>

                    <div class="p-3 bg-light rounded-4 mb-4 border shadow-sm">
                        <?php if (!empty($row['rasm'])): ?>
                            <img src="/nashr_carousel/<?= htmlspecialchars($row['rasm']) ?>" 
                                 class="rounded-3 mb-2 border shadow-sm" 
                                 style="width: 100%; height: 180px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="fw-bold fs-5 mt-2"><?= htmlspecialchars($row['nom_uz']) ?></div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="delete_carousel.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger btn-lg px-4 shadow">
                            <i class="fa-solid fa-trash-can me-2"></i> Ha, o'chirilsin
                        </a>
                        <a href="list_carousel.php" class="btn btn-light btn-lg px-4 border shadow-sm">
                            <i class="fa-solid fa-xmark me-2"></i> Bekor qilish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

