<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 0.8rem; box-shadow: none;">SA</div>
        <span>SuperAdmin</span>
    </div>
    
    <div class="sidebar-scroll">
        <div style="font-size: 0.65rem; font-weight: 800; color: var(--text-secondary); margin: 1rem 0 1rem 1rem; letter-spacing: 2px; text-transform: uppercase; opacity: 0.6;">Menyu</div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> <span>Bosh sahifa</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="users.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> <span>Portfoliolar</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="admins.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admins.php' ? 'active' : '' ?>">
                    <i class="fas fa-user-shield"></i> <span>Adminlar</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="databases.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'databases.php' ? 'active' : '' ?>">
                    <i class="fas fa-server"></i> <span>Ma'lumotlar bazasi</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="backups.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'backups.php' ? 'active' : '' ?>">
                    <i class="fas fa-cloud-arrow-down"></i> <span>Zaxira nusxasi</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="analytics.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i> <span>Analitika</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="trash.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'trash.php' ? 'active' : '' ?>">
                    <i class="fas fa-trash"></i> <span>Korzinka</span>
                </a>
            </li>
            <li class="nav-item">
                <?php
                $unread_total = 0;
                if (isset($master_link)) {
                    $unread_res = $master_link->query("SELECT COUNT(*) as cnt FROM system_messages WHERE is_read = 0 AND sender_role = 0");
                    if ($unread_res) $unread_total = $unread_res->fetch_assoc()['cnt'];
                }
                ?>
                <a href="messages.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i> 
                    <span>Xabarlar</span>
                    <?php if ($unread_total > 0): ?>
                        <span style="
                            background: #ff4757; 
                            color: white; 
                            font-size: 0.7rem; 
                            font-weight: 700; 
                            padding: 2px 8px; 
                            border-radius: 20px; 
                            margin-left: auto;
                            box-shadow: 0 2px 5px rgba(255, 71, 87, 0.3);
                        "><?= $unread_total ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
        
        <div style="margin: 2.5rem 0 1rem 1rem; font-size: 0.65rem; font-weight: 800; color: var(--text-secondary); letter-spacing: 2px; text-transform: uppercase; opacity: 0.6;">Tizim</div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="settings.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i> <span>Sozlamalar</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="javascript:void(0)" class="nav-link" onclick="toggleTheme()" id="themeToggleBtn">
                    <i class="fas fa-circle-half-stroke"></i> <span>Mavzuni o'zgartirish</span>
                </a>
            </li>
            <li class="nav-item" style="margin-top: 2rem;">
                <a href="logout.php" class="nav-link" style="color: var(--danger); background: rgba(239, 68, 68, 0.05);">
                    <i class="fas fa-power-off"></i> <span>Chiqish</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
