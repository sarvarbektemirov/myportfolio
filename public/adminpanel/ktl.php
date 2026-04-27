<?php
function kirillToLotin($text)
{
    $kirill = [
        'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
        'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
        'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Э', 'Ю', 'Я',
        'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
        'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
        'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'э', 'ю', 'я',
        'Ў', 'ў', 'Қ', 'қ', 'Ғ', 'ғ', 'Ҳ', 'ҳ'
    ];
    $latin = [
        'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'J', 'Z', 'I', 'Y',
        'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
        'X', 'Ch', 'Sh', 'Shch', '\'', 'Y', 'E', 'Yu', 'Ya',
        'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'j', 'z', 'i', 'y',
        'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
        'x', 'ch', 'sh', 'shch', '\'', 'y', 'e', 'yu', 'ya',
        'O\'', 'o\'', 'Q', 'q', 'G\'', 'g\'', 'H', 'h'
    ];

    // Avval kirill -> lotin
    $result = str_replace($kirill, $latin, $text);

    // Hammasi kichik harf
    $result = mb_strtolower($result, 'UTF-8');

    // Faqat bo'sh joydan keyingi harfni katta qil
    $words = explode(' ', $result);
    $words = array_map(function($word) {
        return mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') 
               . mb_substr($word, 1, null, 'UTF-8');
    }, $words);

    return implode(' ', $words);
}

