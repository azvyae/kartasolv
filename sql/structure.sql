-- MySQL dump 10.13  Distrib 8.0.32, for Linux (x86_64)
--
-- Host: kartasarijadi.com    Database: kartasar_main
-- ------------------------------------------------------
-- Server version	5.5.5-10.3.37-MariaDB-log-cll-lve
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;

/*!50503 SET NAMES utf8mb4 */
;

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */
;

/*!40103 SET TIME_ZONE='+00:00' */
;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */
;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */
;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */
;

/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */
;

--
-- Table structure for table `communities`
--
DROP TABLE IF EXISTS `communities`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `communities` (
    `community_id` int(11) NOT NULL AUTO_INCREMENT,
    `community_name` varchar(128) NOT NULL,
    `community_address` varchar(256) NOT NULL,
    `community_identifier` varchar(256) DEFAULT NULL,
    `pmpsks_type` int(11) NOT NULL,
    `community_status` enum('Disetujui', 'Belum Disetujui') NOT NULL DEFAULT 'Belum Disetujui',
    `created_by` int(11) DEFAULT NULL,
    `modified_by` int(11) DEFAULT NULL,
    `deleted_by` int(11) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`community_id`) USING BTREE,
    UNIQUE KEY `community_identifier` (`community_identifier`) USING BTREE,
    KEY `FK_pmks_pmpsks_types` (`pmpsks_type`),
    KEY `FK_pmks_users` (`created_by`),
    KEY `FK_pmks_users_2` (`modified_by`),
    KEY `FK_pmks_users_3` (`deleted_by`),
    CONSTRAINT `FK_pmks_pmpsks_types` FOREIGN KEY (`pmpsks_type`) REFERENCES `pmpsks_types` (`pmpsks_id`) ON
    UPDATE
        CASCADE,
        CONSTRAINT `FK_pmks_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE,
        CONSTRAINT `FK_pmks_users_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE,
        CONSTRAINT `FK_pmks_users_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1448 DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `communities`
--
LOCK TABLES `communities` WRITE;

/*!40000 ALTER TABLE `communities` DISABLE KEYS */
;

/*!40000 ALTER TABLE `communities` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `db_factories`
--
DROP TABLE IF EXISTS `db_factories`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `db_factories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(31) NOT NULL,
    `uid` varchar(31) NOT NULL,
    `class` varchar(63) NOT NULL,
    `icon` varchar(31) NOT NULL,
    `summary` varchar(255) NOT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `name` (`name`),
    KEY `uid` (`uid`),
    KEY `deleted_at_id` (`deleted_at`, `id`),
    KEY `created_at` (`created_at`)
) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `db_factories`
--
LOCK TABLES `db_factories` WRITE;

/*!40000 ALTER TABLE `db_factories` DISABLE KEYS */
;

INSERT INTO
    `db_factories`
VALUES
    (
        1,
        'Test Factory',
        'test001',
        'Factories\\Tests\\NewFactory',
        'fas fa-puzzle-piece',
        'Longer sample text for testing',
        NULL,
        '2022-08-05 19:40:46',
        '2022-08-05 19:40:46'
    ),
    (
        2,
        'Widget Factory',
        'widget',
        'Factories\\Tests\\WidgetPlant',
        'fas fa-puzzle-piece',
        'Create widgets in your factory',
        NULL,
        NULL,
        NULL
    ),
    (
        3,
        'Evil Factory',
        'evil-maker',
        'Factories\\Evil\\MyFactory',
        'fas fa-book-dead',
        'Abandon all hope, ye who enter here',
        NULL,
        NULL,
        NULL
    );

/*!40000 ALTER TABLE `db_factories` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `db_migrations`
--
DROP TABLE IF EXISTS `db_migrations`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `db_migrations` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `version` varchar(255) NOT NULL,
    `class` varchar(255) NOT NULL,
    `group` varchar(255) NOT NULL,
    `namespace` varchar(255) NOT NULL,
    `time` int(11) NOT NULL,
    `batch` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB AUTO_INCREMENT = 15 DEFAULT CHARSET = utf8 COLLATE = utf8_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `db_migrations`
--
LOCK TABLES `db_migrations` WRITE;

/*!40000 ALTER TABLE `db_migrations` DISABLE KEYS */
;

INSERT INTO
    `db_migrations`
