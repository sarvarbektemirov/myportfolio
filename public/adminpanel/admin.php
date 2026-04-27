<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

include_once("menu.php");

$uid = (int)$_SESSION['id'];

// Ma'lumotlar bazasi ulanishini tekshirish
if (!$link) {
    // Agar ulanish bo'lmasa, qayta kirishni so'rash yoki xatolik ko'rsatish
    session_destroy();
    header("Location: login.php?error=db_error");
    exit;
}

// Ma'lumotlar sonini olish
$counts = [];

$res = $link->query("SELECT COUNT(*) as c FROM students WHERE user_id = $uid");
$counts['student'] = $res ? $res->fetch_assoc()['c'] : 0;

$res = $link->query("SELECT COUNT(*) as c FROM talim WHERE user_id = $uid");
$counts['talim'] = $res ? $res->fetch_assoc()['c'] : 0;

$res = $link->query("SELECT COUNT(*) as c FROM publication WHERE user_id = $uid");
$counts['publication'] = $res ? $res->fetch_assoc()['c'] : 0;

$res = $link->query("SELECT COUNT(*) as c FROM nashrlar WHERE user_id = $uid");
$counts['nashr'] = $res ? $res->fetch_assoc()['c'] : 0;

// Sarlavhadagi (Header) ism-familiyani olish
$h_res = $link->query("SELECT ism, familiya FROM header WHERE user_id = $uid LIMIT 1");
$header_user = $h_res && $h_res->num_rows > 0 ? $h_res->fetch_assoc() : null;

// Agar header bo'lmasa, sessiondagi ismni ishlatamiz (u login/registerda yangilangan)
$display_name = $header_user ? ($header_user['familiya'] . ' ' . $header_user['ism']) : ($_SESSION['ism'] ?? 'Admin');

