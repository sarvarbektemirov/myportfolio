<?php
include_once('db.php');

// --- PREMIUM MAINTENANCE PREVIEW ---
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Preview: Texnik ishlar | Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #030509;
            --primary: #8b5cf6;
            --secondary: #06b6d4;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(139, 92, 246, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(6, 182, 212, 0.08) 0%, transparent 40%);
            color: white;
            font-family: 'Outfit', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            overflow: hidden;
        }
        .container {
            padding: 3rem;
            max-width: 550px;
            border-radius: 40px;
            background: rgba(18, 22, 33, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo-icon {
            font-size: 5rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: float 4s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        h1 {
            font-size: 2.2rem;
            font-weight: 600;
            margin: 0 0 1.2rem 0;
            letter-spacing: -1px;
        }
        p {
            color: #94a3b8;
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }
        .badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(139, 92, 246, 0.1);
            color: var(--primary);
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(139, 92, 246, 0.2);
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .loader {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255,255,255,0.05);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            margin: 0 auto;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .back-btn { margin-top: 2rem; color: #475569; font-size: 0.8rem; cursor: pointer; text-decoration: none; display: block; }
        .back-btn:hover { color: var(--primary); }
    </style>
</head>
<body>
    <div class="container">
        <div class="badge">Platform Maintenance</div>
        <div class="logo-icon">✨</div>
        <h1>Texnik ishlar ketmoqda</h1>
        <p>Mijozlarimizga yanada yaxshi xizmat ko'rsatish maqsadida platformada profilaktika ishlari olib borilmoqda. Tez orada barchasi odatdagidek ishlaydi.</p>
        <div class="loader"></div>
        <a href="javascript:window.close()" class="back-btn"><i class="fas fa-times"></i> Oynani yopish</a>
    </div>
</body>
</html>