VALUES
    (
        14,
        '2020-02-22-222222',
        'Tests\\Support\\Database\\Migrations\\ExampleMigration',
        'tests',
        'Tests\\Support',
        1659746446,
        1
    );

/*!40000 ALTER TABLE `db_migrations` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `info_activities`
--
DROP TABLE IF EXISTS `info_activities`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `info_activities` (
    `id` int(11) NOT NULL DEFAULT 1,
    `title_a` varchar(64) NOT NULL DEFAULT '',
    `desc_a` varchar(512) NOT NULL DEFAULT '0',
    `image_a` varchar(256) NOT NULL DEFAULT '',
    `title_b` varchar(64) NOT NULL DEFAULT '',
    `desc_b` varchar(512) NOT NULL DEFAULT '',
    `image_b` varchar(256) NOT NULL DEFAULT '',
    `title_c` varchar(64) NOT NULL DEFAULT '',
    `desc_c` varchar(512) NOT NULL DEFAULT '',
    `image_c` varchar(256) NOT NULL DEFAULT '',
    `modified_by` int(11) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE,
    KEY `FK_history_info_users` (`modified_by`) USING BTREE,
    CONSTRAINT `FK_info_activities_users` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `info_activities`
--
LOCK TABLES `info_activities` WRITE;

/*!40000 ALTER TABLE `info_activities` DISABLE KEYS */
;

INSERT INTO
    `info_activities`
VALUES
    (
        1,
        'MVP FSO',
        'MVP FSO (Festival Seni dan Olahraga) di Kelurahan Sarijadi merupakan kegiatan kedua dari Porseni Kelurahan Sarijadi.',
        'https://kartasarijadi.com/uploads/activities/1660654386_a5977cb43fa1652bc87e.webp',
        'Latihan Dasar Kepemipinan Pemuda',
        'Latihan Dasar Kepemimpinan ini dilakukan untuk memberi wawasan kepada Karang Taruna Unit dalam hal Kekarang Tarunaan dan melatih kekompaklan.',
        'https://kartasarijadi.com/uploads/activities/1660653282_2daf03c3a4127d2b36c9.webp',
        'Focus Group Discussion',
        'Focus group discussion yang lebih terkenal dengan singkatannya FGD merupakan salah satu metode riset kualitatif yang paling terkenal selain teknik wawancara. FGD adalah diskusi terfokus dari suatu group untuk membahas suatu masalah tertentu, dalam suasana informal dan santai. Jumlah pesertanya bervariasi antara 8-12 orang, dilaksanakan dengan panduan seorang moderator.',
        'https://kartasarijadi.com/uploads/activities/1660391634_a981c478ede18dcb6e52.webp',
        1,
        NULL,
        '2022-08-16 19:53:06'
    );

/*!40000 ALTER TABLE `info_activities` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `info_history`
--
DROP TABLE IF EXISTS `info_history`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `info_history` (
    `id` int(11) NOT NULL DEFAULT 1,
    `title_a` varchar(64) NOT NULL DEFAULT '',
    `desc_a` varchar(512) NOT NULL DEFAULT '',
    `title_b` varchar(64) NOT NULL DEFAULT '',
    `desc_b` varchar(512) NOT NULL DEFAULT '',
    `title_c` varchar(64) NOT NULL DEFAULT '',
    `desc_c` varchar(512) NOT NULL DEFAULT '',
    `title_d` varchar(64) NOT NULL DEFAULT '',
    `desc_d` varchar(512) NOT NULL DEFAULT '',
    `image_a` varchar(256) NOT NULL DEFAULT '',
    `image_b` varchar(256) NOT NULL DEFAULT '',
    `modified_by` int(11) DEFAULT 0,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `FK_history_info_users` (`modified_by`),
    CONSTRAINT `FK_history_info_users` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `info_history`
--
LOCK TABLES `info_history` WRITE;

/*!40000 ALTER TABLE `info_history` DISABLE KEYS */
;

INSERT INTO
    `info_history`