$portfolio_link = "../en/home.php?u=" . urlencode($_SESSION['username'] ?? '');
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Admin Panel</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
<body>
    <div class="container py-5">
        <!-- Xush kelibsiz qismi -->
        <div class="welcome-section animatsiya1">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="fw-bold mb-2">Assalomu alaykum, <?= htmlspecialchars($display_name) ?>! 👋</h1>
                    <p class="opacity-75 fs-5">Tizimga xush kelibsiz. Bugun qanday ma'lumotlarni ustida ishlaymiz?</p>
                    
                    <div class="mt-4">
                        <a href="<?= htmlspecialchars($portfolio_link) ?>" class="btn btn-light fw-bold px-4 py-2 shadow-sm" target="_blank" style="border-radius: 12px; color: #2563eb;">
                           <i class="fa-solid fa-arrow-up-right-from-square me-2"></i> Mening Portfoliom
                        </a>
                        <a href="cv_view.php" class="btn btn-primary fw-bold px-4 py-2 shadow-sm ms-2" target="_blank" style="border-radius: 12px; background: #004494; border: none;">
                           <i class="fa-solid fa-file-pdf me-2"></i> CV Yuklab olish
                        </a>
                        <div class="mt-4 p-3 border rounded-4 shadow-sm" style="backdrop-filter: blur(10px); width: fit-content; background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1) !important;">
                            <span class="small opacity-75 d-block mb-2" style="color: white;"><i class="fa-solid fa-share-nodes me-2"></i> Ulashish uchun ssilka:</span>
                            <div class="d-flex align-items-center gap-2">
                                <div id="portfolioUrl" class="px-3 py-2 rounded-3 fw-bold shadow-sm" style="font-family: 'DM Sans', sans-serif; width: fit-content; background: var(--card-bg); color: var(--text-primary); border: 1px solid var(--border-color);">
                                    <?=$_SERVER['HTTP_HOST']?>/en/home.php?u=<?=htmlspecialchars($_SESSION['username'] ?? '')?>
                                </div>
                                <button onclick="copyLink()" class="btn btn-warning fw-bold px-3 py-2 shadow-sm rounded-3" id="copyBtn">
                                    <i class="fa-solid fa-copy"></i> Nusxa
                                </button>
                            </div>
                        </div>

                        <script>
                        function copyLink() {
                            const url = document.getElementById('portfolioUrl').innerText.trim();
                            // Append http:// if needed for actual clipboard
                            const fullUrl = window.location.protocol + '//' + url;
                            navigator.clipboard.writeText(fullUrl).then(() => {
                                const btn = document.getElementById('copyBtn');
                                btn.innerHTML = '<i class="fa-solid fa-check"></i> Saqlandi';
                                btn.classList.replace('btn-warning', 'btn-success');
                                setTimeout(() => {
                                    btn.innerHTML = '<i class="fa-solid fa-copy"></i> Nusxa';
                                    btn.classList.replace('btn-success', 'btn-warning');
                                }, 2000);
                            });
                        }
                        </script>
                    </div>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <i class="fa-solid fa-user-gear fa-5x opacity-25" style="color: white;"></i>
                </div>
            </div>
        </div>

        <h4 class="fw-bold mb-4" style="color: var(--text-primary); opacity: 0.8;"><i class="fa-solid fa-chart-pie me-2"></i> Umumiy statistika</h4>

        <div class="row g-4 animatsiya1">
            <!-- Studentlar -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="list_student.php" class="stat-card">
                    <div class="stat-icon icon-student">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Studentlar</div>
                        <div class="fs-3 fw-bold"><?= $counts['student'] ?></div>
                    </div>
                </a>
            </div>

            <!-- Ta'lim -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="list_talim.php" class="stat-card">
                    <div class="stat-icon icon-talim">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Ta'lim bosqichlari</div>
                        <div class="fs-3 fw-bold"><?= $counts['talim'] ?></div>
                    </div>
                </a>
            </div>

            <!-- Publikatsiyalar -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="list_publication.php" class="stat-card">
                    <div class="stat-icon icon-pub">
                        <i class="fa-solid fa-book-journal-whills"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Nashrlar (Maqolalar)</div>
                        <div class="fs-3 fw-bold"><?= $counts['publication'] ?></div>
                    </div>
                </a>
            </div>

            <!-- Ish tajribasi -->
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="list_nashr.php" class="stat-card">
                    <div class="stat-icon icon-nashr">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Tajriba/Nashrlar</div>
                        <div class="fs-3 fw-bold"><?= $counts['nashr'] ?></div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row mt-5 animatsiya1">
            <div class="col-12">
                <div class="form-card p-4 p-md-5">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fa-solid fa-bolt text-warning fs-5"></i>
                        <h3 class="fw-bold mb-0">Tezkor harakatlar</h3>
                    </div>
                    <p class="text-muted mb-4" style="font-size:0.9rem;">Yangi ma'lumot qo'shish uchun quyidagi tugmalardan foydalaning</p>

                    <div class="qa-grid">

                        <a href="add_home.php" class="qa-card qa-home">
                            <span class="qa-plus"><i class="fa-solid fa-plus"></i></span>
                            <div class="qa-icon"><i class="fa-solid fa-house"></i></div>
                            <div class="qa-label">Asosiy sahifa</div>
                        </a>

                        <a href="add_header.php" class="qa-card qa-header">
                            <span class="qa-plus"><i class="fa-solid fa-plus"></i></span>
                            <div class="qa-icon"><i class="fa-solid fa-id-card"></i></div>
                            <div class="qa-label">Sarlavha</div>
                        </a>

                        <a href="add_talim.php" class="qa-card qa-talim">
                            <span class="qa-plus"><i class="fa-solid fa-plus"></i></span>
                            <div class="qa-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                            <div class="qa-label">Ta'lim</div>
                        </a>

                        <a href="add_nashr.php" class="qa-card qa-nashr">
                            <span class="qa-plus"><i class="fa-solid fa-plus"></i></span>
                            <div class="qa-icon"><i class="fa-solid fa-briefcase"></i></div>
                            <div class="qa-label">Tajriba / Nashr</div>
                        </a>

                        <a href="add_carousel.php" class="qa-card qa-rasm">
                            <span class="qa-plus"><i class="fa-solid fa-plus"></i></span>
                            <div class="qa-icon"><i class="fa-solid fa-images"></i></div>
                            <div class="qa-label">Tajriba rasmi</div>
                        </a>

                        <a href="add_publication.php" class="qa-card qa-pub">
                            <span class="qa-plus"><i class="fa-solid fa-plus"></i></span>
                            <div class="qa-icon"><i class="fa-solid fa-book-open"></i></div>
                            <div class="qa-label">Publication</div>
                        </a>

                        <a href="add_student.php" class="qa-card qa-student">
                            <span class="qa-plus"><i class="fa-solid fa-plus"></i></span>
                            <div class="qa-icon"><i class="fa-solid fa-user-graduate"></i></div>
                            <div class="qa-label">Student</div>
                        </a>

                        <a href="add_footer.php" class="qa-card qa-footer">
                            <span class="qa-plus"><i class="fa-solid fa-plus"></i></span>
                            <div class="qa-icon"><i class="fa-solid fa-address-book"></i></div>
                            <div class="qa-label">Footer</div>
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
