-- ============================================================================
-- 001_schema_consolidation.sql — JobMatch DavOr
--
-- Consolidates every schema change that used to be applied at request time
-- (runtime ALTER TABLE / CREATE TABLE inside models & controllers) into one
-- explicit, idempotent migration.
--
-- Idempotency: each column add is guarded by an information_schema check via
-- PREPARE, so this file is safe to run more than once on MySQL 5.7+.
--
-- Run once per environment, e.g.:
--   mysql -u <user> -p <database> < database/migrations/001_schema_consolidation.sql
-- ============================================================================

-- ---------------------------------------------------------------------------
-- worker_profile: TESDA fields (previously mutated in Profile::update)
-- ---------------------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'worker_profile' AND COLUMN_NAME = 'tesda_qualification');
SET @s := IF(@c = 0,
  "ALTER TABLE `worker_profile` ADD COLUMN `tesda_qualification` VARCHAR(150) NULL COMMENT 'TESDA Qualification / NC' AFTER `year_graduated`",
  "SELECT 'worker_profile.tesda_qualification exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'worker_profile' AND COLUMN_NAME = 'course');
SET @s := IF(@c = 0,
  "ALTER TABLE `worker_profile` ADD COLUMN `course` VARCHAR(120) NULL COMMENT 'Course / Program (optional)' AFTER `education_level`",
  "SELECT 'worker_profile.course exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'worker_profile' AND COLUMN_NAME = 'tesda_certs');
SET @s := IF(@c = 0,
  "ALTER TABLE `worker_profile` ADD COLUMN `tesda_certs` TEXT NULL COMMENT 'JSON array of TESDA certs: [{qualification, number, expiry}]' AFTER `tesda_expiry`",
  "SELECT 'worker_profile.tesda_certs exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ---------------------------------------------------------------------------
-- client_projects: duration/payment fields (previously mutated in Projects)
-- ---------------------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'client_projects' AND COLUMN_NAME = 'employment_term');
SET @s := IF(@c = 0,
  "ALTER TABLE `client_projects` ADD COLUMN `employment_term` VARCHAR(32) NULL AFTER `rate_unit`",
  "SELECT 'client_projects.employment_term exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'client_projects' AND COLUMN_NAME = 'payment_cycle');
SET @s := IF(@c = 0,
  "ALTER TABLE `client_projects` ADD COLUMN `payment_cycle` ENUM('monthly','yearly') NULL AFTER `employment_term`",
  "SELECT 'client_projects.payment_cycle exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'client_projects' AND COLUMN_NAME = 'project_duration_value');
SET @s := IF(@c = 0,
  "ALTER TABLE `client_projects` ADD COLUMN `project_duration_value` INT NULL AFTER `payment_cycle`",
  "SELECT 'client_projects.project_duration_value exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'client_projects' AND COLUMN_NAME = 'project_duration_unit');
SET @s := IF(@c = 0,
  "ALTER TABLE `client_projects` ADD COLUMN `project_duration_unit` ENUM('day','week','month','year') NULL AFTER `project_duration_value`",
  "SELECT 'client_projects.project_duration_unit exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ---------------------------------------------------------------------------
-- worker_experience (previously created + altered at model construct time)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `worker_experience` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `role`       VARCHAR(150) NOT NULL,
  `employer`   VARCHAR(180) DEFAULT NULL,
  `from`       VARCHAR(7)   DEFAULT NULL,
  `to`         VARCHAR(7)   DEFAULT NULL,
  `to_present` TINYINT(1)   NOT NULL DEFAULT 0,
  `desc`       TEXT,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'worker_experience' AND COLUMN_NAME = 'to_present');
SET @s := IF(@c = 0,
  "ALTER TABLE `worker_experience` ADD COLUMN `to_present` TINYINT(1) NOT NULL DEFAULT 0 AFTER `to`",
  "SELECT 'worker_experience.to_present exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'worker_experience' AND COLUMN_NAME = 'updated_at');
SET @s := IF(@c = 0,
  "ALTER TABLE `worker_experience` ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`",
  "SELECT 'worker_experience.updated_at exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ---------------------------------------------------------------------------
-- tesda_trainings (previously created + altered at model construct time)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tesda_trainings` (
  `id`            int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster_id`     int(10) unsigned NOT NULL,
  `title`         varchar(200) NOT NULL,
  `description`   text DEFAULT NULL,
  `website_url`   varchar(500) DEFAULT NULL,
  `image_path`    varchar(255) DEFAULT NULL,
  `location_text` varchar(255) DEFAULT NULL,
  `address_id`    int(10) unsigned DEFAULT NULL,
  `province`      varchar(120) DEFAULT NULL,
  `city`          varchar(120) DEFAULT NULL,
  `brgy`          varchar(120) DEFAULT NULL,
  `visibility`    enum('public','followers') NOT NULL DEFAULT 'public',
  `status`        enum('open','closed') NOT NULL DEFAULT 'open',
  `created_at`    datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at`    datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tesda_trainings_poster` (`poster_id`),
  KEY `idx_tesda_trainings_status` (`status`),
  KEY `idx_tesda_trainings_visibility` (`visibility`),
  KEY `idx_tesda_trainings_address` (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tesda_trainings' AND COLUMN_NAME = 'image_path');
SET @s := IF(@c = 0,
  "ALTER TABLE `tesda_trainings` ADD COLUMN `image_path` varchar(255) DEFAULT NULL AFTER `website_url`",
  "SELECT 'tesda_trainings.image_path exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ---------------------------------------------------------------------------
-- users lifecycle columns: reconcile is_active <-> status exactly once.
-- Canonical rule enforced by the app: a login is allowed only when
--   status = 'active' AND is_active = 1
-- ---------------------------------------------------------------------------
UPDATE `users` SET `is_active` = 0 WHERE `status` IN ('pending','suspended');
UPDATE `users` SET `status` = 'suspended' WHERE `is_active` = 0 AND `status` = 'active';
