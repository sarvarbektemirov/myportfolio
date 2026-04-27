<?php
// Hozirgi sahifa nomini olish
$current_page = basename($_SERVER['PHP_SELF']);

// Kutilayotgan (pending) xabarlar sonini olish
if (!isset($link)) {
    include_once(__DIR__ . '/db.php');
}
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 0);
$_msg_count = 0;
if (isset($link) && $link instanceof mysqli) {
    $_msg_res = $link->query("SELECT COUNT(*) as c FROM messages WHERE status = 'pending' AND user_id = $uid");
    $_msg_count = $_msg_res ? (int)$_msg_res->fetch_assoc()['c'] : 0;
}
?>
<link rel="stylesheet" href="../css/bootstrap.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="css/extra.css">
<script src="../js/bootstrap.bundle.js"></script>

<?php
// Theme handling is now centralized in extra.css
?>

<script src="../js/theme.js"></script>


<nav class="navbar navbar-expand-lg navbar-modern sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="admin.php">
            <i class="fa-solid fa-user-shield me-2 fs-4"></i>
            ADMIN PANEL
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="collapsibleNavbar">
            <ul class="navbar-nav ms-auto gap-1 align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?php if($current_page == 'admin.php') echo 'active'; ?>" href="admin.php">
                        <i class="fa-solid fa-chart-line"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php if($current_page == 'list_messages.php') echo 'active'; ?>" href="list_messages.php" style="position:relative;">
                        <i class="fa-solid fa-comments"></i> Xabarlar
                        <?php if ($_msg_count > 0): ?>
                        <span class="msg-badge"><?= $_msg_count ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php if($current_page == 'billing.php' || $current_page == 'checkout.php') echo 'active'; ?>" href="billing.php">
                        <i class="fa-solid fa-credit-card"></i> Obuna
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php if($current_page == 'support.php') echo 'active'; ?>" href="support.php">
                        <i class="fa-solid fa-headset"></i> Bog'lanish
                    </a>
                </li>

                <!-- QO'SHISH -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php if(in_array($current_page, ['add_home.php', 'add_header.php', 'add_talim.php', 'add_nashr.php', 'add_carousel.php', 'add_student.php', 'add_footer.php', 'add_publication.php'])) echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-plus"></i> Qo'shish
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?php if($current_page == 'add_home.php') echo 'active'; ?>" href="add_home.php"><i class="fa-solid fa-house"></i> Asosiy sahifa</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'add_header.php') echo 'active'; ?>" href="add_header.php"><i class="fa-solid fa-id-card"></i> Sarlavha</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'add_talim.php') echo 'active'; ?>" href="add_talim.php"><i class="fa-solid fa-graduation-cap"></i> Ta'lim</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'add_nashr.php') echo 'active'; ?>" href="add_nashr.php"><i class="fa-solid fa-briefcase"></i> Tajriba/Nashr</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'add_carousel.php') echo 'active'; ?>" href="add_carousel.php"><i class="fa-solid fa-images"></i> Tajriba rasmi</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'add_publication.php') echo 'active'; ?>" href="add_publication.php"><i class="fa-solid fa-book-open"></i> Publication</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'add_student.php') echo 'active'; ?>" href="add_student.php"><i class="fa-solid fa-user-graduate"></i> Student</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'add_footer.php') echo 'active'; ?>" href="add_footer.php"><i class="fa-solid fa-address-book"></i> Footer</a></li>
                    </ul>
                </li>

                <!-- TAHRIRLASH -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php if(in_array($current_page, ['edit_home.php', 'edit_header.php', 'edit_footer.php', 'list_talim.php', 'list_nashr.php', 'list_carousel.php', 'list_publication.php', 'list_student.php'])) echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-pen-to-square"></i> Tahrirlash
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item <?php if($current_page == 'edit_home.php') echo 'active'; ?>" href="edit_home.php"><i class="fa-solid fa-house-chimney"></i> Asosiy sahifa</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'edit_header.php') echo 'active'; ?>" href="edit_header.php"><i class="fa-solid fa-id-card-clip"></i> Sarlavha</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'edit_footer.php') echo 'active'; ?>" href="edit_footer.php"><i class="fa-solid fa-address-card"></i> Footer</a></li>
                        <div class="dropdown-divider"></div>
                        <li><a class="dropdown-item <?php if($current_page == 'list_talim.php') echo 'active'; ?>" href="list_talim.php"><i class="fa-solid fa-list-check"></i> Ta'lim ro'yxati</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'list_nashr.php') echo 'active'; ?>" href="list_nashr.php"><i class="fa-solid fa-business-time"></i> Tajriba ro'yxati</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'list_carousel.php') echo 'active'; ?>" href="list_carousel.php"><i class="fa-solid fa-photo-film"></i> Tajriba rasmlari</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'list_publication.php') echo 'active'; ?>" href="list_publication.php"><i class="fa-solid fa-copy"></i> Publications</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'list_student.php') echo 'active'; ?>" href="list_student.php"><i class="fa-solid fa-users"></i> Studentlar</a></li>
                    </ul>
                </li>

                <!-- O'CHIRISH -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php if(in_array($current_page, ['delete_home.php', 'delete_header.php', 'delete_footer.php', 'delete_talim.php', 'delete_nashr.php', 'delete_carousel.php', 'delete_publication.php', 'delete_student.php'])) echo 'active'; ?>" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-trash-can"></i> O'chirish
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item <?php if($current_page == 'delete_home.php') echo 'active'; ?>" href="delete_home.php"><i class="fa-solid fa-eraser"></i> Asosiy sahifa</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'delete_header.php') echo 'active'; ?>" href="delete_header.php"><i class="fa-solid fa-user-minus"></i> Sarlavha</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'delete_footer.php') echo 'active'; ?>" href="delete_footer.php"><i class="fa-solid fa-phone-slash"></i> Footer</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'delete_talim.php') echo 'active'; ?>" href="delete_talim.php"><i class="fa-solid fa-graduation-cap"></i> Ta'lim</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'delete_nashr.php') echo 'active'; ?>" href="delete_nashr.php"><i class="fa-solid fa-briefcase"></i> Tajriba</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'delete_carousel.php') echo 'active'; ?>" href="delete_carousel.php"><i class="fa-solid fa-image"></i> Tajriba rasmi</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'delete_publication.php') echo 'active'; ?>" href="delete_publication.php"><i class="fa-solid fa-file-excel"></i> Publication</a></li>
                        <li><a class="dropdown-item <?php if($current_page == 'delete_student.php') echo 'active'; ?>" href="delete_student.php"><i class="fa-solid fa-user-xmark"></i> Student</a></li>
                    </ul>
                </li>

                <li class="nav-item mx-2">
                    <button class="theme-toggle-btn" onclick="toggleTheme()" title="Rejimni o'zgartirish">
                        <i class="fa-solid fa-moon theme-toggle-icon"></i>
                    </button>
                </li>
                
                <li class="nav-item ms-lg-2">
                    <a class="nav-link btn btn-danger text-white px-3 py-2 d-inline-flex align-items-center" style="border-radius: 10px;" href="logout.php" onclick="return confirm('Haqiqatdan ham chiqmoqchimisiz?');">
                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Chiqish
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

