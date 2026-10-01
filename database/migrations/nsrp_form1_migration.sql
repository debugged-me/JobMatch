-- =====================================================================
-- NSRP Form 1 (Rev.3) — Jobseeker Registration
-- Adds the DOLE/PESO registration fields to worker_profile.
-- Existing columns reused: brgy, city, province (present address),
--   phoneNo (primary mobile), education_level, school, course,
--   year_graduated, tesda_*, skills, languages, exp (work experience JSON),
--   credentials.
-- Safe to re-run: every column uses ADD COLUMN IF NOT EXISTS.
-- =====================================================================

-- I. Personal Information ------------------------------------------------
ALTER TABLE `worker_profile`
  ADD COLUMN IF NOT EXISTS `sex`               ENUM('male','female')      NULL AFTER `headline`,
  ADD COLUMN IF NOT EXISTS `date_of_birth`     DATE                       NULL AFTER `sex`,
  ADD COLUMN IF NOT EXISTS `place_of_birth`    VARCHAR(160)               NULL AFTER `date_of_birth`,
  ADD COLUMN IF NOT EXISTS `civil_status`      VARCHAR(20)                NULL AFTER `place_of_birth`,
  ADD COLUMN IF NOT EXISTS `citizenship`       VARCHAR(60)                NULL AFTER `civil_status`,
  ADD COLUMN IF NOT EXISTS `religion`          VARCHAR(80)                NULL AFTER `citizenship`,
  ADD COLUMN IF NOT EXISTS `height_cm`         DECIMAL(5,2)               NULL AFTER `religion`,
  ADD COLUMN IF NOT EXISTS `weight_kg`         DECIMAL(5,2)               NULL AFTER `height_cm`,
  ADD COLUMN IF NOT EXISTS `landline`          VARCHAR(45)                NULL AFTER `phoneNo`,
  ADD COLUMN IF NOT EXISTS `mobile_secondary`  VARCHAR(45)                NULL AFTER `landline`,
  ADD COLUMN IF NOT EXISTS `present_street`    VARCHAR(160)               NULL AFTER `brgy`,
  ADD COLUMN IF NOT EXISTS `perm_same_as_present` TINYINT(1) NOT NULL DEFAULT 0 AFTER `present_street`,
  ADD COLUMN IF NOT EXISTS `perm_street`       VARCHAR(160)               NULL AFTER `perm_same_as_present`,
  ADD COLUMN IF NOT EXISTS `perm_brgy`         VARCHAR(120)               NULL AFTER `perm_street`,
  ADD COLUMN IF NOT EXISTS `perm_city`         VARCHAR(120)               NULL AFTER `perm_brgy`,
  ADD COLUMN IF NOT EXISTS `perm_province`     VARCHAR(120)               NULL AFTER `perm_city`,
  ADD COLUMN IF NOT EXISTS `disability`        VARCHAR(160)               NULL AFTER `perm_province`;

-- Employment status & flags ---------------------------------------------
ALTER TABLE `worker_profile`
  ADD COLUMN IF NOT EXISTS `employment_status`    VARCHAR(40)  NULL AFTER `disability`,
  ADD COLUMN IF NOT EXISTS `employment_substatus` VARCHAR(80)  NULL AFTER `employment_status`,
  ADD COLUMN IF NOT EXISTS `actively_looking`     TINYINT(1)   NULL AFTER `employment_substatus`,
  ADD COLUMN IF NOT EXISTS `looking_duration`     VARCHAR(60)  NULL AFTER `actively_looking`,
  ADD COLUMN IF NOT EXISTS `willing_immediate`    TINYINT(1)   NULL AFTER `looking_duration`,
  ADD COLUMN IF NOT EXISTS `available_when`       VARCHAR(60)  NULL AFTER `willing_immediate`,
  ADD COLUMN IF NOT EXISTS `is_4ps`               TINYINT(1)   NULL AFTER `available_when`,
  ADD COLUMN IF NOT EXISTS `fourps_household_id`  VARCHAR(60)  NULL AFTER `is_4ps`,
  ADD COLUMN IF NOT EXISTS `is_ofw`               TINYINT(1)   NULL AFTER `fourps_household_id`,
  ADD COLUMN IF NOT EXISTS `ofw_returning`        TINYINT(1)   NULL AFTER `is_ofw`;

-- II. Job Preference -----------------------------------------------------
ALTER TABLE `worker_profile`
  ADD COLUMN IF NOT EXISTS `pref_occupations`         TEXT          NULL AFTER `ofw_returning`,
  ADD COLUMN IF NOT EXISTS `pref_locations_local`     TEXT          NULL AFTER `pref_occupations`,
  ADD COLUMN IF NOT EXISTS `pref_locations_overseas`  TEXT          NULL AFTER `pref_locations_local`,
  ADD COLUMN IF NOT EXISTS `salary_expectation`       DECIMAL(12,2) NULL AFTER `pref_locations_overseas`;

-- V. Eligibility / language certs ---------------------------------------
ALTER TABLE `worker_profile`
  ADD COLUMN IF NOT EXISTS `eligibilities`  TEXT NULL AFTER `nc_list`,
  ADD COLUMN IF NOT EXISTS `language_certs` TEXT NULL AFTER `eligibilities`;

-- VII / IX. Skills self-assessment --------------------------------------
ALTER TABLE `worker_profile`
  ADD COLUMN IF NOT EXISTS `century_skills`       TEXT NULL AFTER `language_certs`,
  ADD COLUMN IF NOT EXISTS `tech_skills_informal` TEXT NULL AFTER `century_skills`;

-- FOR USE OF PESO ONLY --------------------------------------------------
ALTER TABLE `worker_profile`
  ADD COLUMN IF NOT EXISTS `peso_eligibility` VARCHAR(160) NULL AFTER `tech_skills_informal`,
  ADD COLUMN IF NOT EXISTS `assessed_by`      VARCHAR(160) NULL AFTER `peso_eligibility`,
  ADD COLUMN IF NOT EXISTS `assessed_at`      DATE         NULL AFTER `assessed_by`,
  ADD COLUMN IF NOT EXISTS `nsrp_reference`   VARCHAR(40)  NULL AFTER `assessed_at`,
  ADD COLUMN IF NOT EXISTS `nsrp_status`      ENUM('draft','submitted','assessed') NOT NULL DEFAULT 'draft' AFTER `nsrp_reference`;