VALUES
    (
        1,
        'Dari Kami Untuk Kelurahan Sarijadi',
        'Karang Taruna Ngajomantara terdiri dari pemuda dan pemudi di Sarijadi, dan akan membangun Sarijadi.',
        '26 September 1960',
        'Karang Taruna untuk pertama kalinya lahir pada tanggal 26 September 1960 di Kampung Melayu, Jakarta. Dalam perjalanan sejarahnya, Karang Taruna telah melakukan berbagai kegiatan, sebagai upaya untuk turut menanggulangi masalah-masalah Kesejahteraan Sosial terutama yang dihadapi generasi muda dilingkungannya, sesuai dengan kondisi daerah dan tingkat kemampuan masing-masing.',
        'Kelurahan Sarijadi',
        'Sarijadi merupakan salah satu Kelurahan di Kota Bandung, yang kini, telah berdiri pusat-pusat bisnis dan banyaknya pemuda yang aktif. Hal ini mendorong dibutuhkannya wadah kreativitas untuk mendukung kegiatan-kegiatan tersebut.',
        'Karang Taruna Ngajomantara',
        'Adapun Karang Taruna Kelurahan Sarijadi, pada jejaknya menjalankan berbagai macam program kerja untuk diwujudkannya kesejahteraan sosial, dalam berbagai aspek. Kami Karang Taruna Ngajomantara yang terdiri dari pemuda dan pemudi di Sarijadi akan membantu mewujudkan hal tersebut.',
        'https://kartasarijadi.com/uploads/history/1660392126_6404468a3ad1989a3488.webp',
        'https://kartasarijadi.com/uploads/history/1660392127_9d5f5f6baa97e523f848.webp',
        1,
        NULL,
        '2022-08-17 18:11:06'
    );

/*!40000 ALTER TABLE `info_history` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `info_landing`
--
DROP TABLE IF EXISTS `info_landing`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `info_landing` (
    `id` int(11) NOT NULL DEFAULT 1,
    `landing_title` varchar(64) NOT NULL DEFAULT '',
    `landing_tagline` varchar(512) NOT NULL DEFAULT '',
    `cta_text` varchar(16) NOT NULL DEFAULT '',
    `cta_url` varchar(256) NOT NULL DEFAULT '',
    `vision` varchar(512) NOT NULL DEFAULT '',
    `landing_image` varchar(256) NOT NULL DEFAULT '',
    `mission` mediumtext NOT NULL,
    `modified_by` int(11) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `FK_landing_info_users` (`modified_by`),
    CONSTRAINT `FK_landing_info_users` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `info_landing`
--
LOCK TABLES `info_landing` WRITE;

/*!40000 ALTER TABLE `info_landing` DISABLE KEYS */
;

INSERT INTO
    `info_landing`
VALUES
    (
        1,
        'Karang Taruna Sarijadi',
        'Sebagai wadah generasi muda Karang Taruna Ngajomantara Kelurahan Sarijadi hadir ditengah pemuda dan masyarakat untuk memaksimalkan potensi para pemuda Sarijadi agar terciptanya pemuda yang kreatif, inovatif, berintegritas, dan memiliki tanggungjawab sosial.',
        'Kirim Pesan',
        'https://kartasarijadi.com/hubungi-kami#kirim-pesan',
        'Terwujudnya Karang Taruna Ngajomantara Kelurahan Sarijadi ASIK (Adaptif, Sejahtera, Inovatif, dan Kreatif)',
        'https://kartasarijadi.com/uploads/organization-profile/1660971096_efae6bc9dc15a937c444.webp',
        'Pembangunan Pemuda[Membangun pemuda di Kelurahan Sarijadi yang adaptif, cerdas, dan religius.]\\nMenumbuhkan Nilai Sosial[Menumbuhkan nilai-nilai sosial kepada pemuda agar berdayaguna pada pengabdian dan ikut andil dalam peningkatan kesejahteraan masyarakat.]\\nProker Inovatif[Menciptakan program kerja yang inovatif dan didasari dengan kebutuhan para pemuda dan masyarakat di Kelurahan Sarijadi.]\\nEfektif dan Optimal[Efektif dan optimal dalam hal komunikasi, informasi dan pelaksanaan program kerja.]\\nEksistensi melalui prestasi[Meningkatkan eksistensi melalui prestasi minat dan bakat untuk melahirkan pemuda yang kreatif.]',
        1,
        NULL,
        '2023-01-06 11:37:08',
        NULL
    );

