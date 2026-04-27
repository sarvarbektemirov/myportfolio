<?php
session_start();
include_once("db.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username_or_email = trim($_POST['username'] ?? '');
    $password          = $_POST['password'] ?? '';

    if (empty($username_or_email) || empty($password)) {
        $error = "Iltimos, login va parolni kiriting!";
    } else {
        $stmt = $master_link->prepare("SELECT * FROM users WHERE (username = ? OR email = ?)");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $user = $res->fetch_assoc();
            
            // Check if email is verified
            if ($user['email_verified'] == 0) {
                // Not verified, force verification
                $_SESSION['verify_id'] = $user['id'];
                $_SESSION['verify_email'] = $user['email'];
                $error = "Hisobingiz email orqali tasdiqlanmagan!";
                
                // Show modal instantly
                $show_verify_modal = true;
            } else {
                // Verify password
                if (password_verify($password, $user['password_hash'])) {
                    // Success
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['ism'] = $user['firstname'] . ' ' . $user['lastname'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = 1; 
                    $_SESSION['auth_time'] = time();
                    header("Location: admin.php");
                    exit;
                } else {
                    $error = "Parol noto'g'ri.";
                }
            }
        } else {
            $error = "Bunday foydalanuvchi topilmadi.";
        }
    }
}

// Handling verification inside login if needed
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
                
                $userRes = $master_link->query("SELECT * FROM users WHERE id = $vid");
                $user = $userRes->fetch_assoc();
                
                $_SESSION['id'] = $user['id'];
                $_SESSION['ism'] = $user['firstname'] . ' ' . $user['lastname'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 1; 
                $_SESSION['auth_time'] = time();
                
                header("Location: admin.php");
                exit;
            } else {
                $error = "Tasdiqlash kodi noto'g'ri.";
                $show_verify_modal = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirish | SaaS Portfolio</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <script src="js/theme.js"></script>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-header">
            <div class="mb-3 text-primary">
                <i class="fa-solid fa-shield-halved fa-3x"></i>
            </div>
            <h2>Tizimga kirish</h2>
            <p>Xush kelibsiz, davom etish uchun kiring.</p>
        </div>

        <?php if (isset($_GET['reason']) && $_GET['reason'] === 'session_reset'): ?>
            <div class="alert alert-warning px-3 py-2 border-0 rounded-3 shadow-sm animatsiya1 mb-3">
                <i class="fa-solid fa-circle-info me-2"></i>Tizim administrator tomonidan yangilandi. Xavfsizlik uchun qayta kiring.
            </div>
        <?php endif; ?>

        <?php if (!empty($error) && empty($show_verify_modal)): ?>
            <div class="alert alert-danger px-3 py-2 border-0 rounded-3 shadow-sm animatsiya1">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($show_verify_modal)): ?>
            <div class="alert alert-warning px-3 py-2 border-0 rounded-3 shadow-sm animatsiya1">
                <i class="fa-solid fa-envelope-open-text me-2"></i>Hisob tasdiqlanmagan. Kodni kiriting.
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="animatsiya1">
            <input type="hidden" name="action" value="login">
            
            <div class="mb-3">
                <label class="form-label fw-semibold small">Username yoki Email</label>
                <input type="text" name="username" class="form-control" required placeholder="alivaliyev" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Parol</label>
                <div class="input-group">
                    <input type="password" name="password" id="logPassword" class="form-control border-end-0" required placeholder="Parolni kiriting">
                    <button class="btn btn-outline-secondary border-start-0" type="button" onclick="const p = document.getElementById('logPassword'); p.type = p.type === 'password' ? 'text' : 'password';">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-submit w-100 py-3 rounded-3 shadow-sm">
                Kirish <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i>
            </button>
        </form>

        <div class="text-center mt-4 pt-2 border-top">
            <span class="text-secondary small">Hali hisobingiz yo'qmi?</span> 
            <a href="register.php" class="text-primary fw-bold text-decoration-none ms-1">Ro'yxatdan o'tish</a>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verifyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; background: var(--card-bg);">
      <div class="modal-header border-0 text-center flex-column pb-0 mt-3">
          <div style="width: 60px; height: 60px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 15px;">
              <i class="fa-regular fa-envelope"></i>
          </div>
        <h5 class="modal-title w-100 fw-bold" style="color: var(--text-primary);">Emailni tasdiqlang</h5>
      </div>
      <form action="" method="POST">
          <input type="hidden" name="action" value="verify">
          <div class="modal-body text-center px-4 pt-2">
            <p class="text-secondary mb-4">Biz <strong><?= htmlspecialchars($_SESSION['verify_email'] ?? '') ?></strong> manziliga 6 xonali kod yubordik.</p>
            
            <input type="number" name="verification_code" class="form-control form-control-lg text-center fw-bold mb-3" placeholder="------" style="letter-spacing: 5px; font-size: 1.5rem;" required>
            
            <?php if (!empty($error) && !empty($show_verify_modal)): ?>
                <div class="text-danger small fw-bold"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
          </div>
          <div class="modal-footer border-0 d-flex justify-content-center pb-4">
            <button type="submit" class="btn btn-submit px-5 rounded-pill">Tasdiqlash</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="../js/bootstrap.bundle.js"></script>
<?php if (!empty($show_verify_modal)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('verifyModal'));
        myModal.show();
    });
</script>
<?php endif; ?>
</body>
</html>
