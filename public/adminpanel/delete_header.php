<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$surov = $link->query("SELECT * FROM header WHERE user_id = $uid");
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header ma'lumotlari - O'chirish</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <style>
        .table-container {
            border-radius: 12px;
            overflow: hidden;
        }
        .table thead th {
            background-color: #1e293b;
            color: #f8fafc;
            font-weight: 500;
            border: none;
            padding: 15px;
        }
        .table tbody td {
            padding: 12px 15px;
            color: #334155;
            vertical-align: middle;
        }
        .checkbox-cell {
            width: 60px;
            text-align: center;
        }
        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include_once("menu.php"); ?>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-address-card text-primary me-2"></i> Header ma'lumotlari ro'yxati</h2>
        <div class="text-muted small"><i class="fa-solid fa-circle-info me-1"></i> O'chirish uchun elementlarni tanlang</div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-card animatsiya1 p-0">
                <form action="delete_header1.php" method="post" onsubmit="return tasdiqlash()">
                    <div class="table-responsive table-container">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="checkbox-cell"><i class="fa-solid fa-check-double"></i></th>
                                    <th>Familiya</th>
                                    <th>Ism</th>
                                    <th>Daraja</th>
                                    <th>Tel</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
include_once('db.php'); if ($surov->num_rows > 0): ?>
                                    <?php while ($satr = $surov->fetch_assoc()): ?>
                                        <tr>
                                            <td class="checkbox-cell">
                                                <input type="checkbox" name="del[]"
                                                       value="<?= $satr['id'] ?>"
                                                       class="form-check-input">
                                            </td>
                                            <td class="fw-bold"><?= htmlspecialchars($satr['familiya']) ?></td>
                                            <td><?= htmlspecialchars($satr['ism']) ?></td>
                                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($satr['daraja']) ?></span></td>
                                            <td><code class="text-primary"><?= htmlspecialchars($satr['tel'] ?? '') ?></code></td>
                                            <td><?= htmlspecialchars($satr['email']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php
include_once('db.php'); else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-folder-open d-block fs-1 mb-2 opacity-50"></i>
                                            Ma'lumotlar topilmadi
                                        </td>
                                    </tr>
                                <?php
include_once('db.php'); endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-top bg-light text-center">
                        <button type="submit" class="btn btn-submit px-5 shadow">
                            <i class="fa-solid fa-trash-can me-2"></i> Tanlanganlarni o'chirish
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.js"></script>
<script>
function tasdiqlash() {
    const tanlangan = document.querySelectorAll('input[name="del[]"]:checked');
    if (tanlangan.length === 0) {
        alert('Iltimos, o\'chirish uchun kamida bitta qatorni tanlang!');
        return false;
    }
    return confirm('Tanlangan ' + tanlangan.length + ' ta ma\'lumotni o\'chirmoqchimisiz? Bu amalni qaytarib bo\'lmaydi!');
}
</script>
</body>
</html>