/*!40000 ALTER TABLE `info_landing` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `members`
--
DROP TABLE IF EXISTS `members`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `members` (
    `member_id` int(11) NOT NULL AUTO_INCREMENT,
    `member_name` varchar(50) NOT NULL DEFAULT '',
    `member_position` varchar(50) NOT NULL DEFAULT '',
    `member_type` enum('1', '2', '3', '4') NOT NULL DEFAULT '3',
    `member_active` enum('Aktif', 'Nonaktif') NOT NULL DEFAULT 'Aktif',
    `member_image` varchar(256) NOT NULL DEFAULT '',
    `created_by` int(11) DEFAULT NULL,
    `modified_by` int(11) DEFAULT NULL,
    `deleted_by` int(11) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`member_id`),
    KEY `FK_members_users` (`modified_by`),
    KEY `FK_members_users_2` (`created_by`),
    KEY `FK_members_users_3` (`deleted_by`),
    CONSTRAINT `FK_members_users` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE,
        CONSTRAINT `FK_members_users_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE,
        CONSTRAINT `FK_members_users_3` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 34 DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `members`
--
LOCK TABLES `members` WRITE;

/*!40000 ALTER TABLE `members` DISABLE KEYS */
;

INSERT INTO
    `members`
VALUES
    (
        1,
        'Irman Megantara',
        'Ketua',
        '1',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673004245_7b0d68a5a875c55a689c.webp',
        1,
        1,
        NULL,
        NULL,
        '2023-01-06 18:24:05',
        NULL
    ),
    (
        2,
        'Puspita Ayuningtyas',
        'Bendahara',
        '2',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672997362_9ed64773efbd1c51ceb1.webp',
        1,
        1,
        NULL,
        NULL,
        '2023-01-06 16:29:22',
        NULL
    ),
    (
        3,
        'Azvya Erstevan',
        'Bidang Kominsos',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672998335_7717b01c74bb14ac0b2f.webp',
        1,
        1,
        NULL,
        NULL,
        '2023-01-06 18:30:23',
        NULL
    ),
    (
        4,
        'M Taufan Z',
        'Bidang Minat Bakat',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673003356_60b038953a5854de72a2.webp',
        NULL,
        1,
        NULL,
        NULL,
        '2023-01-06 18:09:16',
        NULL
    ),
    (
        5,
        'Huthama Adhi',
        'Kepala Bidang Kominsos',
        '3',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672998551_41c2db4545792b6da28c.webp',
        NULL,
        1,
        NULL,
        NULL,
        '2023-01-06 18:30:01',
        NULL
    ),
    (
        6,
        'Tia Soleha',
        'Bidang Minat Bakat',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672994296_8fc137b6ae4fef16f62c.webp',
        NULL,
        1,
        NULL,
        NULL,
        '2023-01-06 15:38:16',
        NULL
    ),
    (
        7,
        'Ramzenia Namira P',
        'Kepala Bidang Ekraf',
        '3',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1660723581_4c91aed520973cb213a5.webp',
        NULL,
        1,
        1,
        '2022-08-12 15:58:17',
        '2023-01-06 11:41:14',
        '2023-01-06 11:41:14'
    ),
    (
        8,
        'Restu Iqbal',
        'Kepala Bidang Minat Bakat',
        '3',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672998212_bdf0c7eaeb037cd5738a.webp',
        NULL,
        1,
        NULL,
        NULL,
        '2023-01-06 16:43:32',
        NULL
    ),
    (
        9,
        'Satria Adi Nugraha',
        'Bidang Kominsos',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672998706_47b08ed7ab6ae96fcff9.webp',
        NULL,
        1,
        NULL,
        NULL,
        '2023-01-06 16:51:46',
        NULL
    ),
    (
        10,
        'Editya Meidyamara',
        'Sekretaris',
        '2',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672997373_b3f219f336a835d3a9bd.webp',
        NULL,
        1,
        NULL,
        NULL,
        '2023-01-06 16:29:33',
        NULL
    ),
    (
        11,
        'Natasha Sabila',
        'Bidang PSDM',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672994791_0b7878e09f1e57d3cac3.webp',
        NULL,
        1,
        NULL,
        NULL,
        '2023-01-06 15:46:31',
        NULL
    ),
    (
        12,
        'Baim',
        'Bidang OSDM',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1660724301_5d60cae01f1823e9f410.webp',
        1,
        1,
        1,
        '2022-08-17 14:38:18',
        '2023-01-06 11:41:14',
        '2023-01-06 11:41:14'
    ),
    (
        13,
        'Kiki Sodikin',
        'Bidang Minat Bakat',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672999660_ef11c039e71c332a724d.webp',
        1,
        1,
        NULL,
        '2022-08-17 14:38:59',
        '2023-01-06 17:07:40',
        NULL
    ),
    (
        14,
        'Nabila Gustia',
        'Bidang PSDM',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673001454_53de3539b7e751233205.webp',
        1,
        1,
        NULL,
        '2022-08-17 14:39:28',
        '2023-01-06 18:30:52',
        NULL
    ),
    (
        15,
        'Salsabila P Nadita',
        'Bidang Kominsos',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673002105_7d19cc39871295b1b119.webp',
        1,
        1,
        NULL,
        '2022-08-17 14:39:45',
        '2023-01-06 17:48:25',
        NULL
    ),
    (
        16,
        'Annisa N Afriyanti',
        'Bidang Minat Bakat',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673003482_140cc1b91f0bcc3ee8ca.webp',
        1,
        1,
        NULL,
        '2022-08-17 14:40:14',
        '2023-01-06 18:11:22',
        NULL
    ),
    (
        17,
        'Handoko',
        'Bidang Kominsos',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673003641_28d241ed41934717e2aa.webp',
        1,
        1,
        NULL,
        '2022-08-17 14:40:29',
        '2023-01-06 18:14:01',
        NULL
    ),
    (
        18,
        'Razan Ramadhan',
        'Bidang PSDM',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673003255_b663560426227bead7bb.webp',
        1,
        1,
        NULL,
        '2022-08-17 14:40:56',
        '2023-01-06 18:07:35',
        NULL
    ),
    (
        19,
        'Anis',
        'Bidang Orsosbud',
        '4',
        'Aktif',
        '',
        1,
        NULL,
        1,
        '2022-08-17 14:44:10',
        '2023-01-06 11:41:14',
        '2023-01-06 11:41:14'
    ),
    (
        20,
        'Rijal',
        'Bidang Orsosbud',
        '4',
        'Aktif',
        '',
        1,
        NULL,
        1,
        '2022-08-17 14:44:24',
        '2023-01-06 11:41:14',
        '2023-01-06 11:41:14'
    ),
    (
        21,
        'Salma Nurfatin',
        'Bidang Orsosbud',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1660734149_318de6de74ff1cd00d08.webp',
        1,
        1,
        1,
        '2022-08-17 14:46:42',
        '2023-01-06 11:41:14',
        '2023-01-06 11:41:14'
    ),
    (
        22,
        'Alfath',
        'Bidang Humas',
        '4',
        'Aktif',
        '',
        1,
        NULL,
        1,
        '2022-08-17 14:48:13',
        '2023-01-06 11:41:14',
        '2023-01-06 11:41:14'
    ),
    (
        26,
        'Miscolla Alfayed',
        'Kepala Bidang PSDM',
        '3',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672999540_903757914a3c187e6e87.webp',
        1,
        1,
        NULL,
        '2022-08-17 14:49:35',
        '2023-01-06 17:05:40',
        NULL
    ),
    (
        27,
        'M Haikal Yushendri',
        'Wakil Ketua',
        '1',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672994730_d800ce3b525beb6a7d80.webp',
        1,
        1,
        NULL,
        '2022-09-18 18:30:28',
        '2023-01-06 15:45:30',
        NULL
    ),
    (
        28,
        'M Satria Fitrah',
        'Bidang PSDM',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672994910_23e1a6d38e076065a4c6.webp',
        1,
        1,
        NULL,
        '2023-01-06 11:27:01',
        '2023-01-06 15:48:30',
        NULL
    ),
    (
        29,
        'Abdul Azis',
        'Bidang PSDM',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673003966_9b490a0621fabd0d6167.webp',
        1,
        1,
        NULL,
        '2023-01-06 11:27:42',
        '2023-01-06 18:19:26',
        NULL
    ),
    (
        30,
        'Karina Afriliana',
        'Bidang PSDM',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673003946_30b332f3c94b95801a7d.webp',
        1,
        1,
        NULL,
        '2023-01-06 11:27:56',
        '2023-01-06 18:19:06',
        NULL
    ),
    (
        31,
        'Indra Ibrahim Ginanjar',
        'Bidang Minat Bakat',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673004490_419d59b3a32ca6b2c2ec.webp',
        1,
        1,
        NULL,
        '2023-01-06 11:29:38',
        '2023-01-06 18:28:10',
        NULL
    ),
    (
        32,
        'Fachrully Adira',
        'Bidang Minat Bakat',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1672993943_6f3e90a61ceb1d48f65a.webp',
        1,
        1,
        NULL,
        '2023-01-06 11:30:24',
        '2023-01-06 15:32:23',
        NULL
    ),
    (
        33,
        ' Rd. Srimaharsi Yunandharu H',
        'Bidang Kominsos',
        '4',
        'Aktif',
        'https://kartasarijadi.com/uploads/members/1673003996_7e4be8261376ad5b9a8d.webp',
        1,
        1,
        NULL,
        '2023-01-06 11:31:58',
        '2023-01-06 18:19:56',
        NULL
    );

