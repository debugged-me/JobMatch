-- =====================================================================
-- NSRP Form 2 — Establishment Registration Form
-- Establishment + contact details -> client_profile
-- Vacancy + qualification + posting details -> jobs
-- Existing columns reused:
--   client_profile: companyName/business_name (Business Name), phoneNo (mobile),
--                   address/brgy/city/province, employer
--   jobs: title (Position Title), description (Job Description),
--         location_text, price_min/price_max, visibility, status, post_type
-- Safe to re-run.
-- =====================================================================

-- I/II. Establishment & contact details ---------------------------------
ALTER TABLE `client_profile`
  ADD COLUMN IF NOT EXISTS `trade_name`       VARCHAR(160) NULL AFTER `business_name`,
  ADD COLUMN IF NOT EXISTS `acronym`          VARCHAR(60)  NULL AFTER `trade_name`,
  ADD COLUMN IF NOT EXISTS `office_type`      ENUM('main','branch') NULL AFTER `acronym`,
  ADD COLUMN IF NOT EXISTS `tin`              VARCHAR(40)  NULL AFTER `office_type`,
  ADD COLUMN IF NOT EXISTS `employer_type`    ENUM('public','private') NULL AFTER `tin`,
  ADD COLUMN IF NOT EXISTS `employer_subtype` VARCHAR(80)  NULL AFTER `employer_type`,
  ADD COLUMN IF NOT EXISTS `workforce_size`   ENUM('micro','small','medium','large') NULL AFTER `employer_subtype`,
  ADD COLUMN IF NOT EXISTS `line_of_business` VARCHAR(200) NULL AFTER `workforce_size`,
  ADD COLUMN IF NOT EXISTS `street_village`   VARCHAR(160) NULL AFTER `line_of_business`,
  ADD COLUMN IF NOT EXISTS `owner_name`       VARCHAR(160) NULL AFTER `street_village`,
  ADD COLUMN IF NOT EXISTS `contact_person`   VARCHAR(160) NULL AFTER `owner_name`,
  ADD COLUMN IF NOT EXISTS `contact_position` VARCHAR(120) NULL AFTER `contact_person`,
  ADD COLUMN IF NOT EXISTS `telephone`        VARCHAR(45)  NULL AFTER `contact_position`,
  ADD COLUMN IF NOT EXISTS `fax`              VARCHAR(45)  NULL AFTER `telephone`,
  ADD COLUMN IF NOT EXISTS `nsrp_status`      ENUM('draft','submitted','assessed') NOT NULL DEFAULT 'draft' AFTER `fax`;

-- III/IV/V. Vacancy, qualification & posting details + PESO use ----------
ALTER TABLE `jobs`
  ADD COLUMN IF NOT EXISTS `establishment_id`        INT(10) UNSIGNED NULL AFTER `poster_id`,
  ADD COLUMN IF NOT EXISTS `nature_of_work`          VARCHAR(40)  NULL AFTER `description`,
  ADD COLUMN IF NOT EXISTS `place_of_work`           VARCHAR(200) NULL AFTER `nature_of_work`,
  ADD COLUMN IF NOT EXISTS `salary`                  VARCHAR(120) NULL AFTER `place_of_work`,
  ADD COLUMN IF NOT EXISTS `vacancy_count`           INT(11)      NULL AFTER `salary`,
  ADD COLUMN IF NOT EXISTS `work_experience_months`  INT(11)      NULL AFTER `vacancy_count`,
  ADD COLUMN IF NOT EXISTS `other_qualifications`    TEXT         NULL AFTER `work_experience_months`,
  ADD COLUMN IF NOT EXISTS `accepts_pwd`             TINYINT(1)   NULL AFTER `other_qualifications`,
  ADD COLUMN IF NOT EXISTS `pwd_types`               VARCHAR(160) NULL AFTER `accepts_pwd`,
  ADD COLUMN IF NOT EXISTS `accepts_ofw`             TINYINT(1)   NULL AFTER `pwd_types`,
  ADD COLUMN IF NOT EXISTS `educational_level`       VARCHAR(80)  NULL AFTER `accepts_ofw`,
  ADD COLUMN IF NOT EXISTS `course_strand`           VARCHAR(160) NULL AFTER `educational_level`,
  ADD COLUMN IF NOT EXISTS `license`                 VARCHAR(160) NULL AFTER `course_strand`,
  ADD COLUMN IF NOT EXISTS `eligibility`             VARCHAR(160) NULL AFTER `license`,
  ADD COLUMN IF NOT EXISTS `certification`           VARCHAR(160) NULL AFTER `eligibility`,
  ADD COLUMN IF NOT EXISTS `language`                VARCHAR(160) NULL AFTER `certification`,
  ADD COLUMN IF NOT EXISTS `posting_date`            DATE         NULL AFTER `language`,
  ADD COLUMN IF NOT EXISTS `valid_until`             DATE         NULL AFTER `posting_date`,
  ADD COLUMN IF NOT EXISTS `assessed_by`             VARCHAR(160) NULL AFTER `valid_until`,
  ADD COLUMN IF NOT EXISTS `encoded_by`              VARCHAR(160) NULL AFTER `assessed_by`;
