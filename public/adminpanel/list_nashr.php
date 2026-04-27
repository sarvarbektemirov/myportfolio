<?php
include_once("db.php");
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$nashrlar = [];
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$result = $link->query("SELECT * FROM nashrlar WHERE user_id = $uid ORDER BY boshlanish DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nashrlar[] = $row;
    }
}

$oylar = ['','Yanvar','Fevral','Mart','Aprel','May','Iyun',
          'Iyul','Avgust','Sentabr','Oktabr','Noyabr','Dekabr'];
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nashrlar ro'yxati</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
<?php include_once("menu.php"); ?>

    <?php if (isset($_GET['res']) || isset($_GET['xabar'])): ?>
        <div class="animatsiya1 container mt-4 mb-4 p-0">
            <?php if (($_GET['res'] ?? '') === 'ok' || ($_GET['xabar'] ?? '') === 'ok'): ?>
                <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                    <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                    <div>Ma'lumotlar muvaffaqiyatli saqlandi!</div>
                </div>
            <?php elseif (($_GET['xabar'] ?? '') === 'ochirildi'): ?>
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                    <i class="fa-solid fa-trash-can fs-4 me-3"></i>
                    <div>Ma'lumot muvaffaqiyatli o'chirildi.</div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <div>
            <h2 class="mb-0 fw-bold"><i class="fa-solid fa-briefcase text-primary me-2"></i> Tajriba va Nashrlar</h2>
            <p class="text-muted small mb-0">Mehnat faoliyati va tajribalar ro'yxati</p>
        </div>
        <a href="add_nashr.php" class="btn btn-submit px-4">
            <i class="fa-solid fa-plus me-2"></i> Yangi qo'shish
        </a>
    </div>

    <?php if (!empty($nashrlar)): ?>
    <div class="form-card animatsiya1 p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Tur</th>
                        <th>Lavozim</th>
                        <th>Ish joyi</th>
                        <th>Muddat</th>
                        <th class="text-center pe-4">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nashrlar as $i => $n): ?>
                        <?php
                            $b_dt = !empty($n['boshlanish']) ? new DateTime($n['boshlanish']) : null;
                            $b_oy = $b_dt ? (int)$b_dt->format('n') : 0;
                            $b_yil = $b_dt ? $b_dt->format('Y') : '';
                            $bosh = ($b_oy > 0) ? ($oylar[$b_oy] . ' ' . $b_yil) : 'Nomalum';

                            if ($n['hozirgi']) {
                                $tug = '<span class="text-success fw-bold">Hozirgi kungacha</span>';
                            } else {
                                $t_dt  = !empty($n['tugash']) ? new DateTime($n['tugash']) : null;
                                $t_oy  = $t_dt ? (int)$t_dt->format('n') : 0;
                                $t_yil = $t_dt ? $t_dt->format('Y') : '';
                                $tug   = ($t_oy > 0) ? ($oylar[$t_oy] . ' ' . $t_yil) : 'Nomalum';
                            }
                        ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?= $i + 1 ?></td>
                            <td>
                                <?php if ($n['tur'] === 'asosiy'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Asosiy</span>
                                <?php else: ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Qo'shimcha</span>
                                <?php endif; ?>
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($n['lavozim_uz']) ?></td>
                            <td><i class="fa-solid fa-building text-muted me-1 small"></i> <?= htmlspecialchars($n['ish_joyi_uz']) ?></td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="date-badge w-fit"><i class="fa-regular fa-calendar-check me-1"></i> <?= $bosh ?></span>
                                    <span class="date-badge w-fit"><i class="fa-regular fa-calendar-xmark me-1"></i> <?= $tug ?></span>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit_nashr.php?id=<?= $n['id'] ?>" 
                                       class="btn btn-sm btn-outline-warning icon-link" title="Tahrirlash">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="delete_nashr.php?id=<?= $n['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger icon-link" title="O'chirish">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
        <div class="form-card animatsiya1 text-center py-5">
            <i class="fa-solid fa-briefcase fs-1 text-muted opacity-50 mb-3 d-block"></i>
            <h5 class="text-muted">Ma'lumotlar topilmadi!</h5>
            <a href="add_nashr.php" class="btn btn-submit mt-3">Yangi qo'shish</a>
        </div>
    <?php endif; ?>
</div>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>

