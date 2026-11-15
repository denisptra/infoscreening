-- ============================================================================
-- Infoscreening.id - Production MySQL/MariaDB Dump for cPanel (phpMyAdmin)
-- Target Subdomain : https://next.infoscreening.id
-- Compatibility    : MySQL 8.0+ / MariaDB 10.6+
-- Charset          : utf8mb4 / utf8mb4_unicode_ci
-- ============================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+07:00";
SET NAMES utf8mb4;

-- ----------------------------------------------------------------------------
-- Table structure for `migrations`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `users`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_is_admin_index` (`is_admin`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `password_reset_tokens`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `sessions`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `cache`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `cache_locks`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `jobs`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `job_batches`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `failed_jobs`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `categories`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `hero_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `tags`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `articles`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `featured_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `reading_time` int unsigned NOT NULL DEFAULT 4,
  `views` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `wordpress_source_id` bigint unsigned DEFAULT NULL,
  `legacy_author_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_slug_unique` (`slug`),
  UNIQUE KEY `articles_wordpress_source_id_unique` (`wordpress_source_id`),
  KEY `articles_user_id_foreign` (`user_id`),
  KEY `articles_status_published_at_index` (`status`,`published_at`),
  KEY `articles_featured_published_at_index` (`is_featured`,`published_at`),
  KEY `articles_views_index` (`views`),
  CONSTRAINT `articles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `article_category`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `article_category`;
CREATE TABLE `article_category` (
  `article_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`article_id`,`category_id`),
  KEY `article_category_category_id_index` (`category_id`),
  CONSTRAINT `article_category_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `article_category_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `article_tag`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `article_tag`;
CREATE TABLE `article_tag` (
  `article_id` bigint unsigned NOT NULL,
  `tag_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`article_id`,`tag_id`),
  KEY `article_tag_tag_id_index` (`tag_id`),
  CONSTRAINT `article_tag_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `article_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `events`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pemutaran',
  `description` text COLLATE utf8mb4_unicode_ci,
  `venue` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `wordpress_source_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `events_slug_unique` (`slug`),
  UNIQUE KEY `events_wordpress_source_id_unique` (`wordpress_source_id`),
  KEY `events_published_starts_at_index` (`is_published`,`starts_at`),
  KEY `events_published_ends_at_index` (`is_published`,`ends_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `experts`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `experts`;
CREATE TABLE `experts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialty` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `products`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` int unsigned NOT NULL DEFAULT 0,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buy_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `contributions`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `contributions`;
CREATE TABLE `contributions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Artikel',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contributions_status_created_at_index` (`status`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `media`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL DEFAULT 0,
  `width` int unsigned DEFAULT NULL,
  `height` int unsigned DEFAULT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `navigation_items`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `navigation_items`;
