<?php
session_start();
include_once("db.php");

$error = '';
$success = '';

// Plan Detection
$plan_id = $_GET['plan'] ?? '';
$plan_names = [
    '1' => 'Boshlang\'ich (1 oylik)',
    '3' => 'Kvadrat (3 oylik)',
    '12' => 'Yillik (12 oylik)'
];
$selected_plan_name = $plan_names[$plan_id] ?? '';


// Step 1: Processing Registration Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (empty($firstname) || empty($lastname) || empty($email) || empty($username) || empty($password)) {
        $error = "Iltimos, barcha maydonlarni to'ldiring!";
    } else {
        // Check uniqueness of username or email
        $check = $master_link->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $check->bind_param("ss", $email, $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Bu email yoki username allaqachon ro'yxatdan o'tgan.";
        } else {
            // Generate Verification Code
            $code = rand(100000, 999999);
            
            // Calculate Subscription Expiration
            $plan_months = (int)$plan_id;
            if ($plan_months <= 0) $plan_months = 1; // Default to 1 month for free/unspecified
            $expires_at = date('Y-m-d H:i:s', strtotime("+$plan_months months"));
            $plan_type = $plan_id ? $plan_id : 'free';

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $master_link->prepare("INSERT INTO users (username, firstname, lastname, email, password_hash, verification_code, subscription_plan, subscription_expires_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssiss", $username, $firstname, $lastname, $email, $hashed, $code, $plan_type, $expires_at);

            
            if ($stmt->execute()) {
                $_SESSION['verify_id'] = $stmt->insert_id; // hold user id temporarily
                $_SESSION['verify_email'] = $email;
                
                // Send Real Email using Helper
                require_once 'mail_helper.php';
                sendVerificationEmail($email, $code);

                $success = "Ro'yxatdan o'tdingiz! Kodni kiriting.";
                echo "<script>
                        setTimeout(function(){ 
                            var myModal = new bootstrap.Modal(document.getElementById('verifyModal'));
                            myModal.show();
                        }, 500);
                      </script>";
            } else {
                $error = "Xatolik yuz berdi: " . $master_link->error;
            }
        }
    }
}

