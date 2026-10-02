-- ============================================================================
-- 002_philsys_verification.sql — JobMatch DavOr
--
-- Adds PhilSys (National ID) scan/verification columns to `users`.
-- Only the SHA-256 hash of the card number (PCN) is stored — never the raw
-- PCN — per RA 11055 privacy guidance. The hash also lets us enforce that
-- one national ID can only be attached to one account.
--
-- Idempotent: every change is guarded by an information_schema check via
-- PREPARE, so this file is safe to run more than once on MySQL 5.7+.
--
--   mysql -u <user> -p <database> < database/migrations/002_philsys_verification.sql
-- ============================================================================

-- ---------------------------------------------------------------------------
-- users: PhilSys verification columns
-- ---------------------------------------------------------------------------
SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philsys_pcn_hash');
SET @s := IF(@c = 0,
  "ALTER TABLE `users` ADD COLUMN `philsys_pcn_hash` CHAR(64) NULL COMMENT 'SHA-256 of PhilSys Card Number (raw PCN never stored)' AFTER `email_verified_at`",
  "SELECT 'users.philsys_pcn_hash exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philsys_status');
SET @s := IF(@c = 0,
  "ALTER TABLE `users` ADD COLUMN `philsys_status` ENUM('pending','verified','failed') NULL COMMENT 'NULL = no ID scanned; pending = awaits staff/online confirmation' AFTER `philsys_pcn_hash`",
  "SELECT 'users.philsys_status exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philsys_id_type');
SET @s := IF(@c = 0,
  "ALTER TABLE `users` ADD COLUMN `philsys_id_type` VARCHAR(10) NULL COMMENT 'philid (physical card) or ephilid (digital)' AFTER `philsys_status`",
  "SELECT 'users.philsys_id_type exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philsys_name');
SET @s := IF(@c = 0,
  "ALTER TABLE `users` ADD COLUMN `philsys_name` VARCHAR(200) NULL COMMENT 'Full name as read from the national ID' AFTER `philsys_id_type`",
  "SELECT 'users.philsys_name exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philsys_dob');
SET @s := IF(@c = 0,
  "ALTER TABLE `users` ADD COLUMN `philsys_dob` DATE NULL AFTER `philsys_name`",
  "SELECT 'users.philsys_dob exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philsys_sex');
SET @s := IF(@c = 0,
  "ALTER TABLE `users` ADD COLUMN `philsys_sex` VARCHAR(20) NULL AFTER `philsys_dob`",
  "SELECT 'users.philsys_sex exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philsys_scanned_at');
SET @s := IF(@c = 0,
  "ALTER TABLE `users` ADD COLUMN `philsys_scanned_at` DATETIME NULL AFTER `philsys_sex`",
  "SELECT 'users.philsys_scanned_at exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philsys_verified_by');
SET @s := IF(@c = 0,
  "ALTER TABLE `users` ADD COLUMN `philsys_verified_by` INT NULL COMMENT 'Staff user id who confirmed; NULL = self-scanned auto-verified' AFTER `philsys_scanned_at`",
  "SELECT 'users.philsys_verified_by exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @c := (SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'philsys_verified_at');
SET @s := IF(@c = 0,
  "ALTER TABLE `users` ADD COLUMN `philsys_verified_at` DATETIME NULL AFTER `philsys_verified_by`",
  "SELECT 'users.philsys_verified_at exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- One national ID per account (unique index on the hash).
SET @i := (SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'uq_users_philsys_pcn');
SET @s := IF(@i = 0,
  "CREATE UNIQUE INDEX `uq_users_philsys_pcn` ON `users` (`philsys_pcn_hash`)",
  "SELECT 'uq_users_philsys_pcn exists' AS note");
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;