/*!40000 ALTER TABLE `members` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `menu`
--
DROP TABLE IF EXISTS `menu`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `menu` (
    `menu_id` int(11) NOT NULL AUTO_INCREMENT,
    `menu_string` varchar(50) NOT NULL,
    PRIMARY KEY (`menu_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `menu`
--
LOCK TABLES `menu` WRITE;

/*!40000 ALTER TABLE `menu` DISABLE KEYS */
;

INSERT INTO
    `menu`
VALUES
    (1, 'User\\Home'),
    (2, 'Content\\History'),
    (3, 'Content\\OrganizationProfile'),
    (4, 'User\\Profile'),
    (5, 'Data\\Pmpsks'),
    (7, 'Data\\Messages');

/*!40000 ALTER TABLE `menu` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `messages`
--
DROP TABLE IF EXISTS `messages`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `messages` (
    `message_id` int(11) NOT NULL AUTO_INCREMENT,
    `message_sender` varchar(64) NOT NULL,
    `message_whatsapp` varchar(32) NOT NULL,
    `message_type` enum('Kritik & Saran', 'Laporan/Aduan') NOT NULL DEFAULT 'Kritik & Saran',
    `message_text` varchar(280) NOT NULL DEFAULT '',
    `message_status` enum('Dibaca', 'Belum Dibaca') NOT NULL DEFAULT 'Belum Dibaca',
    `modified_by` int(11) DEFAULT NULL,
    `deleted_by` int(11) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`message_id`),
    KEY `FK_messages_users` (`deleted_by`),
    KEY `FK_messages_users_2` (`modified_by`),
    CONSTRAINT `FK_messages_users` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE,
        CONSTRAINT `FK_messages_users_2` FOREIGN KEY (`modified_by`) REFERENCES `users` (`user_id`) ON
    UPDATE
        CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 13 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Table structure for table `pages`
--
DROP TABLE IF EXISTS `pages`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `pages` (
    `page_id` int(11) NOT NULL AUTO_INCREMENT,
    `page_title` varchar(64) NOT NULL,
    `page_url` varchar(256) NOT NULL,
    `page_icon` varchar(128) NOT NULL,
    `menu_id` int(11) NOT NULL,
    PRIMARY KEY (`page_id`),
    KEY `FK_pages_menu` (`menu_id`),
    CONSTRAINT `FK_pages_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON
    UPDATE
        CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 9 DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `pages`
--
LOCK TABLES `pages` WRITE;

/*!40000 ALTER TABLE `pages` DISABLE KEYS */
;

