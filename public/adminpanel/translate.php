<?php
function translateText($text, $from, $to) {
    if (empty(trim($text))) return '';

    // 400 belgidan qisqa bo'lsa — to'g'ridan tarjima
    if (mb_strlen($text) <= 400) {
        return translateChunk($text, $from, $to);
    }

    // Uzun matnni gaplarga bo'lamiz
    $gaplar = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

    $natija = '';
    $blok   = '';

    foreach ($gaplar as $gap) {
        // Blok 400 belgidan oshsa — tarjima qilib, natijaga qo'shamiz
        if (mb_strlen($blok . ' ' . $gap) > 400) {
            if (!empty($blok)) {
                $natija .= translateChunk(trim($blok), $from, $to) . ' ';
                $blok = $gap;
            } else {
                // Bitta gap o'zi 400 dan uzun bo'lsa — bo'lib yuboramiz
                $natija .= translateChunk(mb_substr($gap, 0, 400), $from, $to) . ' ';
                $blok = mb_substr($gap, 400);
            }
        } else {
            $blok .= ' ' . $gap;
        }
    }

    // Qolgan blokni ham tarjima qilamiz
    if (!empty(trim($blok))) {
        $natija .= translateChunk(trim($blok), $from, $to);
    }

    return trim($natija);
}

// Bitta blokni tarjima qiluvchi yordamchi funksiya
function translateChunk($text, $from, $to) {
    if (empty(trim($text))) return '';

    $url = "https://api.mymemory.translated.net/get?q="
         . urlencode($text)
         . "&langpair={$from}|{$to}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($data && $data['responseStatus'] == 200) {
        $tarjima = $data['responseData']['translatedText'];
        // HTML entitylarni tozalaymiz: &quot; &#10; &#039; va boshqalar
        $tarjima = html_entity_decode($tarjima, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return $tarjima;
    }

    return $text; // xatolik bo'lsa originalini qaytaradi
}
