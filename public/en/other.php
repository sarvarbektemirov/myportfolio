<?php
include_once("init.php");
include_once("sarlavha.php");
include_once("navbar.php");
include_once("sidebar.php");
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio</title>
    <link rel="icon" href="../rasmlar/my_favicon.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css" />
  <link rel="stylesheet" href="../css/boots_style.css">
  <link rel="stylesheet" href="../css/modern_style.css">
</head>
<body>

<div class="container my-5">
    <div class="modern-card">
        <div class="modern-header">
            <div class="modern-header-line"></div>
            <h2 class="modern-title"><?= $lang === 'en' ? 'Other activities' : 'Boshqa faoliyatlar' ?></h2>
        </div>

        <div class="modern-body">
            <div class="text-center py-5">
                <div class="mb-4" style="color: var(--primary-color); opacity: 0.2;">
                    <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                </div>
                <h3 class="modern-title" style="font-size: 1.5rem; margin-bottom: 15px;">
                    <?= $lang === 'en' ? 'Coming Soon' : 'Tez orada' ?>
                </h3>
                <p class="modern-text px-lg-5">
                    <?= $lang === 'en' 
                        ? 'This section will feature additional information, community involvement, and other professional interests. Stay tuned for updates.' 
                        : 'Ushbu bo\'limda qo\'shimcha ma\'lumotlar, ijtimoiy faollik va boshqa professional qiziqishlar o\'rin oladi. Yangilanishlarni kuzatib boring.' ?>
                </p>
                <div class="mt-4">
                    <a href="home.php" class="btn-submit-modern" style="text-decoration: none; display: inline-block;">
                        <?= $lang === 'en' ? 'Return Home' : 'Asosiy sahifa' ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="modern-footer">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="#aaa" stroke-width="1.5"/>
                <path d="M12 7v5l3 3" stroke="#aaa" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <?= $lang === 'en' ? 'Miscellaneous Information' : 'Turli xil ma\'lumotlar' ?>
        </div>
    </div>
</div>

<?php 
   include_once("footer.php");
?>
<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
