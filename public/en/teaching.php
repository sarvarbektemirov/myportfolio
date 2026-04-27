<?php
include_once("init.php");
include_once("sarlavha.php");
include_once("navbar.php");
include_once("sidebar.php")
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
            <h2 class="modern-title"><?= $lang === 'en' ? 'Teaching materials' : 'O\'quv materiallari' ?></h2>
        </div>

        <div class="modern-body">
            <div class="text-center py-5">
                <div class="mb-4" style="color: var(--primary-color); opacity: 0.2;">
                    <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                </div>
                <h3 class="modern-title" style="font-size: 1.5rem; margin-bottom: 15px;">
                    <?= $lang === 'en' ? 'Under Preparation' : 'Tayyorlanmoqda' ?>
                </h3>
                <p class="modern-text px-lg-5">
                    <?= $lang === 'en' 
                        ? 'Materials for this section are being organized and will be available soon. Please check back later for updates on curriculum, lectures, and academic resources.' 
                        : 'Ushbu bo\'lim uchun materiallar saralanmoqda va tez orada tayyor bo\'ladi. O\'quv dasturi, ma\'ruzalar va akademik resurslardagi yangiliklarni bilish uchun keyinroq qaytib ko\'ring.' ?>
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
            <?= $lang === 'en' ? 'Academic resource center' : 'Akademik resurslar markazi' ?>
        </div>
    </div>
</div>

<?php include_once("footer.php"); ?>
<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
