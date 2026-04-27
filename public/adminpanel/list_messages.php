<?php
include_once('db.php');
if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);

// Xabarlar va Baholashlarni alohida olish
$messages_res = $link->query("SELECT * FROM messages WHERE user_id = $uid AND (msg_type = 'message' OR msg_type IS NULL) ORDER BY created_at DESC");
$ratings_res  = $link->query("SELECT * FROM messages WHERE user_id = $uid AND msg_type = 'rating' ORDER BY created_at DESC");

// Statistika
$stats = $link->query("SELECT 
    AVG(r_content) as avg_content, 
    AVG(r_design) as avg_design, 
    AVG(r_func) as avg_func,
    COUNT(*) as total_ratings
    FROM messages WHERE user_id = $uid AND msg_type = 'rating'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xabarlar va Baholashlar</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>
</head>
<body>
<?php include_once("menu.php"); ?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: var(--text-primary);"><i class="fa-solid fa-gauge-high text-primary me-2"></i> Feedback Dashboard</h2>
        <div class="small fw-bold opacity-50" style="color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Oxirgi yangilanish: <?= date('H:i:s') ?></div>
    </div>

    <!-- Stats Row: Premium Redesign -->
    <?php if ($stats['total_ratings'] > 0): ?>
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card-modern p-4 h-100 animatsiya1">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon-box me-3 bg-success-subtle text-success"><i class="fa-solid fa-book-open"></i></div>
                    <div class="small fw-bold text-uppercase opacity-75" style="color: var(--text-muted);">Mazmun</div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="display-6 fw-bold" style="color: var(--text-primary);"><?= number_format($stats['avg_content'], 1) ?></span>
                    <span class="opacity-50" style="color: var(--text-muted);">/ 5</span>
                </div>
                <div class="progress mt-3" style="height: 6px; background: var(--bg-color);">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($stats['avg_content']/5)*100 ?>%; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card-modern p-4 h-100 animatsiya1" style="animation-delay: 0.1s;">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon-box me-3 bg-info-subtle text-info"><i class="fa-solid fa-palette"></i></div>
                    <div class="small fw-bold text-uppercase opacity-75" style="color: var(--text-muted);">Dizayn</div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="display-6 fw-bold" style="color: var(--text-primary);"><?= number_format($stats['avg_design'], 1) ?></span>
                    <span class="opacity-50" style="color: var(--text-muted);">/ 5</span>
                </div>
                <div class="progress mt-3" style="height: 6px; background: var(--bg-color);">
                    <div class="progress-bar bg-info" role="progressbar" style="width: <?= ($stats['avg_design']/5)*100 ?>%; border-radius: 10px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card-modern p-4 h-100 animatsiya1" style="animation-delay: 0.2s;">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon-box me-3 bg-purple bg-opacity-10" style="color: #8b5cf6;"><i class="fa-solid fa-code"></i></div>
                    <div class="small fw-bold text-uppercase opacity-75" style="color: var(--text-muted);">Funksiya</div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="display-6 fw-bold" style="color: var(--text-primary);"><?= number_format($stats['avg_func'], 1) ?></span>
                    <span class="opacity-50" style="color: var(--text-muted);">/ 5</span>
                </div>
                <div class="progress mt-3" style="height: 6px; background: var(--bg-color);">
                    <div class="progress-bar" role="progressbar" style="width: <?= ($stats['avg_func']/5)*100 ?>%; background-color: #8b5cf6; border-radius: 10px;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card-modern p-4 text-white position-relative overflow-hidden animatsiya1" style="animation-delay: 0.3s; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="position-absolute top-0 end-0 p-3 opacity-20"><i class="fa-solid fa-trophy fa-3x"></i></div>
                <div class="small fw-bold text-uppercase mb-2">Platforma Reytingi</div>
                <div class="display-5 fw-bold"><?= number_format(($stats['avg_content'] + $stats['avg_design'] + $stats['avg_func']) / 3, 1) ?></div>
                <div class="small opacity-75 mt-2">Umumiy foydalanuvchilar fikri</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row dashboard-container g-4">
        <!-- Messages Column -->
        <div class="col-lg-6">
            <h5 class="fw-bold mb-4 opacity-75" style="color: var(--primary-color);"><i class="fa-solid fa-envelope-open-text me-2"></i> Xabarlar (<?= $messages_res->num_rows ?>)</h5>
            <div class="scroll-column">
                <?php if ($messages_res->num_rows > 0): ?>
                    <?php while ($row = $messages_res->fetch_assoc()): 
                        $is_read = ($row['status'] === 'read');
                    ?>
                        <div class="feedback-card animatsiya1 <?= $is_read ? 'is-read' : '' ?>">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($row['name']) ?></h6>
                                    <div class="small opacity-50"><?= htmlspecialchars($row['email']) ?></div>
                                </div>
                                <div class="text-end">
                                    <div class="small opacity-50 mb-1" style="font-size: 11px;"><?= (new DateTime($row['created_at']))->format('d.m.Y H:i') ?></div>
                                    <?php if ($row['relationship']): ?>
                                        <span class="tag-badge"><i class="fa-solid fa-user-tag me-1"></i> <?= htmlspecialchars($row['relationship']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="content-box mb-3"><?= nl2br(htmlspecialchars($row['message_uz'])) ?></div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="read_message.php?id=<?= $row['id'] ?>" class="btn btn-sm <?= $is_read ? 'btn-outline-secondary' : 'btn-success' ?>" style="border-radius: 10px;">
                                    <i class="fa-solid <?= $is_read ? 'fa-envelope-open' : 'fa-check' ?> me-1"></i> <?= $is_read ? 'Yangi' : 'O\'qildi' ?>
                                </a>
                                <a href="delete_message.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 10px;" onclick="return confirm('O\'chirilsinmi?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5 opacity-50"><i class="fa-solid fa-inbox fa-3x mb-3"></i><p>Yangi xabarlar yo'q.</p></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ratings Column -->
        <div class="col-lg-6">
            <h5 class="fw-bold mb-4 opacity-75 text-rating"><i class="fa-solid fa-star-half-stroke me-2"></i> Baholashlar (<?= $ratings_res->num_rows ?>)</h5>
            <div class="scroll-column">
                <?php if ($ratings_res->num_rows > 0): ?>
                    <?php while ($row = $ratings_res->fetch_assoc()): 
                        $is_read = ($row['status'] === 'read');
                    ?>
                        <div class="feedback-card animatsiya1 <?= $is_read ? 'is-read' : '' ?> rating-accent">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold mb-1 text-info"><?= htmlspecialchars($row['name']) ?></h6>
                                    <div class="small opacity-50"><?= htmlspecialchars($row['email']) ?></div>
                                </div>
                                <div class="small opacity-50" style="font-size: 11px;"><?= (new DateTime($row['created_at']))->format('d.m.Y H:i') ?></div>
                            </div>
                            <div class="row g-2 mb-3 text-center">
                                <div class="col-4">
                                    <div class="bg-info bg-opacity-10 p-2 rounded text-info small">
                                        <div class="fw-bold"><?= $row['r_content'] ?></div><div>Mazmun</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded text-primary small">
                                        <div class="fw-bold"><?= $row['r_design'] ?></div><div>Dizayn</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-purple bg-opacity-10 p-2 rounded small" style="color:#8b5cf6; background: rgba(139,92,246,0.1);">
                                        <div class="fw-bold"><?= $row['r_func'] ?></div><div>Funksiya</div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($row['message_uz']): ?>
                                <div class="content-box mb-3" style="font-style: italic; border-left: 3px solid #0ea5e9;">"<?= htmlspecialchars($row['message_uz']) ?>"</div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="read_message.php?id=<?= $row['id'] ?>" class="btn btn-sm <?= $is_read ? 'btn-outline-secondary' : 'btn-success' ?>" style="border-radius: 10px;">
                                    <i class="fa-solid <?= $is_read ? 'fa-envelope-open' : 'fa-check' ?>"></i>
                                </a>
                                <a href="delete_message.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" style="border-radius: 10px;" onclick="return confirm('O\'chirilsinmi?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5 opacity-50"><i class="fa-solid fa-star fa-3x mb-3"></i><p>Hozircha baholar yo'q.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.js"></script>
</body>
</html>

