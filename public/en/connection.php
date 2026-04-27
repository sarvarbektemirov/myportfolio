<?php
include_once("init.php");
include_once("sarlavha.php");
include_once("navbar.php");
include_once("sidebar.php");
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portfolio</title>
  <link rel="icon" href="../rasmlar/my_favicon.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/bootstrap.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css" />
  <link rel="stylesheet" href="../css/boots_style.css">
  <link rel="stylesheet" href="../css/modern_style.css">
  <style>
    .contact-card-choice {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 15px;
        color: var(--text-dark);
    }
    .contact-card-choice:hover {
        transform: translateY(-5px);
        border-color: var(--primary-color);
        box-shadow: var(--shadow);
    }
    .contact-icon {
        width: 60px;
        height: 60px;
        background: var(--badge-bg);
        color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 5px;
    }
    .contact-title {
        font-family: 'DM Serif Display', serif;
        font-size: 1.25rem;
        color: var(--text-dark);
        margin: 0;
    }
    .contact-desc {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.5;
    }
    .form-label-modern {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-dark);
        margin-bottom: 8px;
    }
    .form-control-modern {
        border-radius: 10px;
        border: 1px solid var(--border-color);
        background: var(--bg-color);
        color: var(--text-dark);
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.2s;
    }
    .form-control-modern:focus {
        border-color: var(--primary-color);
        background: var(--bg-color);
        color: var(--text-dark);
        box-shadow: 0 0 0 3px rgba(29, 158, 117, 0.1);
        outline: none;
    }
    .btn-submit-modern {
        background: var(--primary-color);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-submit-modern:hover {
        background: #168a65;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(29, 158, 117, 0.2);
    }
    .modal-content-modern {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 24px !important;
        color: var(--text-dark);
    }
  </style>
</head>

<body>

<div class="container my-5">
    <div class="modern-card">
        <div class="modern-header">
            <div class="modern-header-line"></div>
            <h2 class="modern-title"><?= $lang === 'en' ? 'Contact & Feedback' : 'Aloqa va fikr-mulohaza' ?></h2>
        </div>

        <div class="modern-body">
            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] === 'success'): ?>
                    <div class="alert alert-success border-0 shadow-sm animatsiya1 mb-4">
                        <i class="fa-solid fa-circle-check me-2"></i> <?= $lang === 'en' ? 'Your message has been sent for moderation. Thank you!' : 'Xabaringiz moderatsiyaga yuborildi. Rahmat!' ?>
                    </div>
                <?php elseif ($_GET['status'] === 'error'): ?>
                    <div class="alert alert-danger border-0 shadow-sm animatsiya1 mb-4">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $lang === 'en' ? 'Something went wrong. Please try again.' : 'Xatolik yuz berdi. Qayta urinib ko\'ring.' ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="row g-4 justify-content-center mb-5">
                <div class="col-md-5">
                    <div class="contact-card-choice" data-bs-toggle="modal" data-bs-target="#form_1">
                        <div class="contact-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <h4 class="contact-title"><?= $lang === 'en' ? 'Leave a Message' : 'Xabar qoldiring' ?></h4>
                        <p class="contact-desc">
                            <?= $lang === 'en' ? 'Inquiries, collaborations, or general messages.' : 'Savollar, hamkorlik takliflari yoki umumiy xabarlar.' ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="contact-card-choice" data-bs-toggle="modal" data-bs-target="#form_2">
                        <div class="contact-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        </div>
                        <h4 class="contact-title"><?= $lang === 'en' ? 'Evaluate the Website' : 'Saytni baholang' ?></h4>
                        <p class="contact-desc">
                            <?= $lang === 'en' ? 'Help me improve the site with your valuable feedback.' : 'Qimmatli fikringiz bilan saytni yaxshilashga yordam bering.' ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="modern-text text-center px-lg-5">
                <p>
                    <?= $lang === 'en' 
                        ? 'Any message or comment you leave is truly valuable and greatly appreciated. This platform is designed to foster professional relationships and academic exchange.' 
                        : 'Siz qoldirgan har qanday xabar yoki sharh men uchun juda qimmatli va minnatdorlik bilan qabul qilinadi. Ushbu platforma professional munosabatlar va akademik almashinuvni rivojlantirish uchun mo\'ljallangan.' ?>
                </p>
            </div>
        </div>

        <div class="modern-footer">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="9" stroke="#aaa" stroke-width="1.5"/>
                <path d="M12 7v5l3 3" stroke="#aaa" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <?= $lang === 'en' ? 'Communication portal' : 'Aloqa portali' ?>
        </div>
    </div>
</div>

