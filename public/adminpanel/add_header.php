<?php
session_start();
include_once("db.php");
$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id']);
$check_header = $link->query("SELECT id FROM header WHERE user_id = $uid LIMIT 1");
if ($check_header && $check_header->num_rows > 0) {
    header("Location: edit_header.php");
    exit;
}
include_once("menu.php");
?>
<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qo'shish</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
</head>

<body>

    <div class="container py-5 mt-3">
        <h2 class="page-header"><i class="fa-solid fa-heading text-primary me-2"></i> Sarlavha ma'lumotlarini qo'shish</h2>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="form-card animatsiya1">
                    <form action="header_check.php" method="post">
                        <div class="row g-4 mb-4">
                            <!-- O'zbekcha Ma'lumotlar -->
                            <div class="col-12"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">O'zbekcha Ma'lumotlar</h5></div>
                            
                            <div class="col-12 col-md-4">
                                <label for="a" class="form-label"><i class="fa-regular fa-id-badge text-primary"></i> Familiya (UZ) <span class="required-asterisk">*</span></label>
                                <input type="text" name="a" id="a" class="form-control" required oninput="faqatHarf(this)">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="b" class="form-label"><i class="fa-regular fa-user text-primary"></i> Ism (UZ) <span class="required-asterisk">*</span></label>
                                <input type="text" name="b" id="b" class="form-control" required oninput="faqatHarf(this)">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="c" class="form-label"><i class="fa-solid fa-graduation-cap text-primary"></i> Ilmiy Daraja (UZ) <span class="required-asterisk">*</span></label>
                                <input type="text" name="c" id="c" class="form-control" required placeholder="Masalan: Professor">
                            </div>

                            <!-- English Information -->
                            <div class="col-12 mt-5"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">English Information</h5></div>
                            
                            <div class="col-12 col-md-4">
                                <label for="a_en" class="form-label"><i class="fa-regular fa-id-badge text-secondary"></i> Surname (EN)</label>
                                <input type="text" name="a_en" id="a_en" class="form-control" oninput="faqatHarf(this)" placeholder="Optional">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="b_en" class="form-label"><i class="fa-regular fa-user text-secondary"></i> Name (EN)</label>
                                <input type="text" name="b_en" id="b_en" class="form-control" oninput="faqatHarf(this)" placeholder="Optional">
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="c_en" class="form-label"><i class="fa-solid fa-graduation-cap text-secondary"></i> Degree (EN)</label>
                                <input type="text" name="c_en" id="c_en" class="form-control" placeholder="e.g. Professor">
                            </div>

                            <!-- Contact Info -->
                            <div class="col-12 mt-5"><h5 class="text-muted small fw-bold text-uppercase border-bottom pb-2">Aloqa Ma'lumotlari</h5></div>

                            <div class="col-12 col-md-6">
                                <label for="d" class="form-label"><i class="fa-solid fa-phone text-success"></i> Telefon Raqam <span class="required-asterisk">*</span></label>
                                <input type="text" name="d" id="d" class="form-control" placeholder="+998901234567" maxlength="13" required oninput="faqatTelefon(this)">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="e" class="form-label"><i class="fa-regular fa-envelope text-danger"></i> Email <span class="required-asterisk">*</span></label>
                                <input type="email" name="e" id="e" class="form-control" required placeholder="example@mail.com">
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-submit px-5 shadow-sm">
                                <i class="fa-solid fa-check-circle me-2"></i> Ma'lumotlarni Saqlash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.js"></script>
    <script>
        // Faqat harf va ' belgisi (o' va g' uchun), raqam va boshqa belgilar kiritilmaydi
        function faqatHarf(input) {
            input.value = input.value.replace(/[^a-zA-ZʻʼА-Яа-яЎўҒғ'\s]/g, "");
        }

        // Faqat + (faqat boshida) va raqamlar, max 13 belgi
        function faqatTelefon(input) {
            let val = input.value;
            val = val.replace(/[^\d+]/g, ""); // + va raqamdan boshqasini o'chir
            val = val.replace(/(?!^)\+/g, ""); // + faqat boshida bo'lsin
            if (val.length > 13) val = val.slice(0, 13);
            input.value = val;
        }

        // Yuborishda tekshiruv
        document.querySelector("form").addEventListener("submit", function(e) {
            let valid = true;

            // Barcha inputlar bo'sh emasligi
            this.querySelectorAll("input").forEach(function(input) {
                input.classList.remove("is-invalid");
                if (input.value.trim() === "") {
                    input.classList.add("is-invalid");
                    valid = false;
                }
            });

            // Telefon: aynan 13 belgi (+998XXXXXXXXX)
            const telefon = document.getElementById("d");
            if (telefon.value.trim() !== "" && telefon.value.trim().length !== 13) {
                telefon.classList.add("is-invalid");
                valid = false;
            }

            // Email: @ belgisi bor-yo'qligi
            const email = document.getElementById("e");
            if (email.value.trim() !== "" && !email.value.includes("@")) {
                email.classList.add("is-invalid");
                valid = false;
            }

            if (!valid) e.preventDefault();
        });

        // Yozayotganda qizilni olib tashlash
        document.querySelectorAll("input").forEach(function(input) {
            input.addEventListener("input", function() {
                this.classList.remove("is-invalid");
            });
        });
    </script>

</body>

</html>