CREATE TABLE `navigation_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `location` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT 0,
  `open_in_new_tab` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `navigation_items_location_parent_id_is_active_sort_order_index` (`location`,`parent_id`,`is_active`,`sort_order`),
  KEY `navigation_items_parent_id_foreign` (`parent_id`),
  CONSTRAINT `navigation_items_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `navigation_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table structure for `site_settings`
-- ----------------------------------------------------------------------------
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Seed Data
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. Recorded Migrations
-- ----------------------------------------------------------------------------
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_11_103319_create_categories_table', 1),
(5, '2026_10_11_103320_create_tags_table', 1),
(6, '2026_10_11_103321_create_articles_table', 1),
(7, '2026_10_11_114020_create_events_table', 1),
(8, '2026_10_11_114021_create_experts_table', 1),
(9, '2026_10_11_114022_create_products_table', 1),
(10, '2026_10_11_114023_create_contributions_table', 1),
(11, '2026_10_11_114024_add_admin_and_views_columns', 1),
(12, '2026_10_11_123000_create_media_table', 1),
(13, '2026_10_26_000000_add_performance_indexes', 1),
(14, '2026_11_04_021358_add_role_to_users_table', 1),
(15, '2026_11_04_021400_create_navigation_items_and_site_settings_tables', 1),
(16, '2026_11_04_022200_add_wordpress_source_columns_to_content_tables', 1),
(17, '2026_11_04_063246_rename_author_role_to_editor_role', 1);

-- ----------------------------------------------------------------------------
-- 2. Default Super Admin User
--    Email    : admin@infoscreening.id
--    Password : Infoscreening#2026!  (bcrypt cost=12 - CHANGE IMMEDIATELY!)
-- ----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `is_admin`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
(1, 'Super Admin Infoscreening', 'admin@infoscreening.id', NOW(), '$2y$12$SrxF7beyAGIRE8bviZpEU./xkgFPJAy.c0S1vQGwt0OlkuQA8iJma', 1, NULL, NOW(), NOW(), 'super_admin');

-- ----------------------------------------------------------------------------
-- 3. Default Categories (DefaultTaxonomySeeder)
-- ----------------------------------------------------------------------------
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `hero_image`, `color`, `created_at`, `updated_at`) VALUES
(1, 'Berita Film', 'berita-film', NULL, NULL, '#ba233b', NOW(), NOW()),
(2, 'Festival Film', 'festival-film', NULL, NULL, '#7344ff', NOW(), NOW()),
(3, 'Wawancara Sineas', 'wawancara-sineas', NULL, NULL, '#16807a', NOW(), NOW()),
(4, 'Kritik Film', 'kritik-film', NULL, NULL, '#b55a2a', NOW(), NOW()),
(5, 'Pemutaran Film', 'pemutaran-film', NULL, NULL, '#2563eb', NOW(), NOW());

-- ----------------------------------------------------------------------------
-- 4. Default Tags (DefaultTaxonomySeeder)
-- ----------------------------------------------------------------------------
INSERT INTO `tags` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Indonesia', 'indonesia', NULL, NOW(), NOW()),
(2, 'Festival Film', 'festival-film', NULL, NOW(), NOW()),
(3, 'Sinema Baru', 'sinema-baru', NULL, NOW(), NOW()),
(4, 'Dokumenter', 'dokumenter', NULL, NOW(), NOW()),
(5, 'Kritik Film', 'kritik-film', NULL, NOW(), NOW()),
(6, 'Masyarakat', 'masyarakat', NULL, NOW(), NOW()),
(7, 'Kebudayaan', 'kebudayaan', NULL, NOW(), NOW()),
(8, 'Film Pendek', 'film-pendek', NULL, NOW(), NOW());

-- ----------------------------------------------------------------------------
-- 5. Default Navigation Items (Migration + DefaultTaxonomySeeder)
-- ----------------------------------------------------------------------------
INSERT INTO `navigation_items` (`id`, `parent_id`, `location`, `label`, `url`, `sort_order`, `open_in_new_tab`, `is_active`, `created_at`, `updated_at`) VALUES
(1, NULL, 'header', 'Beranda', '/', 10, 0, 1, NOW(), NOW()),
(2, NULL, 'header', 'Artikel', '/artikel', 20, 0, 1, NOW(), NOW()),
(3, NULL, 'header', 'Kalender Event', '/events', 30, 0, 1, NOW(), NOW()),
(4, NULL, 'header', 'Berkontribusi', '/berkontribusi', 40, 0, 1, NOW(), NOW()),
(5, NULL, 'header', 'Tentang Kami', '/tentang-kami', 50, 0, 1, NOW(), NOW()),
(6, NULL, 'header', 'Rupa Jasa', '/rupa-jasa', 60, 0, 1, NOW(), NOW()),
(7, NULL, 'header', 'Toko / Zine', '/toko', 70, 0, 1, NOW(), NOW()),
(8, 2, 'header', 'Berita Film', '/kategori/berita-film', 10, 0, 1, NOW(), NOW()),
(9, 2, 'header', 'Wawancara Sineas', '/kategori/wawancara-sineas', 20, 0, 1, NOW(), NOW()),
(10, 2, 'header', 'Kritik Film', '/kategori/kritik-film', 30, 0, 1, NOW(), NOW()),
(11, 5, 'header', 'Profil Infoscreening', '/tentang-kami', 10, 0, 1, NOW(), NOW()),
(12, 5, 'header', 'Daftar Penulis', '/authors-list', 20, 0, 1, NOW(), NOW()),
(13, 5, 'header', 'Kontak Redaksi', '/kontak', 30, 0, 1, NOW(), NOW()),
(14, 5, 'header', 'Kebijakan Privasi', '/kebijakan-privasi', 40, 0, 1, NOW(), NOW()),
(15, 6, 'header', 'Layanan Kemitraan', '/rupa-jasa', 10, 0, 1, NOW(), NOW()),
(16, 6, 'header', 'Tenaga Ahli Sinema', '/rupa-jasa/tenaga-ahli', 20, 0, 1, NOW(), NOW()),
(17, NULL, 'footer', 'Rubrikasi Artikel', NULL, 10, 0, 1, NOW(), NOW()),
(18, NULL, 'footer', 'Ekosistem Sinema', NULL, 20, 0, 1, NOW(), NOW()),
(19, NULL, 'footer', 'Ikuti Kami', NULL, 30, 0, 1, NOW(), NOW()),
(20, 17, 'footer', 'Semua Artikel', '/artikel', 10, 0, 1, NOW(), NOW()),
(21, 17, 'footer', 'Berita Film', '/kategori/berita-film', 20, 0, 1, NOW(), NOW()),
(22, 17, 'footer', 'Festival Film', '/kategori/festival-film', 30, 0, 1, NOW(), NOW()),
(23, 18, 'footer', 'Kalender Acara', '/events', 10, 0, 1, NOW(), NOW()),
(24, 18, 'footer', 'Kirim Tulisan', '/berkontribusi', 20, 0, 1, NOW(), NOW()),
(25, 18, 'footer', 'Tenaga Ahli Film', '/rupa-jasa/tenaga-ahli', 30, 0, 1, NOW(), NOW()),
(26, 18, 'footer', 'Kontak Redaksi', '/kontak', 40, 0, 1, NOW(), NOW()),
(27, 19, 'footer', 'Instagram', 'https://instagram.com/infoscreening', 10, 1, 1, NOW(), NOW()),
(28, 19, 'footer', 'YouTube', 'https://youtube.com/user/infoscreening', 20, 1, 1, NOW(), NOW()),
(29, 2, 'header', 'Festival Film', '/kategori/festival-film', 20, 0, 1, NOW(), NOW()),
(30, 2, 'header', 'Pemutaran Film', '/kategori/pemutaran-film', 50, 0, 1, NOW(), NOW()),
(31, 17, 'footer', 'Wawancara Sineas', '/kategori/wawancara-sineas', 30, 0, 1, NOW(), NOW()),
(32, 17, 'footer', 'Kritik Film', '/kategori/kritik-film', 40, 0, 1, NOW(), NOW()),
(33, 17, 'footer', 'Pemutaran Film', '/kategori/pemutaran-film', 50, 0, 1, NOW(), NOW());

-- ----------------------------------------------------------------------------
-- 6. Default Site Settings
-- ----------------------------------------------------------------------------
INSERT INTO `site_settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'footer_about', 'Media informasi pemutaran sinema alternatif, festival film, dan ekosistem perfilman Indonesia.', NOW(), NOW()),
(2, 'footer_copyright', 'Infoscreening.id', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
