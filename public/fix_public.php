<?php
// Fix public files
$dir = "c:/OSPanel/home/kursishi/public/en/";
$files = ['education.php', 'experience.php', 'footer.php', 'home.php', 'publications.php', 'sarlavha.php', 'students.php'];

foreach ($files as $f) {
    if (file_exists($dir.$f)) {
        $content = file_get_contents($dir.$f);
        
        // Match FROM table ORDER BY
        $content = preg_replace('/FROM ([a-zA-Z0-9_]+) ORDER BY/', 'FROM $1 WHERE user_id = $portfolio_user_id ORDER BY', $content);
        
        // Match FROM table WHERE ... ORDER BY
        $content = preg_replace('/FROM ([a-zA-Z0-9_]+) WHERE (.*?) ORDER BY/', 'FROM $1 WHERE user_id = $portfolio_user_id AND ($2) ORDER BY', $content);
        
        file_put_contents($dir.$f, $content);
    }
}
echo "Public files updated.";
?>
