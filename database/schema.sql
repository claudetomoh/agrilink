-- ================================================================
-- AgriLink Database Schema
-- Ghana Agricultural Marketplace & Logistics System
-- ================================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

USE `mobileapps_2026B_tomoh_ikfingeh`;

-- ----------------------------------------------------------------
-- Table: users
-- ----------------------------------------------------------------
CREATE TABLE `users` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`          VARCHAR(150)  NOT NULL,
  `email`         VARCHAR(200)  NOT NULL UNIQUE,
  `phone`         VARCHAR(20)   DEFAULT NULL,
  `password`      VARCHAR(255)  NOT NULL,
  `role`          ENUM('farmer','buyer','transport','admin') NOT NULL DEFAULT 'buyer',
  `region`        VARCHAR(100)  DEFAULT NULL COMMENT 'Ghana region e.g. Ashanti, Greater Accra',
  `town`          VARCHAR(100)  DEFAULT NULL,
  `profile_photo` VARCHAR(255)  DEFAULT NULL,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- Table: produce
-- ----------------------------------------------------------------
CREATE TABLE `produce` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `farmer_id`      INT UNSIGNED NOT NULL,
  `name`           VARCHAR(150) NOT NULL,
  `category`       ENUM('tubers','cereals','legumes','vegetables','fruits','cash_crops','other') NOT NULL,
  `description`    TEXT         DEFAULT NULL,
  `quantity`       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `unit`           ENUM('kg','bags','tons','crates','bunches','pieces') NOT NULL DEFAULT 'bags',
  `price_per_unit` DECIMAL(10,2) NOT NULL,
  `region`         VARCHAR(100) NOT NULL,
  `town`           VARCHAR(100) DEFAULT NULL,
  `grade`          ENUM('A','B','C') DEFAULT 'A',
  `harvest_date`   DATE         DEFAULT NULL,
  `image`          VARCHAR(255) DEFAULT NULL,
  `status`         ENUM('available','reserved','sold','archived') NOT NULL DEFAULT 'available',
  `created_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_produce_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- Table: orders
-- ----------------------------------------------------------------
CREATE TABLE `orders` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_ref`   VARCHAR(20)   NOT NULL UNIQUE COMMENT 'e.g. AL-9422',
  `buyer_id`    INT UNSIGNED  NOT NULL,
  `farmer_id`   INT UNSIGNED  NOT NULL,
  `produce_id`  INT UNSIGNED  NOT NULL,
  `quantity`    DECIMAL(10,2) NOT NULL,
  `unit`        VARCHAR(20)   NOT NULL,
  `unit_price`  DECIMAL(10,2) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `status`      ENUM('pending','confirmed','processing','in_transit','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `delivery_address` VARCHAR(255) DEFAULT NULL,
  `notes`       TEXT          DEFAULT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_order_buyer`   FOREIGN KEY (`buyer_id`)   REFERENCES `users`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_order_farmer`  FOREIGN KEY (`farmer_id`)  REFERENCES `users`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_order_produce` FOREIGN KEY (`produce_id`) REFERENCES `produce`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- Table: deliveries
