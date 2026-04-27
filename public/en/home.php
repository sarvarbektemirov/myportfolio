<?php
include_once("init.php");
include_once("sarlavha.php");
include_once("navbar.php");
include_once("sidebar.php");

$p_uid = $portfolio_user_id ?? 1;
$home = null;
if (isset($link) && $link instanceof mysqli) {
  $surov = $link->query("SELECT * FROM home WHERE user_id = $p_uid LIMIT 1");
  $home = $surov ? $surov->fetch_assoc() : null;
}

$malumot = '';
if ($home) {
  if ($lang === 'en') {
    $malumot = (!empty($home['malumot_en']) ? $home['malumot_en'] : $home['malumot_uz']);
  } else {
    $malumot = $home['malumot_uz'];
  }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portfolio</title>
  <link rel="icon" href="../rasmlar/my_favicon.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link
    href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="../css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css" />
  <link rel="stylesheet" href="../css/boots_style.css">
  <link rel="stylesheet" href="../css/modern_style.css">
</head>

<body>

  <div class="container my-4">
    <div class="modern-card">

      <div class="modern-header">
        <div class="modern-header-line"></div>
        <h2 class="modern-title">Short Introduction</h2>
      </div>

      <div class="modern-body">
        <?php if (!empty($home['rasm'])): ?>
          <img src="../files/<?= htmlspecialchars($home['rasm']) ?>" class="modern-img" alt="Profil rasmi" />
        <?php endif; ?>

        <span class="modern-badge">
          <?= $lang === 'uz' ? 'Biografiya' : 'Biography' ?>
        </span>
        <p class="modern-text">
          <?= nl2br(htmlspecialchars($malumot ?? '')) ?>
        </p>

        <?php 
        $skills_raw = ($lang === 'en' ? (!empty($home['skills_en']) ? $home['skills_en'] : $home['skills_uz']) : $home['skills_uz']);
        if (!empty($skills_raw)): 
            $skills_array = explode(',', $skills_raw);
        ?>
          <div class="mt-4" style="clear: both;">
            <span class="modern-badge mb-3">
              <?= $lang === 'uz' ? 'Ko\'nikmalar' : 'Skills' ?>
            </span>
            <div class="d-flex flex-wrap gap-1">
              <?php 
              foreach ($skills_array as $idx => $skill): 
                $skill = trim($skill);
                if($skill === '') continue; 
                
                $color_idx = ($idx % 6) + 1;
                $lower_skill = strtolower($skill);
                
                // Expanded Logo mapping with high-quality sources and colors
                $logo_map = [
                    // Languages & Core
                    'php' => 'https://cdn.simpleicons.org/php/777BB4',
                    'javascript' => 'https://cdn.simpleicons.org/javascript/F7DF1E',
                    'js' => 'https://cdn.simpleicons.org/javascript/F7DF1E',
                    'typescript' => 'https://cdn.simpleicons.org/typescript/3178C6',
                    'ts' => 'https://cdn.simpleicons.org/typescript/3178C6',
                    'python' => 'https://cdn.simpleicons.org/python/3776AB',
                    'java' => 'https://cdn.simpleicons.org/java/007396',
                    'c++' => 'https://cdn.simpleicons.org/cplusplus/00599C',
                    'cpp' => 'https://cdn.simpleicons.org/cplusplus/00599C',
                    'c#' => 'https://cdn.simpleicons.org/csharp/239120',
                    'csharp' => 'https://cdn.simpleicons.org/csharp/239120',
                    'go' => 'https://cdn.simpleicons.org/go/00ADD8',
                    'rust' => 'https://cdn.simpleicons.org/rust/000000',
                    'ruby' => 'https://cdn.simpleicons.org/ruby/CC342D',
                    'swift' => 'https://cdn.simpleicons.org/swift/F05138',
                    'kotlin' => 'https://cdn.simpleicons.org/kotlin/7F52FF',
                    'dart' => 'https://cdn.simpleicons.org/dart/0175C2',
                    
                    // Web Frameworks & Tools
                    'html' => 'https://cdn.simpleicons.org/html5/E34F26',
                    'html5' => 'https://cdn.simpleicons.org/html5/E34F26',
                    'css' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
                    'css3' => 'https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg',
                    'laravel' => 'https://cdn.simpleicons.org/laravel/FF2D20',
                    'react' => 'https://cdn.simpleicons.org/react/61DAFB',
                    'vue' => 'https://cdn.simpleicons.org/vuedotjs/4FC08D',
                    'angular' => 'https://cdn.simpleicons.org/angular/DD0031',
                    'node' => 'https://cdn.simpleicons.org/nodedotjs/339933',
                    'express' => 'https://cdn.simpleicons.org/express/000000',
                    'next.js' => 'https://cdn.simpleicons.org/nextdotjs/000000',
                    'nuxt' => 'https://cdn.simpleicons.org/nuxtdotjs/00DC82',
                    'tailwind' => 'https://cdn.simpleicons.org/tailwindcss/06B6D4',
                    'bootstrap' => 'https://cdn.simpleicons.org/bootstrap/7952B3',
                    'jquery' => 'https://cdn.simpleicons.org/jquery/0769AD',
                    'sass' => 'https://cdn.simpleicons.org/sass/CC6699',
                    
                    // Databases
                    'mysql' => 'https://cdn.simpleicons.org/mysql/4479A1',
                    'sql' => 'https://cdn.simpleicons.org/mysql/4479A1',
                    'postgresql' => 'https://cdn.simpleicons.org/postgresql/4169E1',
                    'mongodb' => 'https://cdn.simpleicons.org/mongodb/47A248',
                    'redis' => 'https://cdn.simpleicons.org/redis/DC382D',
                    'firebase' => 'https://cdn.simpleicons.org/firebase/FFCA28',
                    'supabase' => 'https://cdn.simpleicons.org/supabase/3ECF8E',
                    
                    // Marketing & SMM
                    'smm' => 'https://cdn.simpleicons.org/googlemarketingplatform/4285F4',
                    'seo' => 'https://cdn.simpleicons.org/googlesearchconsole/4285F4',
                    'marketing' => 'https://cdn.simpleicons.org/googlemarketingplatform/4285F4',
                    'facebook ads' => 'https://cdn.simpleicons.org/facebook/1877F2',
                    'google ads' => 'https://cdn.simpleicons.org/googleads/4285F4',
                    'instagram' => 'https://cdn.simpleicons.org/instagram/E4405F',
                    'facebook' => 'https://cdn.simpleicons.org/facebook/1877F2',
                    'tiktok' => 'https://cdn.simpleicons.org/tiktok/000000',
                    'youtube' => 'https://cdn.simpleicons.org/youtube/FF0000',
                    'telegram' => 'https://cdn.simpleicons.org/telegram/26A5E4',
                    'linkedin' => 'https://cdn.simpleicons.org/linkedin/0A66C2',
                    'twitter' => 'https://cdn.simpleicons.org/twitter/1DA1F2',
                    
                    // Design
                    'design' => 'https://cdn.simpleicons.org/figma/F24E1E',
                    'figma' => 'https://cdn.simpleicons.org/figma/F24E1E',
                    'canva' => 'https://cdn.simpleicons.org/canva/00C4CC',
                    'photoshop' => 'https://cdn.simpleicons.org/adobephotoshop/31A8FF',
                    'illustrator' => 'https://cdn.simpleicons.org/adobeillustrator/FF9A00',
                    'premiere' => 'https://cdn.simpleicons.org/adobepremierepro/9999FF',
                    'after effects' => 'https://cdn.simpleicons.org/adobeaftereffects/9999FF',
                    
                    // DevOps & Cloud
                    'git' => 'https://cdn.simpleicons.org/git/F05032',
                    'github' => 'https://cdn.simpleicons.org/github/181717',
                    'docker' => 'https://cdn.simpleicons.org/docker/2496ED',
                    'kubernetes' => 'https://cdn.simpleicons.org/kubernetes/326CE5',
                    'aws' => 'https://cdn.simpleicons.org/amazonaws/232F3E',
                    'azure' => 'https://cdn.simpleicons.org/microsoftazure/0078D4',
                    'digitalocean' => 'https://cdn.simpleicons.org/digitalocean/0080FF',
                    
                    // Office & Productivity
                    'word' => 'https://cdn.simpleicons.org/microsoftword/2B579A',
                    'excel' => 'https://cdn.simpleicons.org/microsoftexcel/217346',
                    'powerpoint' => 'https://cdn.simpleicons.org/microsoftpowerpoint/B7472A',
                    'trello' => 'https://cdn.simpleicons.org/trello/0052CC',
                    'notion' => 'https://cdn.simpleicons.org/notion/000000',
                    'slack' => 'https://cdn.simpleicons.org/slack/4A154B'
                ];

                // Logic to handle spaces or missing slugs
                $slug_candidate = str_replace(' ', '', $lower_skill);
                $logo_url = $logo_map[$lower_skill] ?? "https://cdn.simpleicons.org/" . $slug_candidate; 
              ?>
                <span class="skill-tag skill-tag-<?= $color_idx ?>">
                  <div class="skill-icon-box">
                    <img src="<?= $logo_url ?>" onerror="this.closest('.skill-tag').classList.add('no-logo'); this.parentElement.remove();" alt="">
                  </div>
                  <span class="skill-dot"></span>
                  <span class="skill-name"><?= htmlspecialchars($skill) ?></span>
                </span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="modern-footer">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="9" stroke="#aaa" stroke-width="1.5" />
          <path d="M12 7v5l3 3" stroke="#aaa" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        <?= $lang === 'uz' ? 'Asosiy sahifa' : 'Home page' ?>
      </div>

    </div>
  </div>

  <?php include_once("footer.php"); ?>
  <script src="../js/bootstrap.bundle.js"></script>
</body>

</html>