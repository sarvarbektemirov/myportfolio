<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$b = !empty($_POST['b']) ? htmlspecialchars($_POST['b'], ENT_QUOTES) : '';
$c = !empty($_POST['c']) ? htmlspecialchars($_POST['c'], ENT_QUOTES) : '';
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

$d = "SELECT * FROM header WHERE user_id = $uid AND ism LIKE '%$b%' AND familiya LIKE '%$c%'";
$surov = $link->query($d);
?>
<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header ma'lumotlari</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
    <?php include_once("menu.php"); ?>



<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <div>
            <h2 class="mb-0 fw-bold"><i class="fa-solid fa-id-card text-primary me-2"></i> Header ma'lumotlari</h2>
            <p class="text-muted small mb-0">Sarlavha qismidagi shaxsiy ma'lumotlar boshqaruvi</p>
        </div>
    </div>

    <?php
include_once('db.php'); if (isset($_GET['xabar']) && $_GET['xabar'] == 'ok'): ?>
        <div class="animatsiya1 mb-4">
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center">
                <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                <div>Ma'lumotlar muvaffaqiyatli tahrirlandi!</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-card animatsiya1 p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Familiya</th>
                        <th>Ism</th>
                        <th>Daraja</th>
                        <th>Bog'lanish</th>
                        <th class="text-center pe-4">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($satr = $surov->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?= htmlspecialchars($satr['familiya'] ?? '') ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($satr['ism'] ?? '') ?></td>
                            <td><span class="badge bg-light text-primary border border-primary-subtle px-2 py-1"><?= htmlspecialchars($satr['daraja'] ?? '') ?></span></td>
                            <td>
                                <div class="small">
                                    <div class="text-muted mb-1"><i class="fa-solid fa-phone me-1 opacity-50"></i> <?= htmlspecialchars($satr['tel'] ?? '') ?></div>
                                    <div class="text-muted"><i class="fa-solid fa-envelope me-1 opacity-50"></i> <?= htmlspecialchars($satr['email'] ?? '') ?></div>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <form action="edit_header_check.php" method="post">
                                    <input type="hidden" name="id" value="<?= $satr['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning icon-link" title="Tahrirlash">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>

