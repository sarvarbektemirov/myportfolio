<?php
include_once("includes/auth.php");
include_once("includes/db.php");

$msg = "";
$msg_type = "success";

// --- 1. Handle Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_branding'])) {
        setSetting($master_link, 'platform_name', $_POST['platform_name']);
        setSetting($master_link, 'system_email', $_POST['system_email']);
        setSetting($master_link, 'copyright_text', $_POST['copyright_text']);
        $msg = "Brending sozlamalari muvaffaqiyatli saqlandi.";
    } 
    elseif (isset($_POST['update_pricing'])) {
        setSetting($master_link, 'price_1_month', $_POST['price_1_month']);
        setSetting($master_link, 'price_3_month', $_POST['price_3_month']);
        setSetting($master_link, 'price_12_month', $_POST['price_12_month']);
        $msg = "Tarif rejalarining narxlari yangilandi.";
    }
    elseif (isset($_POST['toggle_maintenance'])) {

        $mode = isset($_POST['maintenance_mode']) ? '1' : '0';
        setSetting($master_link, 'maintenance_mode', $mode);
        $msg = "Maintenance rejim " . ($mode === '1' ? 'YOQILDI' : 'O\'CHIRILDI');
    }
    elseif (isset($_POST['logout_all_sessions'])) {
        setSetting($master_link, 'last_session_reset', time());
        $msg = "Barcha faol sessiyalar zudlik bilan tugatildi!";
        $msg_type = "danger";
    }
}

// --- 2. Fetch Current Settings ---
$p_name = getSetting($master_link, 'platform_name', 'Portfolio SaaS');
$s_email = getSetting($master_link, 'system_email', 'admin@myportfolio.local');
$c_text = getSetting($master_link, 'copyright_text', '&copy; 2026 MyPortfolio Inc.');
$m_mode = getSetting($master_link, 'maintenance_mode', '0');

// Prices
$p1 = getSetting($master_link, 'price_1_month', '49,000');
$p3 = getSetting($master_link, 'price_3_month', '129,000');
$p12 = getSetting($master_link, 'price_12_month', '399,000');


include_once("includes/header.php");
include_once("includes/sidebar.php");
?>

