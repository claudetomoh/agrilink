-- ================================================================
-- AgriLink – Advanced Features Migration
-- Run ONCE after schema.sql to add advanced feature columns.
-- ================================================================

-- ----------------------------------------------------------------
-- Users: Add verification system columns
-- ----------------------------------------------------------------
ALTER TABLE `users`
  ADD COLUMN `is_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`,
  ADD COLUMN `verified_at` TIMESTAMP NULL DEFAULT NULL AFTER `is_verified`,
  ADD COLUMN `password_reset_token` VARCHAR(255) DEFAULT NULL AFTER `verified_at`,
  ADD COLUMN `password_reset_expires_at` DATETIME DEFAULT NULL AFTER `password_reset_token`,
  ADD COLUMN `password_reset_requested_at` DATETIME DEFAULT NULL AFTER `password_reset_expires_at`;

-- ----------------------------------------------------------------
-- Produce: Add low stock threshold (alert when stock falls below)
-- ----------------------------------------------------------------
ALTER TABLE `produce`
  ADD COLUMN `low_stock_threshold` DECIMAL(10,2) NOT NULL DEFAULT 10.00 AFTER `quantity`;

-- ----------------------------------------------------------------
-- Reviews: Add unique constraint (one review per order per reviewer)
-- ----------------------------------------------------------------
ALTER TABLE `reviews`
  ADD UNIQUE KEY `uniq_order_reviewer` (`order_id`, `reviewer_id`);

-- ----------------------------------------------------------------
-- Mark established users as verified
-- ----------------------------------------------------------------
UPDATE `users` SET `is_verified` = 1, `verified_at` = `created_at`
WHERE `email` IN (
  'kofi.boateng@agrilink.gh',
  'ama.serwaa@agrilink.gh',
  'kwame.mensah@agrilink.gh',
  'kojo.logistics@agrilink.gh'
);
