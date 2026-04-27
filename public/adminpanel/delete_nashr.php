<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);


if (empty($_GET['id'])) {
    header("Location: list_nashr.php");
    exit;
}

$id = (int)$_GET['id'];

// Tasdiqlangan bo'lsa - O'chirish
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $del = $link->prepare("DELETE FROM nashrlar WHERE id = ? AND user_id = ?");
    $del->bind_param("ii", $id, $uid);
    if ($del->execute()) {
        $del->close();
        header("Location: list_nashr.php?res=ok");
        exit;
    } else {
        echo "<div class='alert alert-danger animatsiya1 container mt-4 border-0 shadow-sm'><i class='fa-solid fa-triangle-exclamation me-2'></i> O'chirishda xatolik yuz berdi!</div>";
        echo "<div class='container mt-2'><a href='list_nashr.php' class='btn btn-secondary btn-back'><i class='fa-solid fa-arrow-left me-1'></i> Orqaga qaytish</a></div>";
        $del->close();
        exit;
    }
}

// Tasdiqlash oynasini ko'rsatish
$stmt = $link->prepare("SELECT lavozim_uz, ish_joyi_uz FROM nashrlar WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    header("Location: list_nashr.php");
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
            <div class="col-12 col-md-6">
                <div class="form-card animatsiya1 text-center p-5 shadow-lg border-0">
                    <div class="mb-4">
                        <i class="fa-solid fa-briefcase text-danger" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="fw-bold mb-3">Nashrni o'chirish?</h2>
                    <p class="text-muted fs-5 mb-4">Haqiqatan ham ushbu nashr ma'lumotini o'chirib tashlamoqchimisiz? Bu amalni qaytarib bo'lmaydi.</p>

                    <div class="p-4 bg-light rounded-4 mb-4 border shadow-sm">
                        <div class="fw-bold fs-5 text-primary"><?= htmlspecialchars($row['lavozim_uz']) ?></div>
                        <div class="text-muted mt-1"><?= htmlspecialchars($row['ish_joyi_uz']) ?></div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="delete_nashr.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger btn-lg px-4 shadow">
                            <i class="fa-solid fa-trash-can me-2"></i> Ha, o'chirilsin
                        </a>
                        <a href="list_nashr.php" class="btn btn-light btn-lg px-4 border shadow-sm">
                            <i class="fa-solid fa-xmark me-2"></i> Bekor qilish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

