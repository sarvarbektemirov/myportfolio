<?php
include_once(__DIR__ . "/init.php");

// Header jadvalidan ism va familiya
$p_uid = $portfolio_user_id ?? 1;
$header_result = $link->query("SELECT ism, familiya FROM header WHERE user_id = $p_uid LIMIT 1");
if ($header_result && $header_result->num_rows > 0) {
    $header_row = $header_result->fetch_assoc();
} else {
    $header_row = null;
}

// Footer ma'lumotlari
$footer_result = $link->query("SELECT * FROM footer WHERE user_id = $p_uid LIMIT 1");
if ($footer_result && $footer_result->num_rows > 0) {
    $row = $footer_result->fetch_assoc();
} else {
    $row = null;
}
?>

<style>
    .modern-footer {
        background: var(--card-bg);
        border-top: 1px solid var(--border-color);
        padding: 80px 0 40px;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.3s ease;
    }
    .footer-heading {
        font-family: 'DM Serif Display', serif;
        font-size: 1.25rem;
        color: var(--text-dark);
        margin-bottom: 25px;
    }
    .footer-link {
        color: var(--text-muted);
        text-decoration: none;
        font-size: 14px;
        transition: all 0.2s;
        display: block;
        padding: 5px 0;
    }
    .footer-link:hover {
        color: var(--primary-color);
        padding-left: 5px;
    }
    .footer-text {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.6;
    }
    .footer-tag {
        font-weight: 600;
        color: var(--text-dark);
    }
    .social-links {
        display: flex;
        gap: 12px;
    }
    .social-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        color: var(--text-dark);
        transition: all 0.2s;
        text-decoration: none;
    }
    .social-btn:hover {
        background: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
        transform: translateY(-3px);
    }
    .modern-footer .border-top {
        border-color: var(--border-color) !important;
    }
</style>

