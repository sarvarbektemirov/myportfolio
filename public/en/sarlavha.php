<?php
$lang = $_SESSION['lang'] ?? 'en';
$til  = $_SESSION['til']  ?? 'en';

$p_uid = $portfolio_user_id ?? 1;
$row = null;

if (isset($link) && $link instanceof mysqli) {
    $result = $link->query("SELECT * FROM header WHERE user_id = $p_uid LIMIT 1");
    $row = $result ? $result->fetch_assoc() : null;
}

if ($row) {
    if ($lang === 'en') {
        $ism      = (!empty($row['ism_en']))      ? $row['ism_en']      : $row['ism'];
        $familiya = (!empty($row['familiya_en'])) ? $row['familiya_en'] : $row['familiya'];
        $daraja   = (!empty($row['daraja_en']))   ? $row['daraja_en']   : $row['daraja'];
    } else {
        $ism      = $row['ism'];
        $familiya = $row['familiya'];
        $daraja   = $row['daraja'];
    }
    $tel   = $row['tel'];
    $email = $row['email'];
} else {
    $ism = $familiya = $daraja = $tel = $email = '';
}
?>

<script src="../js/theme.js"></script>
<header class="py-4 border-bottom sticky-top shadow-sm" style="z-index: 1030; background: var(--card-bg); border-color: var(--border-color) !important; transition: all 0.3s ease;">
    <div class="container">
        <div class="row align-items-center">
            <!-- Left: Name and Title -->
            <div class="col-lg-5 mb-3 mb-lg-0">
                <div class="d-flex align-items-center">
                    <div class="logo-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px; background: var(--primary-color); border-radius: 12px; color: white; font-size: 1.2rem; box-shadow: 0 4px 12px rgba(29, 158, 117, 0.2);">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>

                        <h1 style="font-family: 'DM Serif Display', serif; font-size: 1.5rem; margin: 0; color: var(--text-dark); letter-spacing: -0.01em;">
                            <?= htmlspecialchars(($familiya ?? '') . ' ' . ($ism ?? '')) ?>
                        </h1>
                        <p style="font-family: 'DM Sans', sans-serif; font-size: 14px; color: var(--text-muted); margin: 0; font-weight: 500;">
                            <?= htmlspecialchars($daraja ?? '') ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Center: Contact Info -->
            <div class="col-lg-4 mb-3 mb-lg-0">
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-2" style="font-size: 13px; color: var(--text-muted);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        <?= htmlspecialchars($tel ?? '') ?>
                    </div>
                    <div class="d-flex align-items-center gap-2" style="font-size: 13px; color: var(--text-muted);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        <?= htmlspecialchars($email ?? '') ?>
                    </div>
                </div>
            </div>

            <!-- Right: Search, Theme and Language -->
            <div class="col-lg-3 d-flex align-items-center justify-content-lg-end gap-2">
                
                <!-- Theme Toggle Button -->
                <button class="btn btn-light d-flex align-items-center justify-content-center" onclick="toggleTheme()" title="Rejimni o'zgartirish" style="border-radius: 12px; width: 44px; height: 44px; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-dark); transition: all 0.2s;">
                    <i class="fa-solid fa-moon theme-toggle-icon"></i>
                </button>

                <!-- Language Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" style="border-radius: 12px; padding: 10px 18px; font-size: 14px; font-weight: 600; border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-dark); transition: all 0.2s;">
                        <span class="fi fi-<?= $lang === 'en' ? 'gb' : 'uz' ?>" style="border-radius: 2px;"></span>
                        <span class="d-none d-sm-inline"><?= $lang === 'en' ? 'English' : 'O\'zbek' ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 14px; padding: 10px; min-width: 160px; z-index: 1060; background: var(--card-bg); border: 1px solid var(--border-color) !important;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3" href="?lang=en" style="border-radius: 10px; transition: all 0.2s; color: var(--text-dark);">
                                <span class="fi fi-gb" style="border-radius: 2px;"></span> 
                                <span style="font-family: 'DM Sans', sans-serif; font-weight: 500;">English</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3" href="?lang=uz" style="border-radius: 10px; transition: all 0.2s; color: var(--text-dark);">
                                <span class="fi fi-uz" style="border-radius: 2px;"></span> 
                                <span style="font-family: 'DM Sans', sans-serif; font-weight: 500;">O'zbek</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

