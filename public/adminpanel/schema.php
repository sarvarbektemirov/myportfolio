<?php
/**
 * Master Schema for Multi-Database SaaS
 * This file contains the queries to initialize a new user database.
 */

function initialize_user_db($link) {
    $queries = [
        "CREATE TABLE IF NOT EXISTS home (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ism_uz VARCHAR(100), ism_en VARCHAR(100),
            familiya_uz VARCHAR(100), familiya_en VARCHAR(100),
            mutaxassislik_uz VARCHAR(255), mutaxassislik_en VARCHAR(255),
            malumot_uz TEXT, malumot_en TEXT,
            bio_uz TEXT, bio_en TEXT,
            skills_uz TEXT, skills_en TEXT,
            rasm VARCHAR(255), favicon VARCHAR(255),
            user_id INT DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS talim (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bosqich VARCHAR(100),
            bosqich_en VARCHAR(100),
            tavsif_uz TEXT, tavsif_en TEXT,
            rasm VARCHAR(255),
            user_id INT DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS nashrlar (
            id INT AUTO_INCREMENT PRIMARY KEY,
            lavozim_uz VARCHAR(255), lavozim_en VARCHAR(255),
            ish_joyi_uz VARCHAR(255), ish_joyi_en VARCHAR(255),
            faoliyat_uz TEXT, faoliyat_en TEXT,
            boshlanish DATE, tugash DATE,
            hozirgi TINYINT(1) DEFAULT 0,
            tur ENUM('asosiy', 'qoshimcha') DEFAULT 'asosiy',
            user_id INT DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS publication (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(255),
            anatatsiya TEXT,
            muallif VARCHAR(255),
            jurnal VARCHAR(255),
            yil INT,
            uyil VARCHAR(50),
            sahifa VARCHAR(50),
            doi VARCHAR(255),
            til VARCHAR(50),
            baza VARCHAR(100),
            tur VARCHAR(100),
            cite TEXT,
            cite_f VARCHAR(50),
            fayl1 VARCHAR(255),
            fayl2 VARCHAR(255),
            fayl3 VARCHAR(255),
            user_id INT DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS teaching (
            id INT AUTO_INCREMENT PRIMARY KEY,
            fan_uz VARCHAR(255), fan_en VARCHAR(255),
            tavsif_uz TEXT, tavsif_en TEXT,
            tur ENUM('asosiy', 'qoshimcha') DEFAULT 'asosiy',
            user_id INT DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ism VARCHAR(255),
            rasm VARCHAR(255),
            toifa ENUM('toifa_1', 'toifa_2', 'toifa_3') DEFAULT 'toifa_1',
            qisqa_malumot_uz TEXT,
            qisqa_malumot_en TEXT,
            tolik_malumot_uz TEXT,
            tolik_malumot_en TEXT,
            user_id INT DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT 1,
            msg_type ENUM('message', 'rating') DEFAULT 'message',
            name VARCHAR(100),
            email VARCHAR(100),
            relationship VARCHAR(100),
            message_uz TEXT,
            message_en TEXT,
            status VARCHAR(20) DEFAULT 'pending',
            r_content TINYINT, 
            r_design TINYINT, 
            r_func TINYINT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS footer (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bio_uz TEXT, bio_en TEXT,
            status_uz VARCHAR(255), status_en VARCHAR(255),
            copyright_uz VARCHAR(255), copyright_en VARCHAR(255),
            orcid VARCHAR(255),
            cv_fayl VARCHAR(255),
            site_launch_date DATE,
            tg_link VARCHAR(255),
            wa_link VARCHAR(255),
            scopus_link VARCHAR(255),
            scholar_link VARCHAR(255),
            university_link VARCHAR(255),
            user_id INT DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
        
        "CREATE TABLE IF NOT EXISTS nashr_carousel (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom_uz VARCHAR(255), nom_en VARCHAR(255),
            rasm VARCHAR(255),
            sana DATE,
            tur ENUM('asosiy', 'qoshimcha') DEFAULT 'asosiy',
            user_id INT DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "CREATE TABLE IF NOT EXISTS header (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ism VARCHAR(255), ism_en VARCHAR(255),
            familiya VARCHAR(255), familiya_en VARCHAR(255),
            daraja VARCHAR(255), daraja_en VARCHAR(255),
            tel VARCHAR(50), email VARCHAR(100),
            user_id INT DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ];

    foreach ($queries as $sql) {
        $link->query($sql);
    }
}

