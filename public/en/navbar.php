<style>
    .modern-nav {
        background: var(--card-bg) !important;
        border-bottom: 1px solid var(--border-color);
        padding: 0;
        transition: all 0.3s ease;
    }
    .modern-nav .nav-link {
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark) !important;
        padding: 18px 20px !important;
        transition: all 0.2s;
        border-bottom: 2px solid transparent;
    }
    .modern-nav .nav-link:hover {
        color: var(--primary-color) !important;
        background: var(--bg-color);
    }
    .modern-nav .nav-link.active {
        color: var(--primary-color) !important;
        border-bottom-color: var(--primary-color);
        background: var(--badge-bg);
    }
    .navbar-toggler {
        border: none;
        padding: 10px;
        color: var(--text-dark) !important;
    }
    .navbar-toggler:focus {
        box-shadow: none;
    }
</style>

<nav class="navbar navbar-expand-md modern-nav sticky-top">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#modernNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="modernNavbar">
            <ul class="navbar-nav w-100 justify-content-center align-items-center">
                <?php $u_param = isset($_SESSION['current_portfolio_user']) ? '?u=' . urlencode($_SESSION['current_portfolio_user']) : ''; ?>
                <li class="nav-item">
                    <?php $is_home = (basename($_SERVER['PHP_SELF']) == 'home.php' || basename($_SERVER['PHP_SELF']) == 'index.php'); ?>
                    <a class="nav-link <?= $is_home ? 'active' : '' ?>" href="<?= $p_links['home'] . $u_param ?>"><?= $lang === 'en' ? 'Home' : 'Asosiy' ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == $p_links['edu'] ? 'active' : '' ?>" href="<?= $p_links['edu'] . $u_param ?>"><?= $lang === 'en' ? 'Education' : 'Ta\'lim' ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == $p_links['exp'] ? 'active' : '' ?>" href="<?= $p_links['exp'] . $u_param ?>"><?= $lang === 'en' ? 'Experience' : 'Tajriba' ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == $p_links['pub'] ? 'active' : '' ?>" href="<?= $p_links['pub'] . $u_param ?>"><?= $lang === 'en' ? 'Publications' : 'Nashrlar' ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == $p_links['teach'] ? 'active' : '' ?>" href="<?= $p_links['teach'] . $u_param ?>"><?= $lang === 'en' ? 'Teaching' : 'O\'qitish' ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == $p_links['stud'] ? 'active' : '' ?>" href="<?= $p_links['stud'] . $u_param ?>"><?= $lang === 'en' ? 'Students' : 'Talabalar' ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == $p_links['other'] ? 'active' : '' ?>" href="<?= $p_links['other'] . $u_param ?>"><?= $lang === 'en' ? 'Others' : 'Boshqa' ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == $p_links['conn'] ? 'active' : '' ?>" href="<?= $p_links['conn'] . $u_param ?>"><?= $lang === 'en' ? 'Connection' : 'Aloqa' ?></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
