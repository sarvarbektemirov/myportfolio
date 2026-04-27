<?php 
require_once "superadmin/includes/db.php";
$p1 = getSetting($master_link, 'price_1_month', '49,000');
$p3 = getSetting($master_link, 'price_3_month', '129,000');
$p12 = getSetting($master_link, 'price_12_month', '399,000');
// Convert to '49k' format for display
$p1_display = str_replace([',000', '000'], 'k', $p1);
$p3_display = str_replace([',000', '000'], 'k', $p3);
$p12_display = str_replace([',000', '000'], 'k', $p12);

// Real Stats from Database
$total_users_query = $master_link->query("SELECT COUNT(*) as count FROM users WHERE (role = 0 OR role IS NULL) AND deleted_at IS NULL");
$total_users = ($total_users_query) ? $total_users_query->fetch_assoc()['count'] : 0;
$display_users = $total_users; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyPortfolio | Build Your Professional Brand</title>
    <link rel="icon" href="rasmlar/my_favicon.png">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stats-section {
            padding: 5rem 10%;
            background: rgba(255, 255, 255, 0.03);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .stat-item h2 {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 5px;
            font-family: 'Inter', sans-serif;
            font-weight: 800;
        }
        .stat-item p {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        @media (max-width: 768px) {
            .stats-section { padding: 3rem 5%; }
            .stat-item h2 { font-size: 2.2rem; }
        }
    </style>
</head>
<body>
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <nav class="navbar">
        <a href="#" class="logo">
            <div class="logo-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <span>MyPortfolio</span>
        </a>
        <div class="nav-links">
            <a href="#features">Features</a>
            <a href="#about">About</a>
            <a href="#pricing">Pricing</a>
        </div>
        <div class="nav-btns">
            <a href="adminpanel/login.php" class="btn btn-primary" style="padding: 10px 24px;">Kirish</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <span class="hero-tag">New Platform Launch</span>
            <h1>Build Your Professional Brand in Minutes.</h1>
            <p>The ultimate multi-tenant portfolio builder. Showcase your work, academic publications, and teaching experience with a stunning, theme-aware interface.</p>
            <div style="display: flex; gap: 20px;">
                <a href="adminpanel/register.php" class="btn btn-primary">Start Building Free</a>
                <a href="en/home.php?u=abdurahim" target="_blank" class="btn btn-outline">Explore Live Demo</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-img-container">
                <img src="saas_hero_mockup_1776365043961.png" alt="SaaS Dashboard Mockup">
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <span style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 13px; letter-spacing: 2px;">Why Choose Us</span>
            <h2>Platform Powerhouses</h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">Everything you need to launch a professional online presence, built with security and aesthetics in mind.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-database"></i>
                </div>
                <h3>Isolated Data</h3>
                <p>Each user gets their own dedicated database environment. Your data is secure, isolated, and perfectly managed.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-moon"></i>
                </div>
                <h3>Theme Aware</h3>
                <p>Fully adaptive interfaces. Switch between light and dark modes with a single click while maintaining perfect contrast.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-language"></i>
                </div>
                <h3>Multi-Language</h3>
                <p>Broaden your reach. Native support for Uzbek and English ensures your portfolio resonates with both local and global audiences.</p>
            </div>
        </div>
    </section>
    
    <section class="stats-section">
        <div class="stats-container">
            <div class="stat-item">
                <h2><?= number_format($display_users) ?>+</h2>
                <p>Jami Portfoliolar</p>
            </div>
            <div class="stat-item">
                <h2>98%</h2>
                <p>Mamnun foydalanuvchilar</p>
            </div>
            <div class="stat-item">
                <h2>24/7</h2>
                <p>Texnik ko'mak</p>
            </div>
            <div class="stat-item">
                <h2>15+</h2>
                <p>Dizayn shablonlari</p>
            </div>
        </div>
    </section>

    <section class="pricing" id="pricing">
        <div class="section-header">
            <span style="color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 13px; letter-spacing: 2px;">Pricing Plans</span>
            <h2>Choose Your Growth</h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">Select the plan that fits your professional journey. Upgrade or cancel anytime.</p>
        </div>

        <div class="pricing-grid">
            <!-- 1 Month -->
            <div class="price-card">
                <h3>Boshlang'ich</h3>
                <div class="price-value"><?= $p1_display ?> <span>/ oy</span></div>
                <p class="price-desc">Yangi boshlovchilar uchun</p>
                <ul class="price-features">
                    <li><i class="fas fa-check"></i> <span>1 ta Portfolio yaratish</span></li>
                    <li><i class="fas fa-check"></i> <span>Cheksiz nashrlar</span></li>
                    <li><i class="fas fa-check"></i> <span>Tungi rejim</span></li>
                    <li><i class="fas fa-check"></i> <span>Standart yordam</span></li>
                </ul>
                <a href="adminpanel/register.php?plan=1" class="btn btn-outline">Tanlash</a>
            </div>

            <!-- 12 Months -->
            <div class="price-card featured">
                <div class="popular-badge">Eng Foydali</div>
                <h3>Yillik Reja</h3>
                <div class="price-value"><?= $p12_display ?> <span>/ yil</span></div>
                <p class="price-desc">Yiliga 30% chegirma</p>
                <ul class="price-features">
                    <li><i class="fas fa-check"></i> <span>Barcha Pro funksiyalar</span></li>
                    <li><i class="fas fa-check"></i> <span>Priority (Ustuvor) yordam</span></li>
                    <li><i class="fas fa-check"></i> <span>Maxsus SEO sozlamalari</span></li>
                    <li><i class="fas fa-check"></i> <span>Statistika paneli</span></li>
                </ul>
                <a href="adminpanel/register.php?plan=12" class="btn btn-primary">Hozir boshlang</a>
            </div>

            <!-- 3 Months -->
            <div class="price-card">
                <h3>Kvadrat Reja</h3>
                <div class="price-value"><?= $p3_display ?> <span>/ 3 oy</span></div>
                <p class="price-desc">O'rtacha muddat uchun</p>
                <ul class="price-features">
                    <li><i class="fas fa-check"></i> <span>Cheksiz imkoniyatlar</span></li>
                    <li><i class="fas fa-check"></i> <span>3 oylik to'liq kirish</span></li>
                    <li><i class="fas fa-check"></i> <span>Eksport qilish (Kelgusida)</span></li>
                    <li><i class="fas fa-check"></i> <span>Premium yordam</span></li>
                </ul>
                <a href="adminpanel/register.php?plan=3" class="btn btn-outline">Tanlash</a>
            </div>
        </div>
    </section>

    <footer style="padding: 4rem 10%; border-top: 1px solid var(--border); text-align: center;">
        <p style="color: var(--text-muted); font-size: 0.9rem;">&copy; 2026 MyPortfolio Inc. All rights reserved. <br> Powered by Modern Web Technology.</p>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>
