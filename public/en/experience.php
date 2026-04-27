<?php
include_once(__DIR__ . "/init.php");
include_once("sarlavha.php");
include_once("navbar.php");
include_once("sidebar.php");

// Use the unified language variable
$current_lang = $lang; 

$nashrlar = ['asosiy' => [], 'qoshimcha' => []];
$p_uid = $portfolio_user_id ?? 1;

// Xavfsiz so'rov: Jadval yoki ustun mavjudligini tekshirish uchun try-catch (PHP 8.1+ uchun)
try {
    $result = $link->query("SELECT * FROM nashrlar WHERE user_id = $p_uid ORDER BY boshlanish DESC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['faoliyat_uz'] = json_decode($row['faoliyat_uz'], true) ?? [];
            $row['faoliyat_en'] = json_decode($row['faoliyat_en'], true) ?? [];
            $nashrlar[$row['tur']][] = $row;
        }
    }

    $carousel = ['asosiy' => [], 'qoshimcha' => []];
    $cr = $link->query("SELECT * FROM nashr_carousel WHERE user_id = $p_uid ORDER BY sana DESC");
    if ($cr && $cr->num_rows > 0) {
        while ($row = $cr->fetch_assoc()) {
            $carousel[$row['tur']][] = $row;
        }
    }
} catch (Exception $e) {
    // Xatolik bo'lsa bo'sh massivlar bilan davom etamiz
}

$oylar_uz = ['','Yanvar','Fevral','Mart','Aprel','May','Iyun',
             'Iyul','Avgust','Sentabr','Oktabr','Noyabr','Dekabr'];
$oylar_en = ['','January','February','March','April','May','June',
             'July','August','September','October','November','December'];

function formatSanaDiapazon($r, $til, $oylar_uz, $oylar_en) {
    $oy  = (int)date('m', strtotime($r['boshlanish']));
    $yil = date('Y', strtotime($r['boshlanish']));
    $bosh = $til === 'uz' ? ($oylar_uz[$oy].' '.$yil) : ($oylar_en[$oy].' '.$yil);
    if ($r['hozirgi']) {
        $tug = $til === 'uz' ? 'hozirgi kungacha' : 'Present';
    } else {
        $oy2  = (int)date('m', strtotime($r['tugash']));
        $yil2 = date('Y', strtotime($r['tugash']));
        $tug  = $til === 'uz' ? ($oylar_uz[$oy2].' '.$yil2) : ($oylar_en[$oy2].' '.$yil2);
    }
    return $bosh . ' – ' . $tug;
}

function formatCarouselSana($sana, $til, $oylar_uz, $oylar_en) {
    $oy  = (int)date('m', strtotime($sana));
    $yil = date('Y', strtotime($sana));
    return $til === 'uz' ? ($oylar_uz[$oy].' '.$yil) : ($oylar_en[$oy].' '.$yil);
}

