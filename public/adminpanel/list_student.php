<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$result = $link->query("SELECT * FROM students WHERE user_id = $uid ORDER BY toifa ASC, id DESC");

$toifa_nomi = [
    'toifa_1' => 'BMI',
    'toifa_2' => 'Magistr',
    'toifa_3' => 'Boshqa',
];
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studentlar ro'yxati</title>
    <link rel="icon" href="rasmlar/logo.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
    <?php include_once("menu.php"); ?>

    <?php if (isset($_GET['res']) && $_GET['res'] === 'ok'): ?>
        <div class="alert alert-success animatsiya1 container mt-4 border-0 shadow-sm">
            <i class="fa-solid fa-circle-check me-2"></i> Ma'lumotlar muvaffaqiyatli saqlandi!
        </div>
    <?php endif; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <div>
            <h2 class="mb-0 fw-bold"><i class="fa-solid fa-user-graduate text-primary me-2"></i> Studentlar</h2>
            <p class="text-muted small mb-0">Barcha talabalar ma'lumotlari ro'yxati</p>
        </div>
        <a href="add_student.php" class="btn btn-submit px-4">
            <i class="fa-solid fa-plus me-2"></i> Yangi qo'shish
        </a>
    </div>

    <?php if (isset($_GET['xabar'])): ?>
        <div class="animatsiya1 mb-4">
            <?php if ($_GET['xabar'] === 'ok'): ?>
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                    <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                    <div>Ma'lumotlar muvaffaqiyatli saqlandi!</div>
                </div>
            <?php elseif ($_GET['xabar'] === 'ochirildi'): ?>
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                    <i class="fa-solid fa-trash-can fs-4 me-3"></i>
                    <div>Ma'lumot muvaffaqiyatli o'chirildi.</div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="form-card animatsiya1 p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Rasm</th>
                        <th>Ism Familiya</th>
                        <th>Toifa</th>
                        <th style="width: 40%;">Qisqa ma'lumot</th>
                        <th class="text-center pe-4">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result && $result->num_rows > 0):
                    $i = 1;
                    while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-4 text-muted small"><?= $i++ ?></td>
                        <td>
                            <?php if (!empty($row['rasm'])): ?>
                                <img src="../student_rasmlar/<?= htmlspecialchars($row['rasm']) ?>" class="table-img" alt="Student">
                            <?php else: ?>
                                <div class="table-img bg-light d-flex align-items-center justify-content-center border">
                                    <i class="fa-solid fa-user text-muted opacity-50"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold"><?= htmlspecialchars($row['ism']) ?></td>
                        <td>
                            <span class="badge bg-light text-primary border border-primary-subtle badge-toifa">
                                <i class="fa-solid fa-tag me-1 small"></i>
                                <?= htmlspecialchars($toifa_nomi[$row['toifa']] ?? $row['toifa']) ?>
                            </span>
                        </td>
                        <td class="text-muted small">
                            <?= htmlspecialchars(mb_strimwidth($row['qisqa_malumot_uz'] ?? '', 0, 80, "...")) ?>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="edit_student.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-outline-warning icon-link" title="Tahrirlash">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="delete_student.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger icon-link" title="O'chirish">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open d-block fs-1 mb-2 opacity-50"></i>
                            Hali studentlar qo'shilmagan
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