-- ----------------------------------------------------------------
CREATE TABLE `deliveries` (
  `id`                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`            INT UNSIGNED  NOT NULL UNIQUE,
  `transport_id`        INT UNSIGNED  DEFAULT NULL COMMENT 'Assigned transport provider',
  `vehicle_code`        VARCHAR(30)   DEFAULT NULL COMMENT 'e.g. GH-2204',
  `origin`              VARCHAR(150)  NOT NULL,
  `destination`         VARCHAR(150)  NOT NULL,
  `status`              ENUM('pending','assigned','in_transit','delivered','failed') NOT NULL DEFAULT 'pending',
  `assigned_at`         TIMESTAMP    NULL DEFAULT NULL,
  `picked_up_at`        TIMESTAMP    NULL DEFAULT NULL,
  `delivered_at`        TIMESTAMP    NULL DEFAULT NULL,
  `estimated_arrival`   DATETIME     NULL DEFAULT NULL,
  `notes`               TEXT         DEFAULT NULL,
  `created_at`          TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_delivery_order`     FOREIGN KEY (`order_id`)     REFERENCES `orders`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_delivery_transport` FOREIGN KEY (`transport_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- Table: reviews
-- ----------------------------------------------------------------
CREATE TABLE `reviews` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`    INT UNSIGNED NOT NULL,
  `reviewer_id` INT UNSIGNED NOT NULL,
  `reviewee_id` INT UNSIGNED NOT NULL,
  `produce_id`  INT UNSIGNED DEFAULT NULL,
  `rating`      TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment`     TEXT         DEFAULT NULL,
  `created_at`  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_review_order`    FOREIGN KEY (`order_id`)    REFERENCES `orders`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_review_reviewee` FOREIGN KEY (`reviewee_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- Table: notifications
-- ----------------------------------------------------------------
CREATE TABLE `notifications` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `type`       VARCHAR(50)  NOT NULL COMMENT 'order_placed, delivery_update, bid_received, etc.',
  `title`      VARCHAR(200) NOT NULL,
  `message`    TEXT         NOT NULL,
  `link`       VARCHAR(255) DEFAULT NULL,
  `is_read`    TINYINT(1)  NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- Table: bids (Buyer bids on produce)
-- ----------------------------------------------------------------
CREATE TABLE `bids` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `produce_id` INT UNSIGNED  NOT NULL,
  `buyer_id`   INT UNSIGNED  NOT NULL,
  `quantity`   DECIMAL(10,2) NOT NULL,
  `bid_price`  DECIMAL(10,2) NOT NULL,
  `status`     ENUM('pending','accepted','rejected','expired') NOT NULL DEFAULT 'pending',
  `message`    TEXT         DEFAULT NULL,
  `created_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_bid_produce` FOREIGN KEY (`produce_id`) REFERENCES `produce`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bid_buyer`   FOREIGN KEY (`buyer_id`)   REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------
-- Indexes for performance
-- ----------------------------------------------------------------
CREATE INDEX `idx_produce_farmer`   ON `produce`(`farmer_id`);
CREATE INDEX `idx_produce_category` ON `produce`(`category`);
CREATE INDEX `idx_produce_region`   ON `produce`(`region`);
CREATE INDEX `idx_produce_status`   ON `produce`(`status`);
CREATE INDEX `idx_orders_buyer`     ON `orders`(`buyer_id`);
CREATE INDEX `idx_orders_farmer`    ON `orders`(`farmer_id`);
CREATE INDEX `idx_orders_status`    ON `orders`(`status`);
CREATE INDEX `idx_deliveries_transport` ON `deliveries`(`transport_id`);
CREATE INDEX `idx_notif_user_read`  ON `notifications`(`user_id`, `is_read`);

-- ================================================================
-- SEED DATA – Realistic Ghana context
-- ================================================================

-- Admin user (password: Admin@1234)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`region`,`town`) VALUES
('Kweku Admin','admin@agrilink.gh','+233244000001',
 '$2y$12$Uq1TKvw2Q5ItlXXqHvXQoOfPtF7M1RKbStHHw0AIJPlEJYS0cVCGu','admin','Greater Accra','Accra');

-- Farmer users (password: Pass@1234)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`region`,`town`) VALUES
('Kofi Boateng','kofi.boateng@agrilink.gh','+233244111001',
 '$2y$12$sZ5gEX6LJNqpV5UF2pLMdOrn5RKjJhPXn2dXMtCq3Hb.I8HEfN3qK','farmer','Ashanti','Kumasi'),
('Ama Serwaa','ama.serwaa@agrilink.gh','+233244111002',
 '$2y$12$sZ5gEX6LJNqpV5UF2pLMdOrn5RKjJhPXn2dXMtCq3Hb.I8HEfN3qK','farmer','Brong-Ahafo','Sunyani'),
('Yaw Asante','yaw.asante@agrilink.gh','+233244111003',
 '$2y$12$sZ5gEX6LJNqpV5UF2pLMdOrn5RKjJhPXn2dXMtCq3Hb.I8HEfN3qK','farmer','Northern','Tamale');

-- Buyer users (password: Pass@1234)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`region`,`town`) VALUES
('Kwame Mensah','kwame.mensah@agrilink.gh','+233244222001',
 '$2y$12$sZ5gEX6LJNqpV5UF2pLMdOrn5RKjJhPXn2dXMtCq3Hb.I8HEfN3qK','buyer','Greater Accra','Accra'),
('Abena Ofori','abena.ofori@agrilink.gh','+233244222002',
 '$2y$12$sZ5gEX6LJNqpV5UF2pLMdOrn5RKjJhPXn2dXMtCq3Hb.I8HEfN3qK','buyer','Greater Accra','Tema');

-- Transport users (password: Pass@1234)
INSERT INTO `users` (`name`,`email`,`phone`,`password`,`role`,`region`,`town`) VALUES
('Kojo Transport','kojo.logistics@agrilink.gh','+233244333001',
 '$2y$12$sZ5gEX6LJNqpV5UF2pLMdOrn5RKjJhPXn2dXMtCq3Hb.I8HEfN3qK','transport','Ashanti','Kumasi');