$sarlavha_asosiy    = $current_lang === 'uz' ? "Asosiy professional faoliyat" : "Main Professional Activity";
$sarlavha_qoshimcha = $current_lang === 'uz' ? "Qo'shimcha faoliyatlar"       : "Additional Activities";
$faoliyat_sarlavha  = $current_lang === 'uz' ? "Asosiy faoliyat va majburiyatlar" : "Main activities and responsibilities";
$page_title         = $current_lang === 'uz' ? "Ish tajribasi" : "Work Experience";
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
        /* Barcha carousel rasmlari bir xil o'lcham — sakrash yo'q */
        #m_work .carousel-inner,
        #e_work .carousel-inner {
            height: 350px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #eee;
        }
        #m_work .carousel-item,
        #e_work .carousel-item {
            height: 350px;
        }
        #m_work .carousel-inner img,
        #e_work .carousel-inner img {
            width: 100%;
            height: 350px;
            object-fit: cover;
            object-position: center top;
        }
        .carousel-yagona .carousel-inner,
        .carousel-yagona .carousel-item,
        .carousel-yagona .carousel-inner img {
            height: 420px;
        }
        .exp-item-header {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 12px;
            cursor: pointer;
            border: 1px solid #edf2f7;
            transition: all 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .exp-item-header:hover {
            background: #f1f5f9;
            transform: translateX(5px);
            border-color: var(--primary-color);
        }
        .exp-item-header b {
            font-size: 15px;
            color: #1a1a1a;
        }
        .exp-badge {
            font-size: 11px;
            background: #E1F5EE;
            color: #0F6E56;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
        }
        .exp-details {
            padding: 10px 20px 25px;
        }
        .exp-details h5 {
            font-size: 17px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .exp-details ul {
            padding-left: 20px;
        }
        .exp-details li {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<div class="container my-4">
    <div class="modern-card">
        <div class="modern-header">
            <div class="modern-header-line"></div>
            <h2 class="modern-title"><?= htmlspecialchars($page_title) ?></h2>
        </div>

        <div class="modern-body">

            <!-- ===== ASOSIY NASHRLAR ===== -->
            <h4 class="modern-title mb-4" style="font-size: 1.2rem; font-family: 'DM Sans', sans-serif; font-weight: 700; color: var(--primary-color);">
                <?= htmlspecialchars($sarlavha_asosiy) ?>
            </h4>

            <?php if (!empty($nashrlar['asosiy'])): ?>
                <?php foreach ($nashrlar['asosiy'] as $i => $n): ?>
                    <?php
                        $lavozim  = $current_lang === 'uz' ? $n['lavozim_uz']  : ($n['lavozim_en'] ?: $n['lavozim_uz']);
                        $ish_joyi = $current_lang === 'uz' ? $n['ish_joyi_uz'] : ($n['ish_joyi_en'] ?: $n['ish_joyi_uz']);
                        $faoliyat = $current_lang === 'uz' ? $n['faoliyat_uz'] : ($n['faoliyat_en'] ?: $n['faoliyat_uz']);
                        $sana_txt = formatSanaDiapazon($n, $current_lang, $oylar_uz, $oylar_en);
                        $cid      = 'asosiy_' . $n['id'];
                        $show     = $i === 0 ? 'show' : '';
                    ?>
                    <div class="exp-item-header" data-bs-toggle="collapse" data-bs-target="#<?= $cid ?>">
                        <b><?= htmlspecialchars($lavozim) ?></b>
                        <span class="exp-badge"><?= htmlspecialchars($sana_txt) ?></span>
                    </div>
                    <div id="<?= $cid ?>" class="collapse <?= $show ?>">
                        <div class="exp-details">
                            <h5><?= htmlspecialchars($ish_joyi) ?></h5>
                            <h6 class="mt-3 mb-2 text-primary" style="font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                <?= htmlspecialchars($faoliyat_sarlavha) ?>
                            </h6>
                            <ul>
                                <?php foreach ($faoliyat as $f): ?>
                                    <li><?= htmlspecialchars($f) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted"><?= $current_lang === 'uz' ? "Ma'lumot topilmadi." : "No data found." ?></p>
            <?php endif; ?>

            <!-- ===== CAROUSEL ===== -->
            <?php
                $faqat_asosiy    = !empty($carousel['asosiy'])    && empty($carousel['qoshimcha']);
                $faqat_qoshimcha = !empty($carousel['qoshimcha']) && empty($carousel['asosiy']);
                $ikkalasi        = !empty($carousel['asosiy'])    && !empty($carousel['qoshimcha']);
            ?>
            <?php if (!empty($carousel['asosiy']) || !empty($carousel['qoshimcha'])): ?>
            <div class="row mt-5 mb-5">

                <!-- Asosiy carousel -->
                <?php if (!empty($carousel['asosiy'])): ?>
                <div class="col-12 <?= $ikkalasi ? 'col-md-6' : '' ?> mb-3">
                    <div id="m_work"
                         class="carousel slide carousel-fade <?= $faqat_asosiy ? 'carousel-yagona' : '' ?>"
                         data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach ($carousel['asosiy'] as $ci => $c): ?>
                                <button type="button" data-bs-target="#m_work"
                                        data-bs-slide-to="<?= $ci ?>"
                                        <?= $ci === 0 ? 'class="active"' : '' ?>></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner shadow-sm">
                            <?php foreach ($carousel['asosiy'] as $ci => $c): ?>
                                <?php
                                    $nom  = $current_lang === 'uz' ? $c['nom_uz'] : ($c['nom_en'] ?: $c['nom_uz']);
                                    $sana = formatCarouselSana($c['sana'], $current_lang, $oylar_uz, $oylar_en);
                                ?>
                                <div class="carousel-item <?= $ci === 0 ? 'active' : '' ?>"
                                     data-bs-interval="<?= 5000 ?>">
                                    <img src="../nashr_carousel/<?= htmlspecialchars($c['rasm']) ?>"
                                         alt="<?= htmlspecialchars($nom) ?>">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 12px; padding: 10px;">
                                        <h5 style="color: #fff; font-weight: 600;"><?= htmlspecialchars($nom) ?></h5>
                                        <p style="font-size: 13px; margin: 0; color: #ddd;"><?= htmlspecialchars($sana) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#m_work" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#m_work" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Qo'shimcha carousel -->
                <?php if (!empty($carousel['qoshimcha'])): ?>
                <div class="col-12 <?= $ikkalasi ? 'col-md-6' : '' ?> mb-3">
                    <div id="e_work"
                         class="carousel slide <?= $faqat_qoshimcha ? 'carousel-yagona' : '' ?>"
                         data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            <?php foreach ($carousel['qoshimcha'] as $ci => $c): ?>
                                <button type="button" data-bs-target="#e_work"
                                        data-bs-slide-to="<?= $ci ?>"
                                        <?= $ci === 0 ? 'class="active"' : '' ?>></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner shadow-sm">
                            <?php foreach ($carousel['qoshimcha'] as $ci => $c): ?>
                                <?php
                                    $nom  = $current_lang === 'uz' ? $c['nom_uz'] : ($c['nom_en'] ?: $c['nom_uz']);
                                    $sana = formatCarouselSana($c['sana'], $current_lang, $oylar_uz, $oylar_en);
                                ?>
                                <div class="carousel-item <?= $ci === 0 ? 'active' : '' ?>"
                                     data-bs-interval="<?= 5000 ?>">
                                    <img src="../nashr_carousel/<?= htmlspecialchars($c['rasm']) ?>"
                                         alt="<?= htmlspecialchars($nom) ?>">
                                    <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 12px; padding: 10px;">
                                        <h5 style="color: #fff; font-weight: 600;"><?= htmlspecialchars($nom) ?></h5>
                                        <p style="font-size: 13px; margin: 0; color: #ddd;"><?= htmlspecialchars($sana) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#e_work" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#e_work" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <?php endif; ?>

            <!-- ===== QO'SHIMCHA NASHRLAR ===== -->
            <h4 class="modern-title mb-4 mt-5" style="font-size: 1.2rem; font-family: 'DM Sans', sans-serif; font-weight: 700; color: var(--primary-color);">
                <?= htmlspecialchars($sarlavha_qoshimcha) ?>
            </h4>

            <?php if (!empty($nashrlar['qoshimcha'])): ?>
                <?php foreach ($nashrlar['qoshimcha'] as $i => $n): ?>
                    <?php
                        $lavozim  = $current_lang === 'uz' ? $n['lavozim_uz']  : ($n['lavozim_en'] ?: $n['lavozim_uz']);
                        $ish_joyi = $current_lang === 'uz' ? $n['ish_joyi_uz'] : ($n['ish_joyi_en'] ?: $n['ish_joyi_uz']);
                        $faoliyat = $current_lang === 'uz' ? $n['faoliyat_uz'] : ($n['faoliyat_en'] ?: $n['faoliyat_uz']);
                        $sana_txt = formatSanaDiapazon($n, $current_lang, $oylar_uz, $oylar_en);
                        $cid      = 'qoshimcha_' . $n['id'];
                        $show     = $i === 0 ? 'show' : '';
                    ?>
                    <div class="exp-item-header" data-bs-toggle="collapse" data-bs-target="#<?= $cid ?>" style="border-left: 4px solid #3b82f6;">
                        <b><?= htmlspecialchars($lavozim) ?></b>
                        <span class="exp-badge" style="background: #e0e7ff; color: #3730a3;"><?= htmlspecialchars($sana_txt) ?></span>
                    </div>
                    <div id="<?= $cid ?>" class="collapse <?= $show ?>">
                        <div class="exp-details">
                            <h5 style="color: #3b82f6;"><?= htmlspecialchars($ish_joyi) ?></h5>
                            <ul>
                                <?php foreach ($faoliyat as $f): ?>
                                    <li><?= htmlspecialchars($f) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted"><?= $current_lang === 'uz' ? "Ma'lumot topilmadi." : "No data found." ?></p>
            <?php endif; ?>

        </div>

        <div class="modern-footer">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="#aaa" stroke-width="1.5"/>
                <path d="M12 7v5l3 3" stroke="#aaa" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <?= $current_lang === 'uz' ? 'Ish tajribasi - Professional faoliyat' : 'Work experience and professional journey' ?>
        </div>
    </div>
</div>

<?php include_once("footer.php"); ?>
<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
