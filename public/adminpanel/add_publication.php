<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mening tadqiqotlarim</title>
    <link rel="icon" href="rasmlar/logo.png">
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/extra.css" type="text/css">
    <script src="js/bootstrap.bundle.min.js"></script>

</head>
<body>
    <?php
        include "menu.php";
    ?>
    <div class="container py-5 mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4 page-header">
            <h2 class="mb-0"><i class="fa-solid fa-book-journal-whills text-primary me-2"></i> Shaxsiy adabiyotlarni bazaga qo'shish</h2>
            <a href="list_publication.php" class="btn btn-secondary btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Ro'yxatga qaytish</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">
                <div class="form-card animatsiya1">
                    <form action="publication_check.php" method="post" enctype="multipart/form-data">
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <label for="nom" class="form-label"><i class="fa-solid fa-heading text-primary"></i> Adabiyot (Maqola) nomi <span class="required-asterisk">*</span></label>
                                <input type="text" name="nom" class="form-control" id="nom" placeholder="Maqola nomini kiriting" title="Namuna: Effective methods of data preprocessing / Ma'lumotlarni oldindan qayta ishlashning samarali usullari" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="comment" class="form-label"><i class="fa-solid fa-align-left text-info"></i> Annotatsiya: <span class="required-asterisk">*</span></label>
                                <textarea class="form-control" rows="2" id="comment" name="anatatsiya" placeholder="Maqola mazmunini ifodalovchi kalit so'zlar va iboralarni vergul bilan ajratgan holda kiriting" required></textarea>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-12 col-lg-6">
                                <label for="muallif" class="form-label"><i class="fa-solid fa-users text-success"></i> Mualliflar:</label>
                                <input type="text" name="muallif" class="form-control" id="muallif" placeholder="Mualliflarni vergul bilan ajratgan holda kiriting">
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="jurnal" class="form-label"><i class="fa-solid fa-building-columns text-warning"></i> Jurnal/Nashriyot nomi:</label>
                                <input type="text" name="jurnal" class="form-control" id="jurnal" placeholder="Jurnal yoki nashriyot nomi" title="Namuna: SamDU ilmiy axborotnomasi, № 3 (32) yoki Ijod nashr, Toshkent">
                            </div>                
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label for="yil" class="form-label"><i class="fa-solid fa-calendar text-danger"></i> Nashr yili:</label>
                                <input type="number" name="yil" class="form-control" id="yil" placeholder="Yil" title="Namuna: 2025">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label for="uyil" class="form-label"><i class="fa-solid fa-calendar-days text-secondary"></i> O'quv yili:</label>
                                <input type="text" name="uyil" class="form-control" id="uyil" placeholder="Yil oralig'i" title="Namuna: 2024-2025">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="sahifa" class="form-label"><i class="fa-solid fa-file-lines text-info"></i> Sahifalar:</label>
                                <input type="text" name="sahifa" class="form-control" id="sahifa" placeholder="Sahifalar" title="Namuna: 1) Jurnal va konferensiya uchun: 47-59; 2) Kitob uchun: 263">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="doi" class="form-label"><i class="fa-solid fa-link text-primary"></i> DOI:</label>
                                <input type="text" name="doi" class="form-control" id="doi" placeholder="DOI ni kiriting">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label for="til" class="form-label"><i class="fa-solid fa-language text-success"></i> Nashr tili:</label>
                                <input type="text" name="til" class="form-control" list="tillar" id="til" placeholder="Tanlang">
                                <datalist id="tillar">
                                    <option value="o`zbek">o`zbek</option>
                                    <option value="rus">rus</option>
                                    <option value="ingliz">ingliz</option>
                                </datalist>
                            </div>                
                        </div>
                        
                        <div class="row g-4 mb-4 border-top pt-3 mt-1">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label for="fayl1" class="form-label"><i class="fa-solid fa-file-pdf text-danger"></i> Adabiyot PDF fayli: <span class="required-asterisk">*</span></label>
                                <input type="file" name="fayl1" class="form-control" id="fayl1" accept=".pdf, .doc, .docx" required>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label for="fayl2" class="form-label"><i class="fa-solid fa-file-word text-info"></i> Adabiyot Word fayli:</label>
                                <input type="file" name="fayl2" class="form-control" id="fayl2" accept=".doc, .docx">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label for="fayl3" class="form-label"><i class="fa-solid fa-language text-success"></i> Tarjima fayli:</label>
                                <input type="file" name="fayl3" class="form-control" id="fayl3" accept=".doc, .docx">
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-12 col-sm-6 col-lg-6">
                                <label for="baza" class="form-label"><i class="fa-solid fa-database text-warning"></i> Indekslagan baza nomi:</label>
                                <input type="text" name="baza" class="form-control" list="bazalar" id="baza" placeholder="Baza nomini kiriting">
                                <datalist id="bazalar">
                                    <option value="Scopus"></option>
                                    <option value="WoS"></option>
                                    <option value="Google scholar"></option>
                                    <option value="Google Scholar"></option>
                                </datalist>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-6">
                                <label for="tur" class="form-label"><i class="fa-solid fa-layer-group text-primary"></i> Adabiyot turi:</label>
                                <select name="tur" class="form-select" id="tur">
                                    <option> Xalqaro jurnaldagi maqola       </option>
                                    <option> Respublika jurnalidagi maqola   </option>
                                    <option> Xalqaro konferensiya            </option>
                                    <option> Respublika konferensiya         </option>
                                    <option> Monografiya                     </option>
                                    <option> Book chapter                    </option>
                                    <option> Kitob                           </option>
                                    <option> Boshqa                          </option>
                                </select>
                            </div>                 
                        </div>

                        <div class="row g-4 mb-5 pb-2">
                            <div class="col-12 col-lg-8">
                                <label for="cite" class="form-label"><i class="fa-solid fa-quote-right text-info"></i> Cite:</label>
                                <input type="text" name="cite" class="form-control" id="cite" placeholder="Maqolaga ssilka berish uchun ma'lumot">
                            </div>
                            <div class="col-12 col-lg-4">
                                <label for="cite_f" class="form-label"><i class="fa-solid fa-list-ol text-secondary"></i> Cite formati:</label>
                                <input type="text" name="cite_f" class="form-control" id="cite_f" list="cites" placeholder="Ssilka formatini kiriting">
                                <datalist id="cites">
                                    <option value="APA"></option>
                                    <option value="MLA"></option>
                                    <option value="Chicago"></option>
                                    <option value="Harvard"></option>
                                </datalist>
                            </div>
                        </div>                
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-submit" >
                                <i class="fa-solid fa-cloud-arrow-up me-2"></i> Bazaga qo'shish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

