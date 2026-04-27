<?php
include_once("init.php");
include_once("sarlavha.php");
include_once("navbar.php");
include_once("sidebar.php");

// Use the unified language variable
$current_lang = $lang;

// Har bir tab uchun ma'lumotlarni DB dan olish funksiyasi
function getPubs($link, $tur, $p_uid) {
    if (!$link) return null;
    $tur = $link->real_escape_string($tur);
    $result = $link->query("SELECT * FROM publication WHERE user_id = $p_uid AND tur LIKE '%$tur%' ORDER BY yil DESC");
    return $result;
}

// Function to parse the "Title EN / Title UZ" format
function parsePubTitle($full_title, $lang) {
    if (strpos($full_title, '/') !== false) {
        $parts = explode('/', $full_title);
        if ($lang === 'en') {
            return trim($parts[0]);
        } else {
            return trim($parts[1] ?? $parts[0]);
        }
    }
    return $full_title;
}

$page_title_text = $current_lang === 'en' ? "Scientific and Methodological Publications" : "Ilmiy va uslubiy nashrlar";
$footer_text_val = $current_lang === 'en' ? "Scientific publications and conference materials" : "Ilmiy nashrlar va konferensiya materiallari";
$p_uid = $portfolio_user_id ?? 1;
?>
  <title>Portfolio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css" />
  <link rel="stylesheet" href="../css/boots_style.css">
  <link rel="stylesheet" href="../css/modern_style.css">
  <style>
    /* Force muted text visibility on this page in dark mode */
    [data-theme="dark"] .text-muted {
      color: var(--text-muted) !important;
      opacity: 1 !important;
    }
  </style>
</head>
<body>

<div class="container my-4">
    <div class="modern-card">
        <div class="modern-header">
            <div class="modern-header-line"></div>
            <h2 class="modern-title"><?= htmlspecialchars($page_title_text) ?></h2>
        </div>

        <div class="modern-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs pub-tabs justify-content-start" role="tablist">
              <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#scopus_j"><?= $current_lang === 'en' ? 'Scopus Journals' : 'Scopus Jurnallar' ?></a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#scopus_k"><?= $current_lang === 'en' ? 'Scopus Conferences' : 'Scopus Konferensiyalar' ?></a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#xalqaro_j"><?= $current_lang === 'en' ? 'Int. Journals' : 'Xalqaro Jurnallar' ?></a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#respublika_j"><?= $current_lang === 'en' ? 'Local Journals' : 'Respublika Jurnallari' ?></a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#xalqaro_k"><?= $current_lang === 'en' ? 'Int. Conferences' : 'Xalqaro Konferensiyalar' ?></a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#respublika_k"><?= $current_lang === 'en' ? 'Local Conferences' : 'Respublika Konferensiyalari' ?></a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#dgu"><?= $current_lang === 'en' ? 'Software License' : 'DGU Guvohnomalari' ?></a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#qullanma"><?= $current_lang === 'en' ? 'Handbooks' : 'Uslubiy qo\'llanmalar' ?></a></li>
            </ul>

        <div class="tab-content">

              <div id="scopus_j" class="tab-pane active fade show"><br>
                <h5 class="mb-4" style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);"><?= $current_lang === 'en' ? 'Articles Published in International Journals Indexed in the Scopus Database' : 'Scopus bazasidagi xalqaro jurnallarda nashr etilgan maqolalar' ?></h5>
                <?php
                  $r = $link->query("SELECT * FROM publication WHERE user_id = $p_uid AND baza LIKE '%Scopus%' AND tur LIKE '%jurnal%' ORDER BY yil DESC");
                  showModernList($r, $current_lang);
                ?>
              </div>

              <div id="scopus_k" class="tab-pane fade"><br>
                <h5 class="mb-4" style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);"><?= $current_lang === 'en' ? 'Articles Published in Scopus-Indexed Conference Proceedings' : 'Scopus bazasiga kiruvchi konferensiya materiallarida nashr etilgan maqolalar' ?></h5>
                <?php
                  $r = $link->query("SELECT * FROM publication WHERE user_id = $p_uid AND baza LIKE '%Scopus%' AND tur LIKE '%konferensiya%' ORDER BY yil DESC");
                  showModernList($r, $current_lang);
                ?>
              </div>

              <div id="xalqaro_j" class="tab-pane fade"><br>
                <h5 class="mb-4" style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);"><?= $current_lang === 'en' ? 'Articles Published in International Journals' : 'Xalqaro jurnallarda nashr etilgan maqolalar' ?></h5>
                <?php
                  $r = $link->query("SELECT * FROM publication WHERE user_id = $p_uid AND tur LIKE '%Xalqaro jurnaldagi%' ORDER BY yil DESC");
                  showModernList($r, $current_lang);
                ?>
              </div>

              <div id="respublika_j" class="tab-pane fade"><br>
                <h5 class="mb-4" style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);"><?= $current_lang === 'en' ? 'Articles Published in Local Journals' : 'Respublika jurnallarida nashr etilgan maqolalar' ?></h5>
                <?php
                  $r = $link->query("SELECT * FROM publication WHERE user_id = $p_uid AND tur LIKE '%Respublika jurnalidagi%' ORDER BY yil DESC");
                  showModernList($r, $current_lang);
                ?>
              </div>

              <div id="xalqaro_k" class="tab-pane fade"><br>
                <h5 class="mb-4" style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);"><?= $current_lang === 'en' ? 'Articles Published in International Conference Proceedings' : 'Xalqaro konferensiya materiallarida nashr etilgan maqolalar' ?></h5>
                <?php
                  $r = $link->query("SELECT * FROM publication WHERE user_id = $p_uid AND tur LIKE '%Xalqaro konferensiya%' ORDER BY yil DESC");
                  showModernList($r, $current_lang);
                ?>
              </div>

              <div id="respublika_k" class="tab-pane fade"><br>
                <h5 class="mb-4" style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);"><?= $current_lang === 'en' ? 'Articles Published in National Conference Proceedings' : 'Respublika miqyosidagi konferensiya materiallarida nashr etilgan maqolalar' ?></h5>
                <?php
                  $r = $link->query("SELECT * FROM publication WHERE user_id = $p_uid AND tur LIKE '%Respublika konferensiya%' ORDER BY yil DESC");
                  showModernList($r, $current_lang);
                ?>
              </div>

              <div id="dgu" class="tab-pane fade"><br>
                <h5 class="mb-4" style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);"><?= $current_lang === 'en' ? 'Certificates Issued by the Intellectual Property Agency' : 'Intellektual mulk agentligi tomonidan berilgan guvohnomalar' ?></h5>
                <?php
                  $r = $link->query("SELECT * FROM publication WHERE user_id = $p_uid AND tur LIKE '%Boshqa%' ORDER BY yil DESC");
                  showModernList($r, $current_lang);
                ?>
              </div>

              <div id="qullanma" class="tab-pane fade"><br>
                <h5 class="mb-4" style="font-size: 1.1rem; font-weight: 600; color: var(--text-dark);"><?= $current_lang === 'en' ? 'Educational Manuals and Handbooks' : 'Uslubiy qo\'llanmalar va darsliklar' ?></h5>
                <?php
                  $r = $link->query("SELECT * FROM publication WHERE user_id = $p_uid AND tur LIKE '%Kitob%' ORDER BY yil DESC");
                  showModernList($r, $current_lang);
                ?>
              </div>

            </div>
        </div>

        <div class="modern-footer">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="#aaa" stroke-width="1.5"/>
                <path d="M12 7v5l3 3" stroke="#aaa" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <?= htmlspecialchars($footer_text_val) ?>
        </div>
    </div>
