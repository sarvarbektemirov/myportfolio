<!-- Font Awesome (globally included for sidebar icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php
// Hozirgi sahifa nomini aniqlash
$current_page = basename($_SERVER['PHP_SELF']);

// Qaysi menyu guruhini ochiq qoldirishni aniqlash
$open_cat = '';
if (stripos($current_page, 'education') !== false) $open_cat = 'edu';
if (stripos($current_page, 'experience') !== false) $open_cat = 'exp';
if (stripos($current_page, 'publication') !== false) $open_cat = 'pub';
if (stripos($current_page, 'teaching') !== false) $open_cat = 'teach';
if (stripos($current_page, 'student') !== false) $open_cat = 'stud';
if (stripos($current_page, 'other') !== false) $open_cat = 'other';
if (stripos($current_page, 'connection') !== false) $open_cat = 'conn';
if ($current_page == 'home.php' || $current_page == 'index.php') $open_cat = 'home';
?>

<style>
    /* Floating Toggle Button - Modern Glassmorphism */
    .sidebar-toggle {
        position: fixed;
        left: 20px;
        top: 120px;
        z-index: 1040;
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-shadow: var(--shadow);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .sidebar-toggle i {
        transform: translateY(1px); /* Perfect pixel-perfect centering based on inspection */
    }

    .sidebar-toggle:hover {
        transform: scale(1.1) rotate(5deg);
        color: #168a65;
        border-color: var(--primary-color);
    }

    /* Modern Offcanvas Sidebar */
    .offcanvas-sidebar {
        width: 300px !important;
        background: var(--card-bg) !important;
        border-right: 1px solid var(--border-color) !important;
        box-shadow: 10px 0 40px rgba(0,0,0,0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .sb-header {
        padding: 40px 25px 30px;
        background: linear-gradient(to bottom, var(--bg-color), transparent);
    }
    .sb-title {
        font-family: 'DM Serif Display', serif;
        font-size: 1.6rem;
        color: var(--text-dark);
        margin: 0;
        letter-spacing: -0.5px;
    }
    .sb-subtitle {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 700;
        margin-top: 5px;
        opacity: 0.8;
    }
    
    .sb-menu {
        padding: 10px 20px;
    }
    .sb-item {
        margin-bottom: 8px;
    }
    .sb-link-cat {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 14px 18px;
        border-radius: 12px;
        border: 1px solid transparent;
        background: transparent;
        color: var(--text-dark);
        font-size: 14px;
        font-weight: 600;
        transition: all 0.25s ease;
        text-align: left;
    }
    .sb-link-cat i:first-child {
        width: 24px;
        margin-right: 15px;
        font-size: 18px;
        color: var(--primary-color);
        transition: all 0.3s ease;
    }
    .sb-link-cat .chevron {
        margin-left: auto;
        font-size: 10px;
        transition: transform 0.3s;
        opacity: 0.4;
    }
    .sb-link-cat:not(.collapsed) .chevron {
        transform: rotate(180deg);
        opacity: 1;
    }
    .sb-link-cat:hover, .sb-link-cat:not(.collapsed) {
        background: var(--badge-bg);
        color: var(--primary-color);
        border-color: var(--border-color);
        transform: translateX(5px);
    }
    .sb-link-cat:not(.collapsed) i:first-child {
        transform: scale(1.2);
    }

    .sb-sub-menu {
        padding: 8px 0 8px 57px;
        list-style: none;
        border-left: 1px dashed var(--border-color);
        margin-left: 30px;
        margin-top: 5px;
    }
    .sb-sub-link {
        display: block;
        padding: 10px 0;
        color: var(--text-muted);
        font-size: 13.5px;
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
    }
    .sb-sub-link:hover {
        color: var(--primary-color);
        transform: translateX(5px);
    }
    .sb-sub-link.active {
        color: var(--primary-color);
        font-weight: 700;
    }
    .sb-sub-link.active::before {
        content: "";
        position: absolute;
        left: -28px;
        top: 50%;
        transform: translateY(-50%);
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--primary-color);
        box-shadow: 0 0 10px var(--primary-color);
    }

    /* Close Button Customization */
    .btn-close-custom {
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        color: var(--text-dark);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        opacity: 0.7;
    }
    .btn-close-custom:hover {
        opacity: 1;
        background: #ef4444;
        color: white;
        border-color: #ef4444;
    }

    /* Scrollbar customization */
    .offcanvas-body::-webkit-scrollbar {
        width: 3px;
    }
    .offcanvas-body::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 10px;
    }
</style>
</style>

<!-- Toggle Button -->
<div class="sidebar-toggle animatsiya1" data-bs-toggle="offcanvas" data-bs-target="#modernSidebar">
    <i class="fa-solid fa-bars-staggered"></i>
</div>

<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1" id="modernSidebar">
    <div class="sb-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="sb-title">TeachProfile</h3>
                <div class="sb-subtitle"><?= $lang === 'en' ? 'Portfolio Menu' : 'Portfolio Menyusi' ?></div>
            </div>
            <div class="btn-close-custom" data-bs-dismiss="offcanvas">
                <i class="fa-solid fa-xmark"></i>
            </div>
        </div>
    </div>

    <div class="offcanvas-body sb-menu">
        <div class="list-unstyled ps-0">
            <?php $u_param = isset($_SESSION['current_portfolio_user']) ? '?u=' . urlencode($_SESSION['current_portfolio_user']) : ''; ?>
            <!-- Education -->
            <div class="sb-item">
                <button class="sb-link-cat <?= $open_cat === 'edu' ? '' : 'collapsed' ?>" data-bs-toggle="collapse" data-bs-target="#edu-collapse">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <?= $lang === 'en' ? 'Education' : 'Ta\'lim' ?>
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </button>
                <div class="collapse <?= $open_cat === 'edu' ? 'show' : '' ?>" id="edu-collapse">
                    <ul class="sb-sub-menu">
                        <li><a href="<?= $p_links['edu'] . $u_param ?>#maktab" class="sb-sub-link"><?= $lang === 'en' ? 'School' : 'Maktab' ?></a></li>
                        <li><a href="<?= $p_links['edu'] . $u_param ?>#kollej" class="sb-sub-link"><?= $lang === 'en' ? 'College' : 'Kollej' ?></a></li>
                        <li><a href="<?= $p_links['edu'] . $u_param ?>#bakalavr" class="sb-sub-link"><?= $lang === 'en' ? 'Bachelor' : 'Bakalavriat' ?></a></li>
                        <li><a href="<?= $p_links['edu'] . $u_param ?>#magistr" class="sb-sub-link"><?= $lang === 'en' ? 'Master' : 'Magistratura' ?></a></li>
                        <li><a href="<?= $p_links['edu'] . $u_param ?>#phd" class="sb-sub-link">PhD</a></li>
                    </ul>
                </div>
            </div>

            <!-- Experience -->
            <div class="sb-item">
                <button class="sb-link-cat <?= $open_cat === 'exp' ? '' : 'collapsed' ?>" data-bs-toggle="collapse" data-bs-target="#exp-collapse">
                    <i class="fa-solid fa-briefcase"></i>
                    <?= $lang === 'en' ? 'Experience' : 'Tajriba' ?>
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </button>
                <div class="collapse <?= $open_cat === 'exp' ? 'show' : '' ?>" id="exp-collapse">
                    <ul class="sb-sub-menu">
                        <li><a href="<?= $p_links['exp'] . $u_param ?>#asosiy_1" class="sb-sub-link"><?= $lang === 'en' ? 'Main activity' : 'Asosiy faoliyat' ?></a></li>
                        <li><a href="<?= $p_links['exp'] . $u_param ?>#qoshimcha_1" class="sb-sub-link"><?= $lang === 'en' ? 'Additional activities' : 'Qo\'shimcha faoliyat' ?></a></li>
                    </ul>
                </div>
            </div>

            <!-- Publications -->
            <div class="sb-item">
                <button class="sb-link-cat <?= $open_cat === 'pub' ? '' : 'collapsed' ?>" data-bs-toggle="collapse" data-bs-target="#pub-collapse">
                    <i class="fa-solid fa-book-open"></i>
                    <?= $lang === 'en' ? 'Publications' : 'Nashrlar' ?>
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </button>
                <div class="collapse <?= $open_cat === 'pub' ? 'show' : '' ?>" id="pub-collapse">
                    <ul class="sb-sub-menu">
                        <li><a href="<?= $p_links['pub'] . $u_param ?>#scopus_j" class="sb-sub-link">Scopus Journals</a></li>
                        <li><a href="<?= $p_links['pub'] . $u_param ?>#scopus_k" class="sb-sub-link">Scopus Conferences</a></li>
                        <li><a href="<?= $p_links['pub'] . $u_param ?>#xalqaro_j" class="sb-sub-link"><?= $lang === 'en' ? 'International Journals' : 'Xalqaro jurnallar' ?></a></li>
                        <li><a href="<?= $p_links['pub'] . $u_param ?>#respublika_j" class="sb-sub-link"><?= $lang === 'en' ? 'Local Journals' : 'Respublika jurnallari' ?></a></li>
                        <li><a href="<?= $p_links['pub'] . $u_param ?>#dgu" class="sb-sub-link"><?= $lang === 'en' ? 'Software License' : 'DGU guvohnomalari' ?></a></li>
                        <li><a href="<?= $p_links['pub'] . $u_param ?>#qullanma" class="sb-sub-link"><?= $lang === 'en' ? 'Handbooks' : 'Uslubiy qo\'llanmalar' ?></a></li>
                    </ul>
                </div>
            </div>

            <!-- Teaching -->
            <div class="sb-item">
                <button class="sb-link-cat <?= $open_cat === 'teach' ? '' : 'collapsed' ?>" data-bs-toggle="collapse" data-bs-target="#teach-collapse">
                    <i class="fa-solid fa-person-chalkboard"></i>
                    <?= $lang === 'en' ? 'Teaching' : 'O\'qitish' ?>
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </button>
                <div class="collapse <?= $open_cat === 'teach' ? 'show' : '' ?>" id="teach-collapse">
                    <ul class="sb-sub-menu">
                        <li><a href="<?= $p_links['teach'] . $u_param ?>#asosiy_2" class="sb-sub-link"><?= $lang === 'en' ? 'Main subjects' : 'Asosiy fanlar' ?></a></li>
                        <li><a href="<?= $p_links['teach'] . $u_param ?>#qoshimcha_2" class="sb-sub-link"><?= $lang === 'en' ? 'Additional subjects' : 'Qo\'shimcha fanlar' ?></a></li>
                    </ul>
                </div>
            </div>

            <!-- Students -->
            <div class="sb-item">
                <button class="sb-link-cat <?= $open_cat === 'stud' ? '' : 'collapsed' ?>" data-bs-toggle="collapse" data-bs-target="#stud-collapse">
                    <i class="fa-solid fa-user-graduate"></i>
                    <?= $lang === 'en' ? 'Students' : 'Talabalar' ?>
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </button>
                <div class="collapse <?= $open_cat === 'stud' ? 'show' : '' ?>" id="stud-collapse">
                    <ul class="sb-sub-menu">
                        <li><a href="<?= $p_links['stud'] . $u_param ?>#bakalavr_1" class="sb-sub-link"><?= $lang === 'en' ? 'Bachelor' : 'Bakalavr' ?></a></li>
                        <li><a href="<?= $p_links['stud'] . $u_param ?>#magistr_1" class="sb-sub-link"><?= $lang === 'en' ? 'Master' : 'Magistr' ?></a></li>
                        <li><a href="<?= $p_links['stud'] . $u_param ?>#boshqa" class="sb-sub-link"><?= $lang === 'en' ? 'Others' : 'Boshqalar' ?></a></li>
                    </ul>
                </div>
            </div>

            <!-- Connection/Contact -->
            <div class="sb-item">
                <a href="<?= $p_links['conn'] . $u_param ?>" class="sb-link-cat <?= $current_page === $p_links['conn'] ? 'active' : '' ?>" style="text-decoration: none;">
                    <i class="fa-solid fa-paper-plane"></i>
                    <?= $lang === 'en' ? 'Contact' : 'Bog\'lanish' ?>
                </a>
            </div>

             <!-- Others -->
             <div class="sb-item">
                <button class="sb-link-cat <?= $open_cat === 'other' ? '' : 'collapsed' ?>" data-bs-toggle="collapse" data-bs-target="#other-collapse">
                    <i class="fa-solid fa-ellipsis-h"></i>
                    <?= $lang === 'en' ? 'Others' : 'Boshqa' ?>
                    <i class="fa-solid fa-chevron-down chevron"></i>
                </button>
                <div class="collapse <?= $open_cat === 'other' ? 'show' : '' ?>" id="other-collapse">
                    <ul class="sb-sub-menu">
                        <li><a href="<?= $p_links['other'] . $u_param ?>#oila" class="sb-sub-link"><?= $lang === 'en' ? 'Family' : 'Oila' ?></a></li>
                        <li><a href="<?= $p_links['other'] . $u_param ?>#yutuqlar" class="sb-sub-link"><?= $lang === 'en' ? 'Achievements' : 'Yutuqlar' ?></a></li>
                        <li><a href="<?= $p_links['other'] . $u_param ?>#qiziqishlar" class="sb-sub-link"><?= $lang === 'en' ? 'Interests' : 'Qiziqishlar' ?></a></li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
    
    <div class="p-4 border-top" style="border-color: var(--border-color) !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="modern-badge" style="margin: 0; background: linear-gradient(135deg, var(--primary-color), #0984e3); color: white; border: none; padding: 6px 15px; font-weight: 700; letter-spacing: 0.5px;">
                <i class="fa-solid fa-crown me-2"></i><?= $lang === 'en' ? 'v2.0 Premium' : 'v2.0 Premium' ?>
            </div>
        </div>
    </div>
</div>
