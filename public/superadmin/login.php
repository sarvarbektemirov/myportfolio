<?php
/**
 * Super Admin Login Page
 */
include_once("includes/db.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Iltimos, barcha maydonlarni to'ldiring!";
    } else {
        // Specifically check for 'admin' user or users with superadmin privileges
        $stmt = $master_link->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $user = $res->fetch_assoc();
            
            // Check if user has Super Admin role (role = 1)
            if ($user['role'] == 1 && password_verify($password, $user['password_hash'])) {
                // Success!
                $_SESSION['super_admin_id'] = $user['id'];
                $_SESSION['super_admin_user'] = $user['username'];
                $_SESSION['super_admin_name'] = $user['firstname'] . ' ' . $user['lastname'];
                
                $_SESSION['super_admin_role'] = 'superadmin';
                $_SESSION['auth_time'] = time();
                
                header("Location: index.php");
                exit;
            } else {
                $error = "Kirish taqiqlangan yoki parol noto'g'ri.";
            }
        } else {
            $error = "Bunday foydalanuvchi tizimda mavjud emas.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login | Portfolio SaaS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../js/theme.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --accent: #8b5cf6;
            --accent-glow: rgba(139, 92, 246, 0.4);
            --text-primary: #000000;
            --text-secondary: #1e293b;
            --border: rgba(0, 0, 0, 0.08);
            --input-bg: #ffffff;
        }

        [data-theme="dark"] {
            --bg-color: #030509;
            --card-bg: rgba(18, 22, 33, 0.7);
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --border: rgba(255, 255, 255, 0.1);
            --input-bg: rgba(0, 0, 0, 0.4);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at 50% 50%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            color: var(--text-primary);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            padding: 3rem;
            border-radius: 30px;
            border: 1px solid var(--border);
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo i {
            display: block;
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #8b5cf6, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .logo p {
            color: var(--text-secondary);
            margin-top: 5px;
            font-size: 0.9rem;
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            text-align: center;
            display: <?= $error ? 'block' : 'none' ?>;
        }

        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            background: var(--input-bg) !important;
            border: 1px solid var(--border);
            padding: 16px 20px;
            padding-right: 50px;
            border-radius: 15px;
            color: var(--text-primary) !important;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }

        /* Autofill rangini to'g'irlash */
        .input-wrapper input:-webkit-autofill,
        .input-wrapper input:-webkit-autofill:hover, 
        .input-wrapper input:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--text-primary) !important;
            -webkit-box-shadow: 0 0 0px 1000px var(--input-bg) inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .input-wrapper input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.3s;
            background: none;
            border: none;
            padding: 5px;
            outline: none;
        }

        .password-toggle:hover {
            color: var(--accent);
        }

        .login-btn {
            width: 100%;
            background: var(--accent);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 15px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: 0 10px 15px -3px var(--accent-glow);
        }
        .footer-links {
            text-align: center;
            margin-top: 3rem;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo">
            <i class="fas fa-shield-alt"></i>
            <h2>SuperAdmin Access</h2>
            <p>Tizimni boshqarish uchun kiring.</p>
        </div>

        <div class="error-msg"><?= htmlspecialchars($error) ?></div>

        <?php if (isset($_GET['reason']) && $_GET['reason'] === 'session_reset'): ?>
            <div class="error-msg" style="display: block; background: rgba(139, 92, 246, 0.1); border-color: rgba(139, 92, 246, 0.3); color: var(--accent);">
                <i class="fas fa-info-circle me-1"></i> Tizim yangilandi. Xavfsizlik nuqtai nazaridan qayta kiring.
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group">
                <label>Username</label>
                <div class="input-wrapper">
                    <input type="text" name="username" placeholder="admin" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>

            <div class="input-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="adminPassword" placeholder="••••••••" required>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="login-btn">Secure Login</button>
        </form>

        <div class="footer-links">
            Foydalanuvchi paneliga qaytish: <a href="../adminpanel/login.php">Kirish</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('adminPassword');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

</body>
</html>