INSERT INTO
    `pages`
VALUES
    (1, 'Dasbor', 'dasbor', 'bi bi-speedometer2', 1),
    (
        2,
        'Data PMKS',
        'data/pmks',
        'bi bi-person-rolodex',
        5
    ),
    (
        3,
        'Data PSKS',
        'data/psks',
        'bi bi-person-rolodex',
        5
    ),
    (
        4,
        'Pesan Aduan',
        'data/pesan',
        'bi bi-chat-left',
        7
    ),
    (
        6,
        'Pengaturan Profil Karta',
        'konten/profil-karang-taruna',
        'bi bi-building',
        3
    ),
    (
        7,
        'Pengaturan Sejarah',
        'konten/sejarah',
        'bi bi-hourglass',
        2
    ),
    (
        8,
        'Data Pengurus',
        'konten/profil-karang-taruna/pengurus',
        'bi bi-person',
        3
    );

/*!40000 ALTER TABLE `pages` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `pmpsks_img`
--
DROP TABLE IF EXISTS `pmpsks_img`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `pmpsks_img` (
    `pmpsks_img_id` int(11) NOT NULL AUTO_INCREMENT,
    `community_id` int(11) NOT NULL,
    `pmpsks_img_loc` varchar(256) NOT NULL,
    PRIMARY KEY (`pmpsks_img_id`),
    KEY `FK__communities` (`community_id`),
    CONSTRAINT `FK__communities` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON
    DELETE
        CASCADE ON
    UPDATE
        CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 10 DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `pmpsks_img`
--
LOCK TABLES `pmpsks_img` WRITE;

/*!40000 ALTER TABLE `pmpsks_img` DISABLE KEYS */
;

