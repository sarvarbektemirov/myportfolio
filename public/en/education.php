<?php
include_once("init.php");
include_once("sarlavha.php");
include_once("navbar.php");
include_once("sidebar.php");

// Use the unified language variable
$current_lang = $lang; 

$talimlar = [];
$p_uid = $portfolio_user_id ?? 1;
if (isset($link) && $link instanceof mysqli) {
    $result = $link->query("SELECT * FROM talim WHERE user_id = $p_uid ORDER BY id ASC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $talimlar[] = $row;
        }
    }
}

$bosqich_nomi = [
  'maktab'   => ['uz' => 'Maktab',   'en' => 'School'],
  'kollej'   => ['uz' => 'Kollej',   'en' => 'College'],
  'bakalavr' => ['uz' => 'Bakalavr', 'en' => "Bachelor's Degree"],
  'magistr'  => ['uz' => 'Magistr',  'en' => "Master's Degree"],
  'phd'      => ['uz' => 'PhD',      'en' => 'PhD'],
];
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
    .edu-item {
      display: flex;
      gap: 25px;
      margin-bottom: 35px;
      padding-bottom: 25px;
      border-bottom: 1px dashed #eee;
    }
    .edu-item:last-child {
      border-bottom: none;
      margin-bottom: 0;
    }
    .edu-img {
      width: 140px;
      height: 140px;
      object-fit: cover;
      border-radius: 12px;
      flex-shrink: 0;
      border: 1px solid #f0f0f0;
    }
    .edu-content {
      flex-grow: 1;
    }
    @media (max-width: 576px) {
      .edu-item {
        flex-direction: column;
        gap: 15px;
      }
      .edu-img {
        width: 100%;
        height: 180px;
      }
    }
  </style>
</head>

<body>

  <div class="container my-4">
    <div class="modern-card">
      <div class="modern-header">
        <div class="modern-header-line"></div>
        <h2 class="modern-title"><?= $current_lang === 'uz' ? "Ta'lim joylari" : "Places of study" ?></h2>
      </div>

      <div class="modern-body">
        <?php if (!empty($talimlar)): ?>
          <?php foreach ($talimlar as $t): ?>
            <?php
              $b_key  = strtolower($t['bosqich']);
              $b_nomi = $current_lang === 'uz'
                ? ($bosqich_nomi[$b_key]['uz'] ?? $t['bosqich'])
                : ($bosqich_nomi[$b_key]['en'] ?? $t['bosqich_en'] ?? $t['bosqich']);
              $tavsif = $current_lang === 'uz' ? $t['tavsif_uz'] : ($t['tavsif_en'] ?: $t['tavsif_uz']);
            ?>
            
            <div class="edu-item">
              <?php if (!empty($t['rasm'])): ?>
                <img src="../talimrasm/<?= htmlspecialchars($t['rasm']) ?>" 
                     class="edu-img" alt="<?= htmlspecialchars($b_nomi) ?>">
              <?php endif; ?>
              
              <div class="edu-content">
                <span class="modern-badge"><?= htmlspecialchars($b_nomi) ?></span>
                <p class="modern-text">
                  <?= nl2br(htmlspecialchars($tavsif)) ?>
                </p>
              </div>
            </div>

          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-danger">
            <?= $current_lang === 'uz' ? "Ta'lim ma'lumotlari topilmadi!" : "No education data found!" ?>
          </p>
        <?php endif; ?>
      </div>

      <div class="modern-footer">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="9" stroke="#aaa" stroke-width="1.5"/>
          <path d="M12 7v5l3 3" stroke="#aaa" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <?= $current_lang === 'uz' ? 'Ta\'lim yo\'li' : 'Educational background' ?>
      </div>
    </div>
  </div>

  <?php include_once("footer.php"); ?>
  <script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
