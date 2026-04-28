CREATE TABLE IF NOT EXISTS agrilink_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  phone VARCHAR(20) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('farmer','buyer','transport','admin') NOT NULL DEFAULT 'buyer',
  region VARCHAR(100) DEFAULT NULL,
  town VARCHAR(100) DEFAULT NULL,
  profile_photo VARCHAR(255) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  verified_at TIMESTAMP NULL DEFAULT NULL,
  password_reset_token VARCHAR(255) DEFAULT NULL,
  password_reset_expires_at DATETIME DEFAULT NULL,
  password_reset_requested_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS agrilink_notifications (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  type VARCHAR(50) NOT NULL,
  title VARCHAR(200) NOT NULL,
  message TEXT NOT NULL,
  link VARCHAR(255) DEFAULT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_agrilink_notif_user_read (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO agrilink_users (id, name, email, phone, password, role, region, town, is_active, is_verified, verified_at)
VALUES
  (1, 'Kweku Admin', 'admin@agrilink.gh', '+233244000001', '$2y$12$qABpsccCfJeJGFo1jsyaqeW6kwA.ZueBQCbMB0wrfOZVCtmCE4L0.', 'admin', 'Greater Accra', 'Accra', 1, 1, NOW()),
  (2, 'Kofi Boateng', 'kofi.boateng@agrilink.gh', '+233244111001', '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e', 'farmer', 'Ashanti', 'Kumasi', 1, 1, NOW()),
  (3, 'Ama Serwaa', 'ama.serwaa@agrilink.gh', '+233244111002', '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e', 'farmer', 'Brong-Ahafo', 'Sunyani', 1, 1, NOW()),
  (4, 'Yaw Asante', 'yaw.asante@agrilink.gh', '+233244111003', '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e', 'farmer', 'Northern', 'Tamale', 1, 0, NULL),
  (5, 'Kwame Mensah', 'kwame.mensah@agrilink.gh', '+233244222001', '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e', 'buyer', 'Greater Accra', 'Accra', 1, 1, NOW()),
  (6, 'Abena Ofori', 'abena.ofori@agrilink.gh', '+233244222002', '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e', 'buyer', 'Greater Accra', 'Tema', 1, 0, NULL),
  (7, 'Kojo Transport', 'kojo.logistics@agrilink.gh', '+233244333001', '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e', 'transport', 'Ashanti', 'Kumasi', 1, 1, NOW())
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  phone = VALUES(phone),
  password = VALUES(password),
  role = VALUES(role),
  region = VALUES(region),
  town = VALUES(town),
  is_active = VALUES(is_active),
  is_verified = VALUES(is_verified),
  verified_at = VALUES(verified_at);