/*!40000 ALTER TABLE `pmpsks_img` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `pmpsks_types`
--
DROP TABLE IF EXISTS `pmpsks_types`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `pmpsks_types` (
    `pmpsks_id` int(11) NOT NULL AUTO_INCREMENT,
    `pmpsks_name` varchar(128) NOT NULL,
    `pmpsks_type` enum('PMKS', 'PSKS') NOT NULL,
    `pmpsks_category` enum('Individu', 'Keluarga', 'Lembaga') NOT NULL DEFAULT 'Individu',
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`pmpsks_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 39 DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `pmpsks_types`
--
LOCK TABLES `pmpsks_types` WRITE;

/*!40000 ALTER TABLE `pmpsks_types` DISABLE KEYS */
;

INSERT INTO
    `pmpsks_types`
VALUES
    (1, 'Balita terlantar', 'PMKS', 'Individu', NULL),
    (2, 'Anak terlantar', 'PMKS', 'Individu', NULL),
    (
        3,
        'Anak yang memiliki konflik dengan hukum',
        'PMKS',
        'Individu',
        NULL
    ),
    (4, 'Anak jalanan', 'PMKS', 'Individu', NULL),
    (
        5,
        'Anak dengan Kedisabilitasan',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        6,
        'Anak yang menjadi korban kekerasan atau penganiayaan',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        7,
        'Anak yang memerlukan perlindungan khusus',
        'PMKS',
        'Individu',
        NULL
    ),
    (8, 'Lansia terlantar', 'PMKS', 'Individu', NULL),
    (
        9,
        'Penyandang disabilitas',
        'PMKS',
        'Individu',
        NULL
    ),
    (10, 'Tuna susila', 'PMKS', 'Individu', NULL),
    (11, 'Gelandangan', 'PMKS', 'Individu', NULL),
    (12, 'Pengemis', 'PMKS', 'Individu', NULL),
    (13, 'Pemulung', 'PMKS', 'Individu', NULL),
    (
        14,
        'Kelompok minoritas',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        15,
        'Bekas Warga Binaan Lembaga Pemasyarakatan',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        16,
        'Orang dengan HIV/AIDS',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        17,
        'Korban penyalahgunaan narkoba',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        18,
        'Korban perdagangan orang',
        'PMKS',
        'Individu',
        NULL
    ),
    (19, 'Korban kekerasan', 'PMKS', 'Individu', NULL),
    (
        20,
        'Pekerja Migran Bermasalah Sosial',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        21,
        'Korban bencana alam',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        22,
        'Korban bencana sosial',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        23,
        'Perempuan rentan sosial ekonomi',
        'PMKS',
        'Individu',
        NULL
    ),
    (24, 'Fakir miskin', 'PMKS', 'Individu', NULL),
    (
        25,
        'Keluarga bermasalah sosiopsikologis',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        26,
        'Komunitas Adat Terpencil',
        'PMKS',
        'Individu',
        NULL
    ),
    (
        27,
        'Pekerja Sosial Profesional',
        'PSKS',
        'Individu',
        NULL
    ),
    (
        28,
        'Pekerja Sosial Masyarakat',
        'PSKS',
        'Individu',
        NULL
    ),
    (
        29,
        'Taruna Siaga Disaster',
        'PSKS',
        'Individu',
        NULL
    ),
    (
        30,
        'Lembaga Kesejahteraan Sosial',
        'PSKS',
        'Lembaga',
        NULL
    ),
    (31, 'Karang Taruna', 'PSKS', 'Lembaga', NULL),
    (
        32,
        'Lembaga Konsultasi Kesejahteraan Keluarga',
        'PSKS',
        'Lembaga',
        NULL
    ),
    (33, 'Keluarga pionir', 'PSKS', 'Keluarga', NULL),
    (34, 'WKSBM', 'PSKS', 'Lembaga', NULL),
    (
        35,
        'Pemimpin kesejahteraan sosial perempuan',
        'PSKS',
        'Individu',
        NULL
    ),
    (36, 'Penyuluh sosial', 'PSKS', 'Individu', NULL),
    (37, 'TKSM', 'PSKS', 'Individu', NULL),
    (38, 'Dunia usaha', 'PSKS', 'Lembaga', NULL);

