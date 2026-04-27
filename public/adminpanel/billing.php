<?php
include_once("db.php");
include_once("../superadmin/includes/db.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$user_res = $master_link->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_res ? $user_res->fetch_assoc() : null;

if (!$user) {
    die("Foydalanuvchi ma'lumotlari topilmadi.");
}

$expires_at = $user['subscription_expires_at'];
$plan_id = $user['subscription_plan'] ?? 'free';

$plan_names = [
    'free' => 'Sinov muddati',
    '1' => 'Boshlang\'ich (1 oylik)',
    '3' => 'Kvadrat (3 oylik)',
    '12' => 'Yillik (12 oylik)'
];

$plan_name = $plan_names[$plan_id] ?? $plan_names['free'];

// Calculate days remaining
$days_left = 0;
$progress = 0;
if ($expires_at) {
    $remaining = strtotime($expires_at) - time();
    $days_left = ceil($remaining / (60 * 60 * 24));
    if ($days_left < 0) $days_left = 0;
    
    // Total days based on plan
    $total_days = 30; // default for 1 month or free
    if ($plan_id == '3') $total_days = 90;
    elseif ($plan_id == '12') $total_days = 365;
    
    // Progress bar logic - should show how much is LEFT
    $progress = ($days_left / $total_days) * 100;
    if ($progress > 100) $progress = 100;
}

$success_msg = '';
if (isset($_GET['success']) && $_GET['success'] === 'payment_complete') {
    $success_msg = "To'lov muvaffaqiyatli amalga oshirildi! Obuna muddati uzaytirildi.";
}

include_once("menu.php");
?>

<style>
    .billing-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 24px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .billing-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, var(--accent-primary), transparent);
        border-radius: 50%;
        opacity: 0.1;
        z-index: 0;
    }
    .plan-info h2 { font-weight: 700; font-size: 2rem; margin-bottom: 0.5rem; }
    .status-active-pill {
        display: inline-block;
        padding: 5px 12px;
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .progress-container {
        margin-top: 2rem;
        background: rgba(255, 255, 255, 0.05);
        height: 12px;
        border-radius: 6px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--accent-primary), #06b6d4);
        border-radius: 6px;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pricing-tier {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 1.5rem;
        transition: 0.3s;
    }
    .pricing-tier:hover {
        background: rgba(255, 255, 255, 0.04);
        border-color: var(--accent-primary);
        transform: translateY(-5px);
    }
</style>

<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4 fw-bold">Obuna va To'lovlar</h1>

            <?php if ($success_msg): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4 animatsiya1" style="background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 15px;">
                    <i class="fas fa-check-circle me-2"></i> <?= $success_msg ?>
                </div>
            <?php endif; ?>

            <div class="billing-card shadow-sm animatsiya1">
                <div class="row align-items-center position-relative" style="z-index: 1;">
                    <div class="col-md-7 plan-info">
                        <div class="status-active-pill">Faol obuna</div>
                        <h2><?= htmlspecialchars($plan_name) ?></h2>
                        <p class="text-muted">Keyingi to'lov sanasi: <span class="text-white fw-semibold"><?= $expires_at ? date('d.m.Y', strtotime($expires_at)) : 'Noma\'lum' ?></span></p>
                        
                        <div style="margin-top: 2rem;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Qolgan vaqt</span>
                                <span class="fw-bold"><?= $days_left ?> kun</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar-fill" style="width: <?= number_format($progress, 2, '.', '') ?>%; background: linear-gradient(90deg, #8b5cf6, #3b82f6);"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-4 mt-md-0">
                        <div class="display-4 fw-bold mb-0"><?= $days_left ?></div>
                        <div class="text-muted text-uppercase small ls-1">Kun qoldi</div>
                    </div>
                </div>
            </div>

            <h4 class="mb-4 fw-bold">Tarifni yangilash yoki uzaytirish</h4>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="pricing-tier animatsiya1" style="animation-delay: 0.1s;">
                        <div class="text-muted small mb-1">1 oylik</div>
                        <h5 class="fw-bold mb-3">Boshlang'ich</h5>
                        <div class="h3 fw-bold mb-4"><?= getSetting($master_link, 'price_1_month', '49,000') ?> <small style="font-size: 0.8rem; font-weight: 400;">so'm</small></div>
                        <a href="checkout.php?plan=1" class="btn btn-outline-primary w-100 rounded-pill">Tanlash</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pricing-tier animatsiya1" style="animation-delay: 0.2s; border-color: var(--accent-primary); background: rgba(139, 92, 246, 0.05);">
                        <div class="text-muted small mb-1">3 oylik</div>
                        <h5 class="fw-bold mb-3">Kvadrat</h5>
                        <div class="h3 fw-bold mb-4"><?= getSetting($master_link, 'price_3_month', '129,000') ?> <small style="font-size: 0.8rem; font-weight: 400;">so'm</small></div>
                        <a href="checkout.php?plan=3" class="btn btn-primary w-100 rounded-pill shadow-sm">Eng ommabop</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pricing-tier animatsiya1" style="animation-delay: 0.3s;">
                        <div class="text-muted small mb-1">12 oylik</div>
                        <h5 class="fw-bold mb-3">Yillik</h5>
                        <div class="h3 fw-bold mb-4"><?= getSetting($master_link, 'price_12_month', '399,000') ?> <small style="font-size: 0.8rem; font-weight: 400;">so'm</small></div>
                        <a href="checkout.php?plan=12" class="btn btn-outline-primary w-100 rounded-pill">Tanlash</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="panel animatsiya1" style="height: auto;">
                <div class="panel-title mb-4">To'lov Tarixi</div>
                <div class="text-center py-5">
                    <div class="mb-3 text-muted opacity-20"><i class="fas fa-receipt fa-4x"></i></div>
                    <p class="text-muted small">Hozircha to'lovlar tarixi mavjud emas.</p>
                </div>
            </div>
            
            <div class="billing-card mt-4 animatsiya1" style="animation-delay: 0.2s; background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(6, 182, 212, 0.05)); border-color: rgba(139, 92, 246, 0.2); padding: 1.8rem;">
                <h5 class="fw-bold mb-3 d-flex align-items-center">
                    <div style="width: 35px; height: 35px; background: var(--accent-primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                        <i class="fas fa-headset text-white" style="font-size: 0.9rem;"></i>
                    </div>
                    Yordam kerakmi?
                </h5>
                <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.5rem;">
                    To'lovlar yoki obuna bilan bog'liq muammolar bo'lsa, texnik yordam bo'limiga murojaat qiling.
                </p>
                <a href="https://t.me/ssa1var" target="_blank" class="btn btn-sm btn-primary rounded-pill px-4 py-2 fw-bold" style="font-size: 0.8rem;">
                    Bog'lanish <i class="fas fa-external-link-alt ms-1" style="font-size: 0.7rem;"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