<footer class="modern-footer">
    <div class="container">
        <?php if ($row): ?>
            <div class="row g-5">
                <!-- Left: Profile Info -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-heading">
                        <?= htmlspecialchars(($header_row['familiya'] ?? '') . ' ' . ($header_row['ism'] ?? '')) ?>
                    </h5>
                    <p class="footer-text mb-4">
                        <?php 
                        if ($lang === 'en') {
                            echo htmlspecialchars(!empty($row['bio_en']) ? $row['bio_en'] : (!empty($row['bio_uz']) ? $row['bio_uz'] : 'Academic researcher and educator specializing in technological innovation and specialized software development.'));
                        } else {
                            echo htmlspecialchars(!empty($row['bio_uz']) ? $row['bio_uz'] : 'Akademik tadqiqotchi va pedagog, texnologik innovatsiyalar va ixtisoslashtirilgan dasturiy ta\'minotni ishlab chiqish bo\'yicha mutaxassis.');
                        }
                        ?>
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <div class="footer-text">
                            <span class="footer-tag">ORCID:</span> <?= htmlspecialchars($row['orcid'] ?? '') ?>
                        </div>
                        <a href="<?= htmlspecialchars($row['cv_fayl'] ?? '#') ?>" class="footer-link d-flex align-items-center gap-2">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <?= $lang === 'en' ? 'Download CV' : 'Rezyumeni yuklab olish' ?>
                        </a>
                    </div>
                </div>

                <!-- Center: Quick Stats -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-heading"><?= $lang === 'en' ? 'Profile Status' : 'Profil holati' ?></h5>
                    <ul class="list-unstyled">
                        <li class="footer-text mb-2">
                            <span class="footer-tag"><?= $lang === 'en' ? 'Last Update:' : 'Oxirgi yangilanish:' ?></span> 
                            <?php 
                                $u_upd_res = $master_link->query("SELECT last_portfolio_update FROM users WHERE id = $p_uid");
                                $u_upd_val = ($u_upd_res && $u_upd_row = $u_upd_res->fetch_assoc()) ? $u_upd_row['last_portfolio_update'] : ($row['site_launch_date'] ?? '');
                                echo date('d.m.Y', strtotime($u_upd_val));
                            ?>
                        </li>
                        <li class="footer-text mb-2">
                            <span class="footer-tag"><?= $lang === 'en' ? 'Views:' : 'Tashriflar:' ?></span> 
                            <?php 
                                $v_res = $master_link->query("SELECT views FROM users WHERE id = $p_uid");
                                $v_count = ($v_res && $v_row = $v_res->fetch_assoc()) ? $v_row['views'] : 0;
                                echo number_format($v_count);
                            ?>
                        </li>
                        <li class="footer-text mb-2">
                            <span class="footer-tag"><?= $lang === 'en' ? 'Status:' : 'Holati:' ?></span> 
                            <?php 
                            if ($lang === 'en') {
                                echo htmlspecialchars(!empty($row['status_en']) ? $row['status_en'] : (!empty($row['status_uz']) ? $row['status_uz'] : 'Active Development'));
                            } else {
                                echo htmlspecialchars(!empty($row['status_uz']) ? $row['status_uz'] : 'Faol rivojlanish bosqichida');
                            }
                            ?>
                        </li>
                        <li class="footer-text">
                            <span class="footer-tag"><?= $lang === 'en' ? 'Portal:' : 'Portal:' ?></span> 
                            <?= $lang === 'en' ? 'English Version 2.0' : 'O\'zbekcha talqin 2.0' ?>
                        </li>
                    </ul>
                </div>

                <!-- Right: Platform Info -->
                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-heading"><?= $lang === 'en' ? 'Connect' : 'Bog\'lanish' ?></h5>
                    <div class="social-links mb-4">
                        <?php if (!empty($row['tg_link'])): ?>
                        <a href="<?= htmlspecialchars($row['tg_link']) ?>" class="social-btn" title="Telegram">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.14-.26.26-.534.26l.194-2.822 5.124-4.63c.222-.196-.051-.304-.344-.11l-6.325 3.983-2.72-.85c-.593-.186-.606-.593.124-.88l10.582-4.08c.49-.18.914.12.775.82z"/></svg>
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($row['wa_link'])): ?>
                        <a href="<?= htmlspecialchars($row['wa_link']) ?>" class="social-btn" title="WhatsApp">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($row['scopus_link'])): ?>
                        <a href="<?= htmlspecialchars($row['scopus_link']) ?>" class="social-btn" title="Scopus">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 7l11 5 9-4.09V17h2V7L12 2z"></path></svg>
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($row['scholar_link'])): ?>
                        <a href="<?= htmlspecialchars($row['scholar_link']) ?>" class="social-btn" title="Google Scholar">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M21.508 1.048L12.016 5.38 2.508 1.048v10.957l9.508 4.331 9.508-4.331V1.048zm-9.508 11.87l-6.508-2.964V4.608l6.508 2.965 6.508-2.965v5.346l-6.508 2.964zM12.016 16.5l-9.508-4.331V17.5l9.508 4.332 9.508-4.332v-5.331l-9.508 4.331z"/></svg>
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($row['university_link'])): ?>
                        <a href="<?= htmlspecialchars($row['university_link']) ?>" class="social-btn" title="University">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V20h2V9L12 3z"></path></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Bottom: Copyright -->
            <div class="d-flex flex-column flex-sm-row justify-content-between py-4 mt-5 border-top">
                <p class="footer-text mb-0">
                    &copy; <?= date("Y") ?> <?= htmlspecialchars($header_row['ism'] ?? 'ARE') ?>. 
                    <?php 
                    if ($lang === 'en') {
                        echo htmlspecialchars(!empty($row['copyright_en']) ? $row['copyright_en'] : (!empty($row['copyright_uz']) ? $row['copyright_uz'] : 'Crafted for Professional Excellence.'));
                    } else {
                        echo htmlspecialchars(!empty($row['copyright_uz']) ? $row['copyright_uz'] : 'Professional mukammallik uchun yaratilgan.');
                    }
                    ?>
                </p>
                <div class="d-flex gap-4 mt-3 mt-sm-0">
                    <p class="footer-text mb-0"><?= htmlspecialchars($row['email'] ?? '') ?></p>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <p class="italic" style="color: var(--text-muted);"><?= $lang === 'en' ? 'Footer information hasn\'t been added yet. Visit the Admin Panel to customize your profile.' : 'Footer ma\'lumotlari hali qo\'shilmagan. Profilingizni sozlash uchun Admin Panelga o\'ting.' ?></p>
            </div>
        <?php endif; ?>
    </div>
</footer>
