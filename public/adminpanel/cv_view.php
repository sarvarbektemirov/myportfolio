<?php
include_once('db.php');
if (empty($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)$_SESSION['id'];
$username = $_SESSION['username'] ?? '';

// Fetch All Data
$header = $link->query("SELECT * FROM header WHERE user_id = $uid LIMIT 1")->fetch_assoc();
$home = $link->query("SELECT * FROM home WHERE user_id = $uid LIMIT 1")->fetch_assoc();
$education = $link->query("SELECT * FROM talim WHERE user_id = $uid ORDER BY id DESC");
$experience = $link->query("SELECT * FROM nashrlar WHERE user_id = $uid ORDER BY boshlanish DESC");
$publications = $link->query("SELECT * FROM publication WHERE user_id = $uid ORDER BY yil DESC");
$footer = $link->query("SELECT * FROM footer WHERE user_id = $uid LIMIT 1")->fetch_assoc();

// URLs
$portfolio_url = "http://" . $_SERVER['HTTP_HOST'] . "/en/home.php?u=" . $username;
$landing_url = "http://" . $_SERVER['HTTP_HOST'];

$qr_portfolio = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($portfolio_url);
$qr_landing = "https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=" . urlencode($landing_url);

// Language Handling
$lang = $_GET['lang'] ?? 'uz';
$is_en = ($lang === 'en');

$labels = [
    'contact' => $is_en ? 'Contact' : "Bog'lanish",
    'portfolio' => $is_en ? 'My Portfolio' : "Mening Portfoliom",
    'platform' => $is_en ? 'Platform' : "Platforma",
    'created_at' => $is_en ? 'Created at' : "tizimida yaratildi",
    'education' => $is_en ? 'Education' : "Ta'lim",
    'experience' => $is_en ? 'Experience' : "Ish tajribasi",
    'publications' => $is_en ? 'Publications' : "Ilmiy ishlar va nashrlar",
    'back' => $is_en ? 'Back to Panel' : "Panelga qaytish",
    'download' => $is_en ? 'Download CV' : "CV yuklab olish",
    'download_date' => $is_en ? 'Download Date' : "CV yuklanish sanasi",
    'authors' => $is_en ? 'Authors' : "Mualliflar",
    'now' => $is_en ? 'Present' : "Hozirgi",
    'expert' => $is_en ? 'Expert' : "Mutaxassis"
];

// Safety defaults
$user_full_name = htmlspecialchars($is_en ? (($header['familiya_en'] ?? '') . ' ' . ($header['ism_en'] ?? '')) : (($header['familiya'] ?? '') . ' ' . ($header['ism'] ?? '')));
$user_job = htmlspecialchars($is_en ? ($home['kasbi_en'] ?? $labels['expert']) : ($home['kasbi'] ?? $labels['expert']));
$user_image = ($home['rasm'] ?? '') ? '../files/' . $home['rasm'] : 'rasmlar/default_user.png';
$user_email = htmlspecialchars($footer['email'] ?? ($header['email'] ?? ($_SESSION['email'] ?? '')));
$user_phone = htmlspecialchars($footer['tel'] ?? ($header['tel'] ?? ''));
$user_address = htmlspecialchars($footer['manzil'] ?? '');
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>CV - <?= htmlspecialchars($header['ism'] . ' ' . $header['familiya']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --europass-blue: #004494;
            --europass-light: #f0f5fa;
            --text-dark: #333;
            --text-muted: #666;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e0e0e0;
            margin: 0;
            padding: 20px;
            color: var(--text-dark);
        }
        .cv-container {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            display: flex;
        }
        /* Sidebar */
        .cv-sidebar {
            width: 30%;
            background: var(--europass-light);
            padding: 40px 20px;
            border-right: 1px solid #ddd;
        }
        .cv-main {
            width: 70%;
            padding: 40px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 15px;
            object-fit: cover;
            margin-bottom: 20px;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .sidebar-section {
            margin-bottom: 30px;
        }
        .sidebar-section h3 {
            color: var(--europass-blue);
            font-size: 1rem;
            text-transform: uppercase;
            border-bottom: 2px solid var(--europass-blue);
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .contact-item {
            font-size: 0.85rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .contact-item i {
            color: var(--europass-blue);
            width: 20px;
        }
        /* Main Content */
        .cv-header h1 {
            font-size: 2.2rem;
            margin: 0;
            color: var(--europass-blue);
            line-height: 1.1;
            word-wrap: break-word;
        }
        .cv-header h2 {
            font-size: 1.2rem;
            font-weight: 400;
            color: var(--text-muted);
            margin: 5px 0 20px 0;
        }
        .section-title {
            color: var(--europass-blue);
            font-size: 1.3rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            margin: 30px 0 15px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .item {
            margin-bottom: 20px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .item-header {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
        }
        .item-sub {
            color: var(--europass-blue);
            font-size: 0.9rem;
            font-style: italic;
        }
        .item-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 5px;
            line-height: 1.4;
        }
        .qr-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #ccc;
        }
        .qr-item {
            text-align: center;
            font-size: 0.7rem;
            color: var(--text-muted);
        }
        .qr-item img {
            width: 80px;
            height: 80px;
            margin-bottom: 5px;
        }
        /* Print Styles */
        @media print {
            body { background: white; padding: 0; }
            .cv-container { box-shadow: none; margin: 0; width: 100%; border: none; }
            .no-print { display: none !important; }
            @page {
                margin: 0;
                size: auto;
            }
        }
        .no-print { display: block; }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 25px;
            background: var(--europass-blue);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 12px 25px;
            background: #666;
            color: white;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            z-index: 1000;
        }
        .lang-switch {
            position: fixed;
            top: 80px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .lang-btn {
            padding: 8px 15px;
            background: white;
            border: 2px solid var(--europass-blue);
            color: var(--europass-blue);
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.8rem;
        }
        .lang-btn.active {
            background: var(--europass-blue);
            color: white;
        }
    </style>
</head>
<body>

    <a href="admin.php" class="back-btn no-print"><i class="fas fa-arrow-left"></i> <?= $labels['back'] ?></a>
    
    <div class="lang-switch no-print">
        <a href="?lang=uz" class="lang-btn <?= $lang === 'uz' ? 'active' : '' ?>">O'ZBEK</a>
        <a href="?lang=en" class="lang-btn <?= $lang === 'en' ? 'active' : '' ?>">ENGLISH</a>
    </div>

    <button onclick="window.print()" class="print-btn no-print"><i class="fas fa-download"></i> <?= $labels['download'] ?></button>

    <div class="cv-container">
        <!-- Sidebar -->
        <div class="cv-sidebar">
            <img src="<?= $user_image ?>" class="profile-img" alt="Profile">
            
            <div class="sidebar-section">
                <h3><?= $labels['contact'] ?></h3>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <span><?= $user_email ?></span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <span><?= $user_phone ?></span>
                </div>
            </div>

            <div class="qr-container">
                <div class="qr-item">
                    <img src="<?= $qr_portfolio ?>" alt="Portfolio QR">
                    <div><?= $labels['portfolio'] ?></div>
                </div>
                <div class="qr-item">
                    <img src="<?= $qr_landing ?>" alt="Platform QR">
                    <div>MyPortfolio.uz</div>
                </div>
            </div>
            
            <div style="margin-top: 50px; text-align: center; opacity: 0.5; font-size: 0.8rem;">
                <strong>MyPortfolio</strong> <?= $labels['created_at'] ?><br>
                <span style="font-size: 0.7rem;"><?= $labels['download_date'] ?>: <?= date('d.m.Y') ?></span>
            </div>
        </div>

        <!-- Main Content -->
        <div class="cv-main">
            <div class="cv-header">
                <h1><?= $user_full_name ?></h1>
                <h2><?= $user_job ?></h2>
                <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($is_en ? ($home['malumot_en'] ?? ($home['haqida_en'] ?? '')) : ($home['malumot_uz'] ?? ($home['haqida'] ?? '')))) ?>
                </p>
            </div>

            <!-- Ta'lim -->
            <div class="section-title">
                <i class="fas fa-graduation-cap"></i> <?= mb_strtoupper($labels['education'], 'UTF-8') ?>
            </div>
            <?php while($edu = $education->fetch_assoc()): ?>
            <div class="item">
                <div class="item-header">
                    <span><?= mb_strtoupper($is_en ? ($edu['bosqich_en'] ?? $edu['bosqich']) : $edu['bosqich'], 'UTF-8') ?></span>
                </div>
                <div class="item-desc"><?= nl2br(htmlspecialchars($is_en ? ($edu['tavsif_en'] ?? $edu['tavsif_uz']) : $edu['tavsif_uz'])) ?></div>
            </div>
            <?php endwhile; ?>

            <!-- Ish Tajribasi -->
            <div class="section-title">
                <i class="fas fa-briefcase"></i> <?= mb_strtoupper($labels['experience'], 'UTF-8') ?>
            </div>
            <?php while($exp = $experience->fetch_assoc()): ?>
            <div class="item">
                <div class="item-header">
                    <span><?= htmlspecialchars($is_en ? ($exp['ish_joyi_en'] ?? ($exp['nomi_en'] ?? $exp['ish_joyi_uz'])) : ($exp['ish_joyi_uz'] ?? $exp['nomi'])) ?></span>
                    <span style="font-size: 0.8rem; font-weight: 400;">
                        <?= date('Y.m', strtotime($exp['boshlanish'])) ?> - 
                        <?= $exp['hozirgi'] ? $labels['now'] : date('Y.m', strtotime($exp['tugash'])) ?>
                    </span>
                </div>
                <div class="item-sub"><?= htmlspecialchars($is_en ? ($exp['lavozim_en'] ?? $exp['lavozim_uz']) : $exp['lavozim_uz']) ?></div>
            </div>
            <?php endwhile; ?>

            <!-- Nashrlar -->
            <div class="section-title">
                <i class="fas fa-book"></i> <?= mb_strtoupper($labels['publications'], 'UTF-8') ?>
            </div>
            <?php while($pub = $publications->fetch_assoc()): ?>
            <div class="item">
                <div class="item-header">
                    <span><?= htmlspecialchars($pub['nom']) ?></span>
                    <span><?= htmlspecialchars($pub['yil']) ?></span>
                </div>
                <div class="item-sub"><?= htmlspecialchars($pub['jurnal']) ?></div>
                <div class="item-desc"><?= $labels['authors'] ?>: <?= htmlspecialchars($pub['muallif']) ?></div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