<main class="main-content">
    <header class="header">
        <div class="header-info">
            <h1>Tizim sozlamalari</h1>
            <p style="color: var(--text-secondary);">SaaS portfoliolari platformangiz uchun global sozlamalar.</p>
        </div>
        
        <button type="submit" form="brandingForm" class="btn-action" style="background: var(--accent-primary); border: none;">O'zgarishlarni saqlash</button>
    </header>

    <!-- Alert Message -->
    <?php if(!empty($msg)): ?>
        <div class="alert alert-<?= $msg_type ?> animatsiya1" style="padding: 1rem; border-radius: 12px; margin-bottom: 2rem; background: rgba(<?= $msg_type === 'success' ? '16, 185, 129' : '239, 68, 68' ?>, 0.1); color: var(--<?= $msg_type === 'success' ? 'success' : 'danger' ?>); border: 1px solid rgba(<?= $msg_type === 'success' ? '16, 185, 129' : '239, 68, 68' ?>, 0.2);">
            <i class="fas fa-check-circle me-2"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="content-grid" style="grid-template-columns: 1fr 1fr;">
        <!-- General Settings -->
        <div class="panel">
            <div class="panel-title"><i class="fas fa-cog me-2"></i> Platforma brendi</div>
            <form id="brandingForm" action="" method="POST" style="margin-top: 1rem;">
                <input type="hidden" name="update_branding" value="1">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 8px;">Platforma nomi</label>
                    <input type="text" name="platform_name" value="<?= htmlspecialchars($p_name) ?>" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-primary); padding: 10px 15px; border-radius: 10px; outline: none;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 8px;">Mualliflik huquqi matni</label>
                    <input type="text" name="copyright_text" value="<?= htmlspecialchars($c_text) ?>" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-primary); padding: 10px 15px; border-radius: 10px; outline: none;">
                </div>
                <div>
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 8px;">Tizim emaili</label>
                    <input type="email" name="system_email" value="<?= htmlspecialchars($s_email) ?>" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-primary); padding: 10px 15px; border-radius: 10px; outline: none;">
                </div>
            </form>
        </div>

        <!-- Security & Admin -->
        <div class="panel">
            <div class="panel-title"><i class="fas fa-shield-alt me-2"></i> SuperAdmin holati va texnik ishlar</div>
            <div style="margin-top: 1rem;">
                <form action="" method="POST">
                    <input type="hidden" name="toggle_maintenance" value="1">
                    <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(239, 68, 68, 0.05); padding: 20px; border-radius: 12px; border: 1px dashed var(--danger);">
                        <div style="padding-right: 20px;">
                            <div style="font-weight: 600; font-size: 0.95rem; color: var(--text-primary);">Texnik tanaffus rejimi</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 4px;">Barcha portfoliolarga kirishni zudlik bilan o'chirish.</div>
                        </div>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                 <?php if($m_mode == '1'): ?>
                                    <a href="../adminpanel/check_diag.php" target="_blank" style="color: var(--accent-secondary); font-size: 0.75rem; text-decoration: none; border-bottom: 1px dashed var(--accent-secondary);">Sahifani ko'rish</a>
                                 <?php endif; ?>
                                 <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 26px;">
                                <input type="checkbox" name="maintenance_mode" <?= $m_mode == '1' ? 'checked' : '' ?> onchange="this.form.submit()" style="opacity: 0; width: 0; height: 0;">
                                <span class="slider round" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?= $m_mode == '1' ? 'var(--danger)' : '#334155' ?>; transition: .4s; border-radius: 34px;">
                                    <span style="position: absolute; content: ''; height: 18px; width: 18px; left: <?= $m_mode == '1' ? '28px' : '4px' ?>; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                </form>
                
                <div style="margin-top: 2rem;">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 8px;">Super Admin ma'lumotlari</label>
                    <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; border: 1px solid var(--border-color); font-size: 0.9rem;">
                        <i class="fas fa-user-circle me-2"></i> Tizimda faol: <span style="color: var(--accent-secondary); font-weight: 500;">@<?= $_SESSION['super_admin_user'] ?? 'admin' ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing Settings -->
        <div class="panel">
            <div class="panel-title"><i class="fas fa-tags me-2"></i> Tariflar narxini boshqarish</div>
            <form id="pricingForm" action="" method="POST" style="margin-top: 1.5rem;">
                <input type="hidden" name="update_pricing" value="1">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 8px;">1 oylik (so'mda)</label>
                        <input type="text" name="price_1_month" value="<?= htmlspecialchars($p1) ?>" placeholder="49,000" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-primary); padding: 10px 15px; border-radius: 10px; outline: none;">
                    </div>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 8px;">3 oylik (so'mda)</label>
                        <input type="text" name="price_3_month" value="<?= htmlspecialchars($p3) ?>" placeholder="129,000" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-primary); padding: 10px 15px; border-radius: 10px; outline: none;">
                    </div>
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 8px;">12 oylik (so'mda)</label>
                    <input type="text" name="price_12_month" value="<?= htmlspecialchars($p12) ?>" placeholder="399,000" style="width: 100%; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-primary); padding: 10px 15px; border-radius: 10px; outline: none;">
                </div>
                <button type="submit" class="btn-action" style="width: 100%; height: 45px; background: var(--accent-secondary); color: white; border: none; font-weight: 600;">Narxlarni yangilash</button>
            </form>
        </div>


        <!-- System Maintenance -->
        <div class="panel" style="grid-column: span 2; margin-top: 1rem;">
            <div class="panel-title">Tizimni sozlash asboblari</div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1rem;">
                <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 15px; border: 1px solid var(--border-color);">
                    <div style="font-weight: 600; margin-bottom: 5px;">Keshni tozalash</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 15px;">Vaqtinchalik fayllar va resurslar keshini tozalaydi.</div>
                    <button class="btn-action" style="width: 100%;">Bajarish</button>
                </div>
                <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 15px; border: 1px solid var(--border-color);">
                    <div style="font-weight: 600; margin-bottom: 5px;">Bazani optimizatsiya qilish</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 15px;">Mijozlar bazalaridagi jadvallarni optimizatsiya qiladi.</div>
                    <button class="btn-action" style="width: 100%;" onclick="location.href='databases.php'">Global ishga tushirish</button>
                </div>
                <div style="background: rgba(239, 68, 68, 0.05); padding: 20px; border-radius: 15px; border: 1px solid var(--danger);">
                    <div style="font-weight: 600; color: var(--danger); margin-bottom: 5px;">Barcha sessiyadan chiqarish</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 15px;">Zudlik bilan barcha foydalanuvchilarni profilidan chiqaradi.</div>
                    <form action="" method="POST" onsubmit="return confirm('Haqiqatan ham barcha foydalanuvchilarni tizimdan chiqarib yubormoqchimisiz?');">
                        <input type="hidden" name="logout_all_sessions" value="1">
                        <button type="submit" class="btn-action" style="width: 100%; border-color: var(--danger); color: var(--danger);">Bajarish</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

</div> <!-- End .admin-container -->
</body>
</html>