</div>

<?php 
function showModernList($result, $lang) {
    if ($result && $result->num_rows > 0) {
        echo '<div class="pub-list">';
        $num = 1;
        while ($row = $result->fetch_assoc()) {
            echo '<div class="pub-item">';

            // --- Raqam ---
            echo '<span class="pub-num">' . $num++ . '.</span>';

            echo '<div class="pub-cite">';

            if (!empty($row['cite'])) {
                // Faqat cite matni
                echo nl2br(htmlspecialchars($row['cite']));
            } else {
                // Ketma-ketlik: muallif → nom → jurnal → yil → sahifa → doi
                $parts = [];

                if (!empty($row['muallif']))
                    $parts[] = '<strong>' . htmlspecialchars($row['muallif']) . '</strong>';

                $p_title = parsePubTitle($row['nom'] ?? '', $lang);
                if (!empty($p_title))
                    $parts[] = '&ldquo;' . htmlspecialchars($p_title) . '&rdquo;';

                if (!empty($row['jurnal']))
                    $parts[] = '<em>' . htmlspecialchars($row['jurnal']) . '</em>';

                if (!empty($row['yil']))
                    $parts[] = htmlspecialchars($row['yil']);

                if (!empty($row['sahifa']))
                    $parts[] = ($lang === 'en' ? 'pp.&nbsp;' : 'b.&nbsp;') . htmlspecialchars($row['sahifa']);

                if (!empty($row['doi']))
                    $parts[] = '<a href="' . htmlspecialchars($row['doi']) . '" target="_blank" class="pub-doi-inline">DOI: ' . htmlspecialchars($row['doi']) . '</a>';

                echo implode('. ', $parts) . '.';
            }

            echo '</div>'; // pub-cite

            // --- Meta: bazasi va PDF tugmasi ---
            echo '<div class="pub-meta">';
            if (!empty($row['baza']))
                echo '<span class="modern-badge" style="margin-bottom:0;">' . htmlspecialchars($row['baza']) . '</span>';

            if (!empty($row['fayl1']))
                echo '<a href="/my_files/' . htmlspecialchars($row['fayl1']) . '" target="_blank" class="pub-btn pub-btn-pdf">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-top:-2px;margin-right:4px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>PDF</a>';
            echo '</div>';

            echo '</div>'; // pub-item
        }
        echo '</div>';
    } else {
        echo "<p class='text-muted'>" . ($lang === 'en' ? 'No publications found in this category.' : 'Ushbu toifada maqolalar topilmadi.') . "</p>";
    }
}
?>

<?php include_once("footer.php"); ?>
<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