-- Produce listings
INSERT INTO `produce` (`farmer_id`,`name`,`category`,`description`,`quantity`,`unit`,`price_per_unit`,`region`,`town`,`grade`,`harvest_date`,`status`) VALUES
(2,'White Yam','tubers','Premium Grade A white yam, freshly harvested from Ashanti region farms.',50,'bags',150.00,'Ashanti','Kumasi','A','2024-10-24','available'),
(2,'Cassava','tubers','Fresh cassava tubers, properly cleaned and ready for processing.',120,'bags',85.00,'Ashanti','Kumasi','A','2024-10-28','available'),
(3,'Ashanti Cocoa','cash_crops','Premium fermented and dried cocoa beans from certified farms.',80,'bags',320.00,'Brong-Ahafo','Sunyani','A','2024-09-15','available'),
(3,'Plantain','fruits','Ripe plantain bunches, ready for market, Grade A quality.',200,'bunches',25.00,'Brong-Ahafo','Sunyani','A','2024-10-30','available'),
(4,'Maize','cereals','Dried yellow maize, very suitable for poultry and human consumption.',500,'bags',60.00,'Northern','Tamale','B','2024-10-10','available'),
(4,'Groundnuts','legumes','Shelled groundnuts, clean and dry. Excellent for export.',150,'bags',90.00,'Northern','Tamale','A','2024-10-05','available'),
(2,'Tomatoes','vegetables','Fresh plum tomatoes, just harvested, excellent for wholesale.',300,'crates',45.00,'Ashanti','Mampong','A','2024-11-01','available'),
(4,'Shea Butter Nuts','cash_crops','Unprocessed shea nuts, collected from natural parklands in the North.',200,'bags',110.00,'Northern','Bolgatanga','A','2024-09-20','available');

-- Sample orders
INSERT INTO `orders` (`order_ref`,`buyer_id`,`farmer_id`,`produce_id`,`quantity`,`unit`,`unit_price`,`total_price`,`status`,`delivery_address`) VALUES
('AL-9422',5,2,1,10,'bags',150.00,1500.00,'in_transit','Accra Central Market, Accra'),
('AL-8831',6,3,3,5,'bags',320.00,1600.00,'delivered','Tema Industrial Area, Tema'),
('AL-7195',5,4,5,20,'bags',60.00,1200.00,'confirmed','Agbogbloshie Market, Accra'),
('AL-6044',5,2,7,15,'crates',45.00,675.00,'pending','Madina Market, Accra'),
('AL-5521',6,3,4,30,'bunches',25.00,750.00,'processing','Ashaiman Market, Ashaiman');

-- Deliveries
INSERT INTO `deliveries` (`order_id`,`transport_id`,`vehicle_code`,`origin`,`destination`,`status`,`assigned_at`,`picked_up_at`,`estimated_arrival`) VALUES
(1,7,'GH-2204','Kumasi Central Market','Accra Central Market','in_transit',NOW() - INTERVAL 2 DAY,NOW() - INTERVAL 1 DAY,NOW() + INTERVAL 4 HOUR),
(2,7,'GH-1890','Sunyani','Tema Industrial Area','delivered',NOW() - INTERVAL 5 DAY,NOW() - INTERVAL 4 DAY,NULL),
(3,7,'GH-3310','Tamale Grain Market','Agbogbloshie Market','assigned',NOW() - INTERVAL 1 HOUR,NULL,NOW() + INTERVAL 1 DAY),
(5,7,'GH-4455','Sunyani Plantain Farm','Ashaiman Market','assigned',NOW() - INTERVAL 30 MINUTE,NULL,NOW() + INTERVAL 6 HOUR);

-- Notifications
INSERT INTO `notifications` (`user_id`,`type`,`title`,`message`,`link`,`is_read`) VALUES
(2,'order_placed','New Order Received','Kwame Mensah placed an order for 10 bags of White Yam.','/farmer/orders','0'),
(5,'order_confirmed','Order Confirmed','Your order #AL-9422 has been confirmed by Kofi Boateng.','/buyer/orders','0'),
(7,'delivery_assigned','New Delivery Job','You have been assigned delivery #AL-9422. Origin: Kumasi.','/transport/deliveries','0'),
(5,'delivery_update','Order In Transit','Your order #AL-9422 is now in transit to Accra.','/buyer/track/1','0');
