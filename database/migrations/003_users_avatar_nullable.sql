-- 003_users_avatar_nullable.sql
-- The users table carries 4 legacy avatar columns (avatar, photo, image,
-- profile_pic) that are NOT NULL with no default. Any INSERT that doesn't
-- set them (e.g. signup) fails under STRICT_TRANS_TABLES. Make them
-- nullable pending the planned schema consolidation (see AGENTS.md debt).
-- Idempotent: MODIFY is a no-op when the column is already nullable.

ALTER TABLE `users`
  MODIFY `avatar`      VARCHAR(150) NULL,
  MODIFY `photo`       VARCHAR(150) NULL,
  MODIFY `image`       VARCHAR(150) NULL,
  MODIFY `profile_pic` VARCHAR(150) NULL;