// Step 2: Processing Verification Code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify') {
    $code_input = trim($_POST['verification_code'] ?? '');
    if (!empty($_SESSION['verify_id'])) {
        $vid = $_SESSION['verify_id'];
        
        $stmt = $master_link->prepare("SELECT verification_code FROM users WHERE id = ?");
        $stmt->bind_param("i", $vid);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if ($row['verification_code'] == $code_input) {
                // Success!
                $upd = $master_link->prepare("UPDATE users SET email_verified = 1, verification_code = NULL WHERE id = ?");
                $upd->bind_param("i", $vid);
                $upd->execute();
                
                // Retrieve user info for session
                $userRes = $master_link->query("SELECT * FROM users WHERE id = $vid");
                $user = $userRes->fetch_assoc();
                
                // Login User
                $_SESSION['id'] = $user['id'];
                $_SESSION['ism'] = $user['firstname'] . ' ' . $user['lastname'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 1; 
                
                // --- MULTI-DB: Create and Initialize User Database ---
                $new_db = "portfolio_" . $user['username'];
                $master_link->query("CREATE DATABASE IF NOT EXISTS `$new_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                $user_link = new mysqli('mysql-8.4', 'root', '', $new_db);
                if (!$user_link->connect_error) {
                    require_once 'schema.php';
                    initialize_user_db($user_link);
                    $user_link->close();
                }
                
                // Redirect based on plan
                if (!empty($user['subscription_plan']) && $user['subscription_plan'] !== 'free') {
                    header("Location: checkout.php?plan=" . urlencode($user['subscription_plan']));
                } else {
                    header("Location: admin.php");
                }
                exit;
            } else {
                $error = "Kod noto'g'ri. Iltimos qayta urinib ko'ring.";
                echo "<script>
                        setTimeout(function(){ 
                            var myModal = new bootstrap.Modal(document.getElementById('verifyModal'));
                            myModal.show();
                        }, 500);
                      </script>";
            }
        }
    } else {
        $error = "Sessiya muddati tugagan. Qaytadan ro'yxatdan o'ting.";
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ro'yxatdan o'tish | SaaS Portfolio</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <script src="js/theme.js"></script>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card" style="max-width: 500px;">
        <div class="auth-header">
            <div class="mb-3 text-primary">
                <i class="fa-solid fa-user-plus fa-3x"></i>
            </div>
            <h2>Hush kelibsiz!</h2>
            <p>Yangi hisob yarating</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger px-3 py-2 border-0 rounded-3 shadow-sm animatsiya1">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($selected_plan_name)): ?>
            <div class="alert alert-info px-3 py-2 border-0 rounded-3 shadow-sm animatsiya1" style="background: rgba(13, 202, 240, 0.1); color: #0dcaf0; border: 1px solid rgba(13, 202, 240, 0.2) !important;">
                <i class="fa-solid fa-star me-2"></i> Tanlangan: <strong><?= htmlspecialchars($selected_plan_name) ?></strong>
            </div>
        <?php endif; ?>


        <form action="" method="POST" class="animatsiya1">
            <input type="hidden" name="action" value="register">
            
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <label class="form-label fw-semibold small">Ismingiz</label>
                    <input type="text" name="firstname" class="form-control" required placeholder="Ali" value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-semibold small">Familiyangiz</label>
                    <input type="text" name="lastname" class="form-control" required placeholder="Valiyev" value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Yagona Username</label>
                <div class="input-group">
                    <span class="input-group-text">@</span>
                    <input type="text" name="username" class="form-control border-start-0" required placeholder="alivaliyev" pattern="[a-zA-Z0-9_]+" title="Lotun harf, raqam va _" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Email manzil</label>
                <input type="email" name="email" class="form-control" required placeholder="ali@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Parol</label>
                <div class="input-group">
                    <input type="password" name="password" id="regPassword" class="form-control border-end-0" required placeholder="Kuchsiz parol qo'ymang">
                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="const p = document.getElementById('regPassword'); p.type = p.type === 'password' ? 'text' : 'password';">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-submit w-100 py-3 rounded-3 shadow-sm">
                Ro'yxatdan o'tish <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <span class="text-secondary small">Allaqachon hisobingiz bormi?</span> 
            <a href="login.php" class="text-primary fw-bold text-decoration-none ms-1">Kirish</a>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verifyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; background: var(--card-bg);">
      <div class="modal-header border-0 text-center flex-column pb-0 mt-3">
          <div style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 15px;">
              <i class="fa-regular fa-envelope"></i>
          </div>
        <h5 class="modal-title w-100 fw-bold" style="color: var(--text-primary);">Emailni tasdiqlang</h5>
      </div>
      <form action="" method="POST">
          <input type="hidden" name="action" value="verify">
          <div class="modal-body text-center px-4 pt-2">
            <p class="text-secondary mb-4">Biz <strong><?= htmlspecialchars($_SESSION['verify_email'] ?? 'sizning pochtangiz') ?></strong> manziliga 6 xonali kod yubordik.</p>
            <input type="number" name="verification_code" class="form-control form-control-lg text-center fw-bold mb-3" placeholder="------" style="letter-spacing: 5px; font-size: 1.5rem;" required>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="register.php?cancel_verify=1" class="text-decoration-none small text-muted">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Emailni o'zgartirish
                </a>
                <button type="submit" name="action" value="resend" class="btn btn-link p-0 text-decoration-none small fw-bold text-primary">
                    <i class="fa-solid fa-rotate-right me-1"></i> Kodni qayta yuborish
                </button>
            </div>
          </div>
          <div class="modal-footer border-0 d-flex justify-content-center pb-4">
            <button type="submit" name="action" value="verify" class="btn btn-submit px-5 rounded-pill py-2">Tasdiqlash</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
