<?php
include_once("init.php");
include_once("sarlavha.php");
include_once("navbar.php");
include_once("sidebar.php");

// Use the unified language variable
$current_lang = $lang;

// Bazadan studentlarni olish
$p_uid = $portfolio_user_id ?? 1;
$result = $link->query("SELECT * FROM students WHERE user_id = $p_uid ORDER BY id ASC");

// YANGI
$studentlar = ['toifa_1' => [], 'toifa_2' => [], 'toifa_3' => []];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $t = $row['toifa'] ?? 'boshqa';
        if (isset($studentlar[$t])) {
            $studentlar[$t][] = $row;
        }
    }
}

// Til bo'yicha matn tanlash
function s_matn($row, $kalit, $til) {
    $uz = $row[$kalit . '_uz'] ?? '';
    $en = $row[$kalit . '_en'] ?? '';
    if ($til === 'uz') return (!empty($uz)) ? $uz : $en;
    return (!empty($en)) ? $en : $uz;
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
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
  <style>
    .student-card {
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s;
        background: var(--card-bg);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .student-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow);
        border-color: var(--primary-color);
    }
    .student-img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        object-position: top;
        border-bottom: 1px solid var(--border-color);
    }
    .student-info {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .student-name {
        font-family: 'DM Serif Display', serif;
        font-size: 1.25rem;
        color: var(--text-dark);
        margin-bottom: 8px;
    }
    .student-desc {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.6;
        margin-bottom: 15px;
        flex-grow: 1;
    }
    .info-alert {
        background: var(--badge-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        position: relative;
    }
    .btn-close-alert {
        position: absolute;
        top: 12px;
        right: 15px;
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 20px;
        cursor: pointer;
        opacity: 0.5;
        transition: opacity 0.2s;
        line-height: 1;
    }
    .btn-close-alert:hover {
        opacity: 1;
    }
    .info-alert-title {
        color: var(--primary-color);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
    }
    .info-alert-text {
        color: var(--text-dark);
        font-size: 14px;
        line-height: 1.6;
    }
    .btn-student-profile {
        background: transparent;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 15px;
        transition: all 0.2s;
        width: 100%;
    }
    .btn-student-profile:hover {
        background: var(--primary-color);
        color: #fff;
    }
    .modal-content {
        border-radius: 20px;
        overflow: hidden;
    }
    .modal-header {
        padding: 1.5rem 2rem;
    }
    .modal-title {
        font-family: 'DM Serif Display', serif;
    }
    .modal-body {
        padding: 2rem;
    }
    .student-modal-img {
        border: 1px solid var(--border-color);
    }
    .carousel-indicators [data-bs-target] {
        background-color: var(--primary-color);
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
  </style>
</head>
<body>

<div class="container my-4">
    <div class="modern-card">
        <div class="modern-header">
            <div class="modern-header-line"></div>
            <h2 class="modern-title"><?= $current_lang === 'en' ? "My Students" : "Mening talabalarim" ?></h2>
        </div>

        <div class="modern-body">
            <div class="info-alert" id="studentInfoAlert">
                <button class="btn-close-alert" onclick="dismissAlert()">&times;</button>
                <div class="info-alert-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <?= $current_lang === 'en' ? "Mentorship and Guidance" : "Mentorlik va yo'l-yo'riq" ?>
                </div>
                <div class="info-alert-text">
                    <?= $current_lang === 'en' 
                        ? "This page provides information about students whom I taught during their bachelor's, master's, or pre-university periods and who maintain a mentor-mentee relationship with me." 
                        : "Ushbu sahifada men bakalavriat, magistratura yoki universitetgacha bo'lgan davrlarda dars bergan va men bilan ustoz-shogird munosabatlarini saqlab kelayotgan talabalar haqida ma'lumot berilgan." ?>
                    <hr style="opacity: 0.1; margin: 15px 0;">
                    <small><?= $current_lang === 'en' 
                            ? "If there are any inaccuracies in your information, or if you'd like to update your profile, please reach out via the contact form or email." 
                            : "Agar ma'lumotlaringizda biron bir noaniqlik bo'lsa yoki profilingizni yangilamoqchi bo'lsangiz, iltimos, aloqa formasi yoki elektron pochta orqali bog'laning." ?></small>
                </div>
            </div>

            <?php
            $toifalar = [
                'toifa_1' => ['id' => 'bakalavr_1', 'sarlavha' => ($current_lang === 'en' ? 'Bachelors' : 'Bakalavrlar')],
                'toifa_2' => ['id' => 'magistr_1',  'sarlavha' => ($current_lang === 'en' ? 'Masters' : 'Magistrlar')],
                'toifa_3' => ['id' => 'boshqa',     'sarlavha' => ($current_lang === 'en' ? 'Others' : 'Boshqalar')],
            ];

            foreach ($toifalar as $toifa_key => $toifa_info):
                $guruh = $studentlar[$toifa_key];
                if (empty($guruh)) continue;
            ?>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5" id="<?= $toifa_info['id'] ?>">
                <?php foreach ($guruh as $st):
                    $ism       = htmlspecialchars($st['ism']);
                    $rasm      = !empty($st['rasm']) ? '/student_rasmlar/' . htmlspecialchars($st['rasm']) : '/rasmlar/shogirdlar/odam.png';
                    $qisqa     = htmlspecialchars(s_matn($st, 'qisqa_malumot', $current_lang));
                    $tolik     = htmlspecialchars(s_matn($st, 'tolik_malumot', $current_lang));
                    $modal_id  = 'student_' . $st['id'];
                ?>
                    <div class="col">
                        <div class="student-card">
                            <img src="<?= $rasm ?>" class="student-img" alt="<?= $ism ?>">
                            <div class="student-info">
                                <h4 class="student-name"><?= $ism ?></h4>
                                <p class="student-desc"><?= $qisqa ?></p>
                                <button type="button" class="btn-student-profile" 
                                        data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>">
                                    <?= $current_lang === 'en' ? 'View Full Profile' : 'To\'liq profilni ko\'rish' ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="<?= $modal_id ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title"><?= $ism ?></h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <img src="<?= $rasm ?>" class="rounded-4 shadow-sm float-start me-4 mb-3 student-modal-img" style="max-width: 300px;">
                                    <p class="modern-text" style="text-align: justify;"><?= nl2br($tolik ?: $qisqa) ?></p>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>

        </div>

        <div class="modern-footer">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="#aaa" stroke-width="1.5"/>
                <path d="M12 7v5l3 3" stroke="#aaa" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <?= $current_lang === 'en' ? 'Student network and future colleagues' : 'Talabalar tarmog\'i va kelajakdagi hamkasblar' ?>
        </div>
    </div>
</div>

<?php include_once("footer.php"); ?>
<script src="../js/bootstrap.bundle.js"></script>
<script>
    // Alert holatini tekshirish
    document.addEventListener('DOMContentLoaded', function() {
        if (localStorage.getItem('studentAlertDismissed') === 'true') {
            const alert = document.getElementById('studentInfoAlert');
            if (alert) alert.style.display = 'none';
        }
    });

    // Alert'ni yopish funksiyasi
    function dismissAlert() {
        const alert = document.getElementById('studentInfoAlert');
        if (alert) {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
                localStorage.setItem('studentAlertDismissed', 'true');
            }, 300);
        }
    }
</script>
</body>
</html>
