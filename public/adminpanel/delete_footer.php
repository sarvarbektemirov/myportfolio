<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$surov = $link->query("SELECT * FROM footer WHERE user_id = $uid");
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer ma'lumotlari - O'chirish</title>
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
            border: none;
            padding: 15px;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 12px 15px;
            vertical-align: middle;
            color: #334155;
        }
        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            cursor: pointer;
        }
        .icon-link-sm {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .icon-link-sm:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<?php include_once("menu.php"); ?>

<div class="container-fluid py-5 px-4 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-circle-info text-primary me-2"></i> Footer ma'lumotlari ro'yxati</h2>
        <div class="text-muted small"><i class="fa-solid fa-check-square me-1"></i> O'chirish uchun qatorlarni tanlang</div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-card animatsiya1 p-0 overflow-hidden">
                <form action="delete_footer1.php" method="post" onsubmit="return tasdiqlash()">
                    <div class="table-responsive table-container">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:60px;"><i class="fa-solid fa-list-check"></i></th>
                                    <th>ORCID</th>
                                    <th>CV</th>
                                    <th>Telegram</th>
                                    <th>WhatsApp</th>
                                    <th>Scopus</th>
                                    <th>Scholar</th>
                                    <th>University</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
include_once('db.php'); if ($surov->num_rows > 0): ?>
                                    <?php while ($satr = $surov->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="del[]"
                                                       value="<?= $satr['id'] ?>"
                                                       class="form-check-input">
                                            </td>
                                            <td><code class="text-success"><?= htmlspecialchars($satr['orcid'] ?? '') ?></code></td>
                                            <td>
                                                <?php if (!empty($satr['cv_fayl'])): ?>
                                                    <a href="../files/<?= htmlspecialchars($satr['cv_fayl']) ?>"
                                                       target="_blank" class="btn btn-sm btn-outline-danger icon-link-sm" title="CV ko'rish">
                                                        <i class="fa-solid fa-file-pdf"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">yo'q</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($satr['tg_link'])): ?>
                                                    <a href="<?= htmlspecialchars($satr['tg_link']) ?>"
                                                       target="_blank" class="btn btn-sm btn-outline-primary icon-link-sm">
                                                        <i class="fa-brands fa-telegram"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($satr['wa_link'])): ?>
                                                    <a href="<?= htmlspecialchars($satr['wa_link']) ?>"
                                                       target="_blank" class="btn btn-sm btn-outline-success icon-link-sm">
                                                        <i class="fa-brands fa-whatsapp"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($satr['scopus_link'])): ?>
                                                    <a href="<?= htmlspecialchars($satr['scopus_link']) ?>"
                                                       target="_blank" class="btn btn-sm btn-outline-warning icon-link-sm">
                                                        <i class="fa-solid fa-magnifying-glass"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($satr['scholar_link'])): ?>
                                                    <a href="<?= htmlspecialchars($satr['scholar_link']) ?>"
                                                       target="_blank" class="btn btn-sm btn-outline-info icon-link-sm">
                                                        <i class="fa-solid fa-graduation-cap"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($satr['university_link'])): ?>
                                                    <a href="<?= htmlspecialchars($satr['university_link']) ?>"
                                                       target="_blank" class="btn btn-sm btn-outline-dark icon-link-sm">
                                                        <i class="fa-solid fa-university"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-database d-block fs-1 mb-2 opacity-50"></i>
                                            Ma'lumotlar mavjud emas
                                        </td>
                                    </tr>
                                <?php endif; ?>
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

