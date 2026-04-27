<?php
/**
 * Auto-migration script to ensure 'publication' table columns exist.
 * This script is safe to include in init.php.
 */
if (isset($link) && $link instanceof mysqli) {
    // Check if 'baza' column exists in 'publication' table
    $check_col = $link->query("SHOW COLUMNS FROM publication LIKE 'baza'");
    
    if ($check_col && $check_col->num_rows == 0) {
        // Table needs migration
        $existing_cols = [];
        $cols_res = $link->query("SHOW COLUMNS FROM publication");
        if ($cols_res) {
            while ($col = $cols_res->fetch_assoc()) {
                $existing_cols[] = $col['Field'];
            }
            
            $alter_queries = [];
            
            // Renames
            if (!in_array('nom', $existing_cols) && in_array('title', $existing_cols)) {
                $alter_queries[] = "CHANGE COLUMN title nom VARCHAR(255)";
                $existing_cols[] = 'nom'; 
            }
            if (!in_array('muallif', $existing_cols) && in_array('authors', $existing_cols)) {
                $alter_queries[] = "CHANGE COLUMN authors muallif VARCHAR(255)";
                $existing_cols[] = 'muallif';
            }
            if (!in_array('jurnal', $existing_cols) && in_array('journal', $existing_cols)) {
                $alter_queries[] = "CHANGE COLUMN journal jurnal VARCHAR(255)";
                $existing_cols[] = 'jurnal';
            }
            if (!in_array('doi', $existing_cols) && in_array('link', $existing_cols)) {
                $alter_queries[] = "CHANGE COLUMN link doi VARCHAR(255)";
                $existing_cols[] = 'doi';
            }

            // Columns to add
            $columns_to_add = [
                'nom' => "VARCHAR(255) AFTER id",
                'anatatsiya' => "TEXT AFTER nom",
                'muallif' => "VARCHAR(255) AFTER anatatsiya",
                'jurnal' => "VARCHAR(255) AFTER muallif",
                'uyil' => "VARCHAR(50) AFTER yil",
                'sahifa' => "VARCHAR(50) AFTER uyil",
                'doi' => "VARCHAR(255) AFTER sahifa",
                'til' => "VARCHAR(50) AFTER doi",
                'baza' => "VARCHAR(100) AFTER til",
                'cite' => "TEXT AFTER tur",
                'cite_f' => "VARCHAR(50) AFTER cite",
                'fayl1' => "VARCHAR(255) AFTER cite_f",
                'fayl2' => "VARCHAR(255) AFTER fayl1",
                'fayl3' => "VARCHAR(255) AFTER fayl2"
            ];

            foreach ($columns_to_add as $col_name => $col_def) {
                if (!in_array($col_name, $existing_cols)) {
                    $alter_queries[] = "ADD COLUMN $col_name $col_def";
                }
            }
            
            if (!empty($alter_queries)) {
                $sql = "ALTER TABLE publication " . implode(", ", $alter_queries);
                $link->query($sql);
            }
        }
    }

    // --- Ta'lim Table Migration ---
    $check_talim = $link->query("SHOW COLUMNS FROM talim LIKE 'bosqich_en'");
    if ($check_talim && $check_talim->num_rows == 0) {
        $link->query("ALTER TABLE talim ADD COLUMN bosqich_en VARCHAR(100) AFTER bosqich");
    }

    // --- Students Table Migration ---
    $check_students = $link->query("SHOW COLUMNS FROM students LIKE 'qisqa_malumot_uz'");
    if ($check_students && $check_students->num_rows == 0) {
        $link->query("ALTER TABLE students 
            ADD COLUMN qisqa_malumot_uz TEXT AFTER toifa,
            ADD COLUMN qisqa_malumot_en TEXT AFTER qisqa_malumot_uz,
            ADD COLUMN tolik_malumot_uz TEXT AFTER qisqa_malumot_en,
            ADD COLUMN tolik_malumot_en TEXT AFTER tolik_malumot_uz
        ");
    }
    // --- Home Table Skills Migration ---
    $check_home_skills = $link->query("SHOW COLUMNS FROM home LIKE 'skills_uz'");
    if ($check_home_skills && $check_home_skills->num_rows == 0) {
        $link->query("ALTER TABLE home 
            ADD COLUMN skills_uz TEXT AFTER bio_en,
            ADD COLUMN skills_en TEXT AFTER skills_uz
        ");
    }
}
?>