<!-- Modal 1: Leave a Message -->
<div class="modal fade" id="form_1" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modern-title" style="font-size: 1.4rem; color: var(--text-dark);"><?= $lang === 'en' ? 'Leave a Message' : 'Xabar qoldiring' ?></h4>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="filter: var(--invert-icon);"></button>
            </div>
            <div class="modal-body p-4">
                <form action="connection_check.php?u=<?= htmlspecialchars($_SESSION['current_portfolio_user'] ?? '') ?>" method="POST" enctype="multipart/form-data" class="row g-4">
                    <input type="hidden" name="msg_type" value="message">
                    <div class="col-md-6">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Name *' : 'Ism *' ?></label>
                        <input type="text" name="ism" required class="form-control form-control-modern" placeholder="<?= $lang === 'en' ? 'John' : 'Anvar' ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Surname' : 'Familiya' ?></label>
                        <input type="text" name="fam" class="form-control form-control-modern" placeholder="<?= $lang === 'en' ? 'Doe' : 'Aliyev' ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Relationship' : 'Bog\'liqlik' ?></label>
                        <select name="bogliq" class="form-select form-control-modern">
                            <option value="Collaborating Researcher"><?= $lang === 'en' ? 'Collaborating Researcher' : 'Hamkor tadqiqotchi' ?></option>
                            <option value="Mentee"><?= $lang === 'en' ? 'Mentee' : 'Shogird' ?></option>
                            <option value="Researcher"><?= $lang === 'en' ? 'Researcher' : 'Tadqiqotchi' ?></option>
                            <option value="Other"><?= $lang === 'en' ? 'Other' : 'Boshqa' ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-modern">Email</label>
                        <input type="email" name="email" class="form-control form-control-modern" placeholder="john@example.com">
                    </div>
                    <div class="col-12">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Social Media Profile *' : 'Ijtimoiy tarmoq profili *' ?></label>
                        <div class="input-group">
                            <select name="type" class="form-select form-control-modern" style="max-width: 120px; border-radius: 10px 0 0 10px;">
                                <option>Telegram</option>
                                <option>WhatsApp</option>
                                <option>Facebook</option>
                            </select>
                            <input type="url" name="url" required class="form-control form-control-modern" style="border-radius: 0 10px 10px 0;" placeholder="https://t.me/...">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Your Message *' : 'Xabaringiz *' ?></label>
                        <textarea name="xabar" required class="form-control form-control-modern" rows="4" placeholder="<?= $lang === 'en' ? 'How can I help you?' : 'Sizga qanday yordam bera olaman?' ?>"></textarea>
                    </div>
                    <div class="col-12 text-end pt-2">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal" style="border-radius: 10px; padding: 12px 25px;">
                            <?= $lang === 'en' ? 'Cancel' : 'Bekor qilish' ?>
                        </button>
                        <button type="submit" class="btn-submit-modern"><?= $lang === 'en' ? 'Send Message' : 'Xabarni yuborish' ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Evaluate Website -->
<div class="modal fade" id="form_2" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modern-title" style="font-size: 1.4rem; color: var(--text-dark);"><?= $lang === 'en' ? 'Evaluate the Website' : 'Saytni baholang' ?></h4>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" style="filter: var(--invert-icon);"></button>
            </div>
            <div class="modal-body p-4">
                <form action="connection_check.php?u=<?= htmlspecialchars($_SESSION['current_portfolio_user'] ?? '') ?>" method="POST" class="row g-4">
                    <input type="hidden" name="msg_type" value="rating">
                    <div class="col-md-6">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Name / Alias' : 'Ism / Taxallus' ?></label>
                        <input type="text" name="a" class="form-control form-control-modern" placeholder="<?= $lang === 'en' ? 'Your name' : 'Ismingiz' ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Contact Details' : 'Aloqa ma\'lumotlari' ?></label>
                        <input type="text" name="b" class="form-control form-control-modern" placeholder="<?= $lang === 'en' ? 'Phone, Email etc.' : 'Tel, Email va h.k.' ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Feedback & Suggestions *' : 'Fikr-mulohazalar va takliflar *' ?></label>
                        <textarea name="xabar" required class="form-control form-control-modern" rows="4" placeholder="<?= $lang === 'en' ? 'What should I improve...' : 'Nimani yaxshilashim kerak...' ?>"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Content Quality' : 'Mazmun sifati' ?></label>
                        <input type="range" name="q_content" class="form-range" min="1" max="5">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Design Aesthetics' : 'Dizayn estetikasi' ?></label>
                        <input type="range" name="q_design" class="form-range" min="1" max="5">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-modern"><?= $lang === 'en' ? 'Functionality' : 'Funksionallik' ?></label>
                        <input type="range" name="q_func" class="form-range" min="1" max="5">
                    </div>
                    <div class="col-12 text-end pt-2">
                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal" style="border-radius: 10px; padding: 12px 25px;">
                            <?= $lang === 'en' ? 'Cancel' : 'Bekor qilish' ?>
                        </button>
                        <button type="submit" class="btn-submit-modern" style="background: #0369a1;">
                            <?= $lang === 'en' ? 'Submit Review' : 'Bahoni yuborish' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
  include_once("footer.php");
?>
<script src="../js/bootstrap.bundle.js"></script>
</body>
</html>
