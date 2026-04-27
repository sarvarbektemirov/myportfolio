<?php
include_once('db.php');

if (empty($_SESSION['id']) || empty($_SESSION['ism'])) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list_publication.php");
    exit;
}

$uid = (int)($_SESSION['current_user_id'] ?? $_SESSION['id'] ?? 1);
$stmt = $link->prepare("SELECT * FROM publication WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    header("Location: list_publication.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nashrni tahrirlash</title>
    <link rel="icon" href="rasmlar/logo.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css">
    <script src="js/bootstrap.bundle.min.js"></script>
</head>
<body>
<?php include_once("menu.php"); ?>

<div class="container py-5 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h2 class="mb-0"><i class="fa-solid fa-book-open text-primary me-2"></i> Nashrni tahrirlash</h2>
        <a href="list_publication.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="form-card animatsiya1">
                <form action="update_publication.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="eski_fayl1" value="<?= htmlspecialchars($row['fayl1'] ?? '') ?>">
                    <input type="hidden" name="eski_fayl2" value="<?= htmlspecialchars($row['fayl2'] ?? '') ?>">
                    <input type="hidden" name="eski_fayl3" value="<?= htmlspecialchars($row['fayl3'] ?? '') ?>">

                    <!-- ASOSIY MA'LUMOTLAR -->
                    <div class="mb-4">
                        <label class="form-label fw-bold"><i class="fa-solid fa-heading text-primary"></i> Adabiyot (Maqola) nomi <span class="required-asterisk">*</span></label>
                        <input type="text" name="nom" class="form-control" required
                               value="<?= htmlspecialchars($row['nom']) ?>"
                               placeholder="Maqola nomini to'liq kiriting">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold"><i class="fa-solid fa-align-left text-success"></i> Annotatsiya <span class="required-asterisk">*</span></label>
                        <textarea class="form-control" rows="3" name="anatatsiya" required placeholder="Qisqacha mazmuni..."><?= htmlspecialchars($row['anatatsiya'] ?? '') ?></textarea>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-users text-info"></i> Mualliflar</label>
                            <input type="text" name="muallif" class="form-control"
                                   placeholder="Vergul bilan ajrating"
                                   value="<?= htmlspecialchars($row['muallif'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold"><i class="fa-solid fa-newspaper text-warning"></i> Jurnal / Konferensiya / Nashriyot</label>
                            <input type="text" name="jurnal" class="form-control"
                                   placeholder="Nashr manbasi nomi"
                                   value="<?= htmlspecialchars($row['jurnal'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- TEXNIK MA'LUMOTLAR -->
                    <div class="row g-4 mb-4 p-3 rounded-4 border">
                        <div class="col-6 col-md-2">
                            <label class="form-label fw-bold small text-muted">Nashr yili</label>
                            <input type="number" name="yil" class="form-control"
                                   value="<?= htmlspecialchars($row['yil'] ?? '') ?>">
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label fw-bold small text-muted">O'quv yili</label>
                            <input type="text" name="uyil" class="form-control"
                                   placeholder="2023-2024"
                                   value="<?= htmlspecialchars($row['uyil'] ?? '') ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-bold small text-muted">Sahifalar</label>
                            <input type="text" name="sahifa" class="form-control"
                                   placeholder="12-45"
                                   value="<?= htmlspecialchars($row['sahifa'] ?? '') ?>">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label fw-bold small text-muted">DOI</label>
                            <input type="text" name="doi" class="form-control"
                                   placeholder="10.xxxx/xxxxx"
                                   value="<?= htmlspecialchars($row['doi'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-md-2">
                            <label class="form-label fw-bold small text-muted">Tili</label>
                            <input type="text" name="til" class="form-control" list="tillar"
                                   value="<?= htmlspecialchars($row['til'] ?? '') ?>">
                            <datalist id="tillar">
                                <option value="o`zbek">o`zbek</option>
                                <option value="rus">rus</option>
                                <option value="ingliz">ingliz</option>
                            </datalist>
                        </div>
                    </div>

                    <!-- FAYLLAR -->
                    <div class="row g-4 mb-4 border-top pt-4">
                        <!-- fayl1 -->
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-file-pdf text-danger"></i> PDF fayl</label>
                            <?php if (!empty($row['fayl1'])): ?>
                                <div class="file-preview-box">
                                    <a href="../my_files/<?= urlencode($row['fayl1']) ?>" target="_blank">
                                        <i class="fa-solid fa-paperclip me-1"></i> <?= htmlspecialchars($row['fayl1']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="fayl1" class="form-control" accept=".pdf">
                            <small class="text-muted-small d-block mt-1">Eski faylni almashtirish uchun tanlang</small>
                        </div>
                        <!-- fayl2 -->
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-file-word text-primary"></i> Word fayl</label>
                            <?php if (!empty($row['fayl2'])): ?>
                                <div class="file-preview-box">
                                    <a href="../my_files/<?= urlencode($row['fayl2']) ?>" target="_blank">
                                        <i class="fa-solid fa-paperclip me-1"></i> <?= htmlspecialchars($row['fayl2']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="fayl2" class="form-control" accept=".doc,.docx">
                            <small class="text-muted-small d-block mt-1">Eski faylni almashtirish uchun tanlang</small>
                        </div>
                        <!-- fayl3 -->
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-language text-success"></i> Tarjima fayli</label>
                            <?php if (!empty($row['fayl3'])): ?>
                                <div class="file-preview-box">
                                    <a href="../my_files/<?= urlencode($row['fayl3']) ?>" target="_blank">
                                        <i class="fa-solid fa-paperclip me-1"></i> <?= htmlspecialchars($row['fayl3']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="fayl3" class="form-control" accept=".doc,.docx">
                            <small class="text-muted-small d-block mt-1">Eski faylni almashtirish uchun tanlang</small>
                        </div>
                    </div>

                    <!-- QO'SHIMCHA -->
                    <div class="row g-4 mb-4 border-top pt-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-database text-secondary"></i> Indekslagan baza</label>
                            <input type="text" name="baza" class="form-control" list="bazalar"
                                   placeholder="Scopus, WoS..."
                                   value="<?= htmlspecialchars($row['baza'] ?? '') ?>">
                            <datalist id="bazalar">
                                <option value="Scopus"></option>
                                <option value="WoS"></option>
                                <option value="Google scholar"></option>
                            </datalist>
                        </div>
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-bold"><i class="fa-solid fa-tags text-warning"></i> Adabiyot turi <span class="required-asterisk">*</span></label>
                            <select name="tur" class="form-select">
                                <?php
                                $turlar = [
                                    ' Xalqaro jurnaldagi maqola       ',
                                    ' Respublika jurnalidagi maqola   ',
                                    ' Xalqaro konferensiya            ',
                                    ' Respublika konferensiya         ',
                                    ' Monografiya                     ',
                                    ' Book chapter                    ',
                                    ' Kitob                           ',
                                    ' Boshqa                          ',
                                ];
                                foreach ($turlar as $t): ?>
                                    <option <?= trim($row['tur']) === trim($t) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(trim($t)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mb-4 border-top pt-4">
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-bold"><i class="fa-solid fa-link text-primary"></i> Cite (Iqtibos)</label>
                            <input type="text" name="cite" class="form-control"
                                   placeholder="Full citation text"
                                   value="<?= htmlspecialchars($row['cite'] ?? '') ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold"><i class="fa-solid fa-spell-check text-dark"></i> Cite formati</label>
                            <input type="text" name="cite_f" class="form-control" list="cites"
                                   placeholder="APA, MLA..."
                                   value="<?= htmlspecialchars($row['cite_f'] ?? '') ?>">
                            <datalist id="cites">
                                <option value="APA"></option>
                                <option value="MLA"></option>
                                <option value="Chicago"></option>
                                <option value="Harvard"></option>
                            </datalist>
                        </div>
                    </div>

                    <div class="text-center mt-5 pt-3 border-top">
                        <button type="submit" class="btn btn-submit px-5 shadow">
                            <i class="fa-solid fa-floppy-disk me-2"></i> O'zgarishlarni saqlash
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Dinamik dizayn elementlari yoki validatsiya kerak bo'lsa shu yerga
</script>
</body>
</html>

