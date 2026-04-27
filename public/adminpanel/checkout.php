<?php
session_start();
include_once("db.php");
include_once("../superadmin/includes/db.php"); // To get prices

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$plan_id = $_GET['plan'] ?? '1'; // Default to 1 month

// Fetch plan details from settings
$p1 = getSetting($master_link, 'price_1_month', '49,000');
$p3 = getSetting($master_link, 'price_3_month', '129,000');
$p12 = getSetting($master_link, 'price_12_month', '399,000');

$prices = [
    '1' => $p1,
    '3' => $p3,
    '12' => $p12
];

$plan_names = [
    '1' => 'Boshlang\'ich (1 oylik)',
    '3' => 'Kvadrat (3 oylik)',
    '12' => 'Yillik (12 oylik)'
];

$current_price = $prices[$plan_id] ?? $p1;
$current_plan_name = $plan_names[$plan_id] ?? $plan_names['1'];

// Handle Payment Simulation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $method = $_POST['method'] ?? 'payme';
    $months = (int)$plan_id;
    
    // Calculate new expiry date
    // If user already has an active subscription, extend it. Otherwise, start from now.
    $user_res = $master_link->query("SELECT subscription_expires_at FROM users WHERE id = $user_id");
    $user_data = $user_res->fetch_assoc();
    
    $current_expiry = strtotime($user_data['subscription_expires_at'] ?? 'now');
    if ($current_expiry < time()) $current_expiry = time();
    
    $new_expiry = date('Y-m-d H:i:s', strtotime("+$months months", $current_expiry));
    
    $stmt = $master_link->prepare("UPDATE users SET subscription_plan = ?, subscription_expires_at = ? WHERE id = ?");
    $stmt->bind_param("ssi", $plan_id, $new_expiry, $user_id);
    
    if ($stmt->execute()) {
        header("Location: billing.php?success=payment_complete");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To'lov | Portfolio SaaS</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: #8b5cf6;
            --secondary: #06b6d4;
            --bg: #030509;
            --card-bg: rgba(18, 22, 33, 0.7);
            --border: rgba(255, 255, 255, 0.08);
            --text: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.15) 0%, transparent 40%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .checkout-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 30px;
            width: 100%;
            max-width: 900px;
            display: flex;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .summary-side {
            flex: 1;
            padding: 3rem;
            background: rgba(0, 0, 0, 0.2);
            border-right: 1px solid var(--border);
        }

        .payment-side {
            flex: 1.2;
            padding: 3rem;
        }

        .plan-badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(139, 92, 246, 0.1);
            color: var(--primary);
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        h2 { font-weight: 700; margin-bottom: 0.5rem; letter-spacing: -1px; }
        .price { font-size: 3rem; font-weight: 800; color: var(--text); margin: 1.5rem 0; }
        .price span { font-size: 1rem; color: var(--text-muted); font-weight: 400; }

        .feature-list { list-style: none; padding: 0; margin-top: 2rem; }
        .feature-list li { margin-bottom: 12px; display: flex; align-items: center; gap: 10px; color: var(--text-muted); font-size: 0.95rem; }
        .feature-list li i { color: var(--primary); font-size: 0.8rem; }

        .method-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 2rem;
        }

        .method-card {
            border: 2px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .method-card:hover { border-color: rgba(139, 92, 246, 0.3); background: rgba(255, 255, 255, 0.02); }

        .method-card input { position: absolute; opacity: 0; cursor: pointer; }

        .method-card.active { border-color: var(--primary); background: rgba(139, 92, 246, 0.05); }

        .method-logo { height: 30px; margin-bottom: 10px; filter: grayscale(1) brightness(2); transition: 0.3s; }
        .method-card.active .method-logo { filter: none; }

        .btn-pay {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 18px;
            border-radius: 18px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 20px -5px rgba(139, 92, 246, 0.4);
        }

        .btn-pay:hover { transform: translateY(-2px); box-shadow: 0 15px 25px -5px rgba(139, 92, 246, 0.5); }

        @media (max-width: 768px) {
            .checkout-card { flex-direction: column; }
            .summary-side { border-right: none; border-bottom: 1px solid var(--border); }
        }
    </style>
</head>
<body>

    <div class="checkout-card">
        <div class="summary-side">
            <div class="plan-badge">Selected Plan</div>
            <h2><?= htmlspecialchars($current_plan_name) ?></h2>
            <p style="color: rgba(255,255,255,0.6); font-size: 0.95rem; line-height: 1.6;">Barcha imkoniyatlardan to'liq foydalanish uchun obunani faollashtiring.</p>
            
            <div class="price"><?= $current_price ?> <span>so'm</span></div>

            <ul class="feature-list">
                <li><i class="fas fa-circle"></i> <span>Domen va Xosting xizmati</span></li>
                <li><i class="fas fa-circle"></i> <span>Cheksiz ma'lumotlar bazasi</span></li>
                <li><i class="fas fa-circle"></i> <span>Professional Portfolio dizayni</span></li>
                <li><i class="fas fa-circle"></i> <span>24/7 Texnik yordam</span></li>
            </ul>

            <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                <a href="billing.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem;">
                    <i class="fas fa-arrow-left me-2"></i> Ortga qaytish
                </a>
            </div>
        </div>

        <div class="payment-side">
            <h4 style="font-weight: 600; margin-bottom: 2rem;">To'lov usulini tanlang</h4>
            
            <form action="" method="POST">
                <input type="hidden" name="plan" value="<?= $plan_id ?>">
                
                <div class="method-selector">
                    <div class="method-card active" onclick="selectMethod(this, 'payme')">
                        <input type="radio" name="method" value="payme" checked>
                        <img src="https://cdn.payme.uz/logo/payme_color.svg" class="method-logo" alt="Payme">
                        <div style="font-size: 0.8rem; font-weight: 600;">Payme</div>
                    </div>
                    <div class="method-card" onclick="selectMethod(this, 'click')">
                        <input type="radio" name="method" value="click">
                        <img src="https://click.uz/click/images/logo.png" class="method-logo" alt="Click" style="height: 25px;">
                        <div style="font-size: 0.8rem; font-weight: 600;">Click Up</div>
                    </div>
                </div>

                <div style="background: rgba(255, 255, 255, 0.03); padding: 25px; border-radius: 20px; border: 1px solid var(--border); margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span class="text-muted">Subtotal</span>
                        <span><?= $current_price ?> so'm</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed var(--border);">
                        <span class="text-muted">Komissiya</span>
                        <span style="color: #10b981;">0.00 so'm</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.2rem;">
                        <span>Jami:</span>
                        <span style="color: var(--primary);"><?= $current_price ?> so'm</span>
                    </div>
                </div>

                <button type="submit" name="pay" class="btn-pay">
                    <i class="fas fa-lock me-2"></i> To'lovni tasdiqlash
                </button>
            </form>

            <p style="text-align: center; margin-top: 1.5rem; font-size: 0.8rem; color: var(--text-muted);">
                Xavfsiz to'lov tizimi. Sizning ma'lumotlaringiz shifrlangan holda saqlanadi.
            </p>
        </div>
    </div>

    <script>
        function selectMethod(el, method) {
            document.querySelectorAll('.method-card').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            el.querySelector('input').checked = true;
        }
    </script>
</body>
</html>