/*!40000 ALTER TABLE `pmpsks_types` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `role_access`
--
DROP TABLE IF EXISTS `role_access`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `role_access` (
    `role_access_id` int(11) NOT NULL AUTO_INCREMENT,
    `role_id` int(11) NOT NULL,
    `menu_id` int(11) NOT NULL,
    PRIMARY KEY (`role_access_id`),
    UNIQUE KEY `role_id` (`role_id`, `menu_id`),
    KEY `FK_role_access_menu` (`menu_id`),
    CONSTRAINT `FK_role_access_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON
    DELETE
        CASCADE ON
    UPDATE
        CASCADE,
        CONSTRAINT `FK_role_access_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON
    DELETE
        CASCADE ON
    UPDATE
        CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 8 DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `role_access`
--
LOCK TABLES `role_access` WRITE;

/*!40000 ALTER TABLE `role_access` DISABLE KEYS */
;

INSERT INTO
    `role_access`
VALUES
    (1, 1, 1),
    (2, 1, 2),
    (3, 1, 3),
    (4, 1, 4),
    (5, 1, 5),
    (7, 1, 7);

/*!40000 ALTER TABLE `role_access` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `roles`
--
DROP TABLE IF EXISTS `roles`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `roles` (
    `role_id` int(11) NOT NULL AUTO_INCREMENT,
    `role_string` varchar(50) NOT NULL,
    `role_name` varchar(50) NOT NULL,
    PRIMARY KEY (`role_id`),
    UNIQUE KEY `role_string` (`role_string`)
) ENGINE = InnoDB AUTO_INCREMENT = 2 DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `roles`
--
LOCK TABLES `roles` WRITE;

/*!40000 ALTER TABLE `roles` DISABLE KEYS */
;

INSERT INTO
    `roles`
VALUES
    (1, 'admin', 'Administrator');

/*!40000 ALTER TABLE `roles` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Table structure for table `users`
--
DROP TABLE IF EXISTS `users`;

/*!40101 SET @saved_cs_client     = @@character_set_client */
;

/*!50503 SET character_set_client = utf8mb4 */
;

CREATE TABLE `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `role_id` int(11) NOT NULL,
    `user_name` varchar(128) NOT NULL,
    `user_email` varchar(64) NOT NULL,
    `user_password` varchar(128) NOT NULL,
    `user_temp_mail` varchar(64) DEFAULT NULL,
    `user_reset_attempt` datetime DEFAULT NULL,
    `user_change_mail` datetime DEFAULT NULL,
    `user_last_login` datetime DEFAULT NULL,
    `created_at` datetime DEFAULT current_timestamp(),
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `user_email` (`user_email`),
    KEY `FK_users_roles` (`role_id`),
    CONSTRAINT `FK_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON
    UPDATE
        CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 3 DEFAULT CHARSET = latin1 COLLATE = latin1_swedish_ci;

/*!40101 SET character_set_client = @saved_cs_client */
;

--
-- Dumping data for table `users`
--
LOCK TABLES `users` WRITE;

/*!40000 ALTER TABLE `users` DISABLE KEYS */
;

INSERT INTO
    `users`
VALUES
    (
        1,
        1,
        'Humas Ngajomantara',
        'karangtarunasarijadi@gmail.com',
        '$argon2i$v=19$m=65536,t=4,p=1$aE5vMEpKcG45NEtyOHg0Uw$YgVjFAgRJqUHBDtr2FLCMXjO6bkhKYliQ1kc+umpEXM16',
        'erstevn@gmail.com',
        NULL,
        '2022-08-18 00:54:47',
        '2023-02-05 15:37:21',
        NULL,
        '2023-02-05 15:37:21'
    );

/*!40000 ALTER TABLE `users` ENABLE KEYS */
;

UNLOCK TABLES;

--
-- Dumping routines for database 'kartasar_main'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */
;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */
;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */
;

/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */
;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */
;

-- Dump completed on 2023-02-15 16:10:36