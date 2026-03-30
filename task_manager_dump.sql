CREATE DATABASE IF NOT EXISTS `task_manager`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `task_manager`;

DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id`        int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255)     NOT NULL,
  `batch`     int(11)          NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('2014_10_12_000000_create_users_table', 1),
  ('2014_10_12_100000_create_password_reset_tokens_table', 1),
  ('2019_08_19_000000_create_failed_jobs_table', 1),
  ('2019_12_14_000001_create_personal_access_tokens_table', 1),
  ('2024_01_01_000000_create_tasks_table', 1);

CREATE TABLE `tasks` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      varchar(255)        NOT NULL,
  `due_date`   date                NOT NULL,
  `priority`   enum('low','medium','high')          NOT NULL,
  `status`     enum('pending','in_progress','done') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_title_due_date` (`title`, `due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tasks` (`title`, `due_date`, `priority`, `status`, `created_at`, `updated_at`) VALUES
  ('Fix critical production bug',  CURDATE(),                              'high',   'in_progress', NOW(), NOW()),
  ('Deploy security patch',        CURDATE(),                              'high',   'pending',     NOW(), NOW()),
  ('Review authentication flow',   DATE_ADD(CURDATE(), INTERVAL 1 DAY),   'high',   'done',        NOW(), NOW()),
  ('Write unit tests for API',     DATE_ADD(CURDATE(), INTERVAL 1 DAY),   'medium', 'pending',     NOW(), NOW()),
  ('Update project documentation', DATE_ADD(CURDATE(), INTERVAL 7 DAY),   'medium', 'done',        NOW(), NOW()),
  ('Refactor legacy helper functions', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'low', 'pending',      NOW(), NOW()),
  ('Clean up unused CSS',              DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'low', 'done',         NOW(), NOW());