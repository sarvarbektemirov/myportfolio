<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$d = "SELECT * FROM footer WHERE user_id = $uid";
$surov = $link->query($d);
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer ma'lumotlari</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
<?php include_once("menu.php"); ?>

    <?php if (isset($_GET['res']) && $_GET['res'] === 'ok' || isset($_GET['xabar']) && $_GET['xabar'] === 'ok'): ?>
        <div class="alert alert-success animatsiya1 container mt-4 border-0 shadow-sm">
            <i class="fa-solid fa-circle-check me-2"></i> Ma'lumotlar muvaffaqiyatli tahrirlandi!
        </div>
    <?php endif; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <div>
            <h2 class="mb-0 fw-bold"><i class="fa-solid fa-address-book text-primary me-2"></i> Footer ma'lumotlari</h2>
            <p class="text-muted small mb-0">Bog'lanish va ijtimoiy tarmoq havolalari boshqaruvi</p>
        </div>
    </div>

    <div class="form-card animatsiya1 p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Amallar</th>
                        <th>ORCID ID</th>
                        <th>Profil va Bog'lanish</th>
                        <th>CV Fayl</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
include_once('db.php'); while ($satr = $surov->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-4">
                                <form action="edit_footer_check.php" method="post">
                                    <input type="hidden" name="id" value="<?= $satr['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning icon-link" title="Tahrirlash">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <span class="badge border px-2 py-1">
                                    <i class="fa-solid fa-fingerprint text-success me-1"></i>
                                    <?= htmlspecialchars($satr['orcid'] ?? '—') ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <?php if (!empty($satr['tg_link'])): ?>
                                        <a href="<?= htmlspecialchars($satr['tg_link']) ?>" target="_blank" class="social-icon-btn bg-primary-subtle text-primary" title="Telegram">
                                            <i class="fa-brands fa-telegram"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($satr['wa_link'])): ?>
                                        <a href="<?= htmlspecialchars($satr['wa_link']) ?>" target="_blank" class="social-icon-btn bg-success-subtle text-success" title="WhatsApp">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($satr['scopus_link'])): ?>
                                        <a href="<?= htmlspecialchars($satr['scopus_link']) ?>" target="_blank" class="social-icon-btn bg-warning-subtle text-warning" title="Scopus">
                                            <i class="fa-solid fa-book"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($satr['scholar_link'])): ?>
                                        <a href="<?= htmlspecialchars($satr['scholar_link']) ?>" target="_blank" class="social-icon-btn bg-info-subtle text-info" title="Google Scholar">
                                            <i class="fa-solid fa-graduation-cap"></i>
                                        </a>
                                    <?php endif; ?>

                                    <?php if (!empty($satr['university_link'])): ?>
                                        <a href="<?= htmlspecialchars($satr['university_link']) ?>" target="_blank" class="social-icon-btn bg-dark-subtle text-dark" title="University">
                                            <i class="fa-solid fa-university"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($satr['cv_fayl'])): ?>
                                    <a href="<?= htmlspecialchars($satr['cv_fayl']) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-file-pdf me-1"></i> CV Ko'rish
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Yuklanmagan</span>
                                <?php endif; ?>
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

