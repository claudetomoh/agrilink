-- ================================================================
-- AgriLink – Development Seed Data
-- Ghana Agricultural Marketplace & Logistics System
--
-- Run AFTER schema.sql to add comprehensive test data.
-- Safe to re-run: uses INSERT IGNORE or skips existing records.
-- ================================================================

USE `mobileapps_2026B_tomoh_ikfingeh`;

-- ----------------------------------------------------------------
-- Additional Users
-- All test passwords: Pass@1234
-- Hash: $2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e
-- ----------------------------------------------------------------
INSERT IGNORE INTO `users` (`name`,`email`,`phone`,`password`,`role`,`region`,`town`) VALUES
-- Additional farmers
('Akosua Twum','akosua.twum@agrilink.gh','+233244111004',
 '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e','farmer','Eastern','Koforidua'),
('Nana Adu','nana.adu@agrilink.gh','+233244111005',
 '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e','farmer','Central','Cape Coast'),
-- Additional buyers
('Efua Asante','efua.asante@agrilink.gh','+233244222003',
 '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e','buyer','Greater Accra','Osu, Accra'),
('Fiifi Boateng','fiifi.boateng@agrilink.gh','+233244222004',
 '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e','buyer','Western','Takoradi'),
-- Additional transporter
('Afia Logistics','afia.transport@agrilink.gh','+233244333002',
 '$2y$12$Lmmm/jDsbgAoa/E64xe8xO2HGXDjRrYwpGcVmN7audLDerfDpn47e','transport','Greater Accra','Accra');

-- ----------------------------------------------------------------
-- Additional Produce Listings (from new farmers, ids 8 and 9)
-- ----------------------------------------------------------------
INSERT IGNORE INTO `produce`
  (`farmer_id`,`name`,`category`,`description`,`quantity`,`unit`,`price_per_unit`,`region`,`town`,`grade`,`harvest_date`,`status`)
VALUES
(8,'Pineapple','fruits','Sweet Pineapples from Nsawam farms, Grade A, ready for market.',400,'crates',35.00,'Eastern','Nsawam','A','2024-11-05','available'),
(8,'Cocoyam','tubers','Fresh cocoyam from Eastern region. Soft and ideal for soups.',80,'bags',120.00,'Eastern','Koforidua','A','2024-10-31','available'),
(9,'Watermelon','fruits','Large, sweet watermelons from Central region coastal farms.',90,'pieces',18.00,'Central','Cape Coast','A','2024-11-02','available'),
(9,'Pepper','vegetables','Fresh chilli peppers, mixed variety, ideal for sauce and stew production.',60,'crates',55.00,'Central','Winneba','A','2024-11-03','available'),
(2,'Kontomire (Cocoyam Leaves)','vegetables','Fresh kontomire leaves, bundled, great for palava sauce.',150,'bunches',12.00,'Ashanti','Kumasi','A','2024-11-01','available'),
(4,'Cowpea (Beans)','legumes','Dried cowpea, white variety, Grade A. Popular across Ghana.',200,'bags',95.00,'Northern','Wa','A','2024-10-20','available');

-- ----------------------------------------------------------------
-- Additional Orders (using existing and new user IDs)
-- Users: buyers are 5 (Kwame), 6 (Abena), 10 (Efua), 11 (Fiifi)
-- Farmers: 2 (Kofi), 3 (Ama), 4 (Yaw), 8 (Akosua), 9 (Nana)
-- ----------------------------------------------------------------
INSERT IGNORE INTO `orders`
  (`order_ref`,`buyer_id`,`farmer_id`,`produce_id`,`quantity`,`unit`,`unit_price`,`total_price`,`status`,`delivery_address`)
VALUES
('AL-4420',10,8,9,20,'crates',35.00,700.00,'confirmed','Ridge, Accra'),
('AL-3318',11,4,6,10,'bags',90.00,900.00,'delivered','Takoradi Port Area'),
('AL-2214',10,4,5,30,'bags',60.00,1800.00,'pending','Osu, Accra'),
('AL-1109',6,9,11,5,'crates',55.00,275.00,'in_transit','Tema Main Market'),
('AL-0087',5,2,2,15,'bags',85.00,1275.00,'processing','Accra New Town');

-- ----------------------------------------------------------------
-- Deliveries for new orders
-- Transporter ID 7 (Kojo) and 12 (Afia)
-- ----------------------------------------------------------------
INSERT IGNORE INTO `deliveries`
  (`order_id`,`transport_id`,`vehicle_code`,`origin`,`destination`,`status`,`assigned_at`,`estimated_arrival`)
VALUES
(6,12,'GH-5582','Nsawam','Ridge, Accra','assigned',NOW() - INTERVAL 3 HOUR, NOW() + INTERVAL 8 HOUR),
(7,7,'GH-2204','Tamale','Takoradi Port Area','delivered',NOW() - INTERVAL 6 DAY, NULL),
(9,12,'GH-6601','Winneba','Tema Main Market','in_transit',NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 2 HOUR);

-- ----------------------------------------------------------------
-- Bids
-- ----------------------------------------------------------------
INSERT IGNORE INTO `bids` (`produce_id`,`buyer_id`,`quantity`,`bid_price`,`status`,`message`) VALUES
(1,5,20,'140.00','pending','Can you do ₵140/bag for 20 bags? We buy regularly every fortnight.'),
(3,6,10,'300.00','pending','Asking ₵300/bag for 10 bags. We are an established exporter.'),
(5,5,50,'55.00','accepted','₵55/bag for 50 bags — whole batch. Please confirm.'),
(7,10,100,'40.00','pending','₵40/crate for 100 crates if delivered to Accra within 3 days.'),
(9,11,8,'50.00','pending','₵50/crate for 8 crates. We run a restaurant in Takoradi.');

-- ----------------------------------------------------------------
-- Additional Notifications
-- ----------------------------------------------------------------
INSERT IGNORE INTO `notifications` (`user_id`,`type`,`title`,`message`,`link`,`is_read`) VALUES
(2,'bid_received','New Bid on White Yam','Kwame Mensah placed a bid of ₵140/bag for 20 bags of White Yam.','/farmer/listings','0'),
(3,'bid_received','New Bid on Cocoa','Abena Ofori placed a bid of ₵300/bag for 10 bags of Ashanti Cocoa.','/farmer/listings','0'),
(6,'order_confirmed','Order Confirmed','Your order #AL-8831 was confirmed and is now delivered.','/buyer/orders','1'),
(7,'job_available','New Delivery Job Available','A delivery job from Tamale to Takoradi is available.','/transport/jobs','0'),
(12,'new_account','Welcome to AgriLink!','Your transporter account has been created. Start accepting delivery jobs.','/transport/dashboard','0'),
(8,'new_account','Welcome, Farmer!','Your farmer account is active. Start listing your produce today.','/farmer/listings','0'),
(9,'new_account','Welcome, Farmer!','Your farmer account is active. List your produce to connect with buyers.','/farmer/listings/add','0'),
(10,'new_account','Welcome to AgriLink!','Welcome Efua! Start buying fresh produce from Ghanaian farmers.','/buyer/marketplace','0'),
(2,'order_placed','New Order for Tomatoes','Efua Asante wants 20 bags of Cassava. Review and confirm.','/farmer/orders','0');

-- ----------------------------------------------------------------
-- Reviews (for delivered orders)
-- ----------------------------------------------------------------
INSERT IGNORE INTO `reviews` (`order_id`,`reviewer_id`,`reviewee_id`,`produce_id`,`rating`,`comment`) VALUES
(2,6,3,3,5,'Excellent cocoa beans. Premium quality as described. Delivery was smooth. 5 stars!'),
(2,3,6,NULL,4,'Great buyer. Paid promptly and communicated well throughout.'),
(7,11,4,6,4,'Good quality groundnuts. Packaging was adequate. Delivery was on time. Would reorder.'),
(7,4,11,NULL,5,'Professional buyer. Clear communication and paid promptly. Recommend.');

-- ----------------------------------------------------------------
-- Additional Notifications (for all feature demos)
-- ----------------------------------------------------------------
INSERT IGNORE INTO `notifications` (`user_id`,`type`,`title`,`message`,`link`,`is_read`) VALUES
-- Low stock alerts (sent by system to farmers)
(4,'low_stock','Low Stock: Groundnuts','Your Groundnuts listing is running low (8 bags remaining). Restock to avoid missed orders.','/farmer/listings','0'),
(2,'low_stock','Low Stock: Tomatoes','Your Tomatoes listing has only 7 crates left. Update your quantity soon.','/farmer/listings','0'),
-- Review notifications
(4,'review_received','Review From Fiifi Boateng','Fiifi Boateng left you a 4-star review: "Good quality groundnuts. Delivery was on time."','/farmer/orders','0'),
(3,'review_received','Review From Abena Ofori','Abena Ofori left you a 5-star review: "Excellent cocoa beans!"','/farmer/orders','1'),
-- Verification notifications
(3,'account_verified','Account Verified!','Congratulations! Your AgriLink account has been verified. A Verified badge now shows on your listings.','/farmer/dashboard','0'),
(4,'account_verified','Account Verified!','Your farmer account is now verified. Buyers trust verified sellers more.','/farmer/dashboard','0'),
-- Order status updates
(11,'order_status','Order Delivered','Your order AL-3318 (Groundnuts) has been delivered successfully.','/buyer/orders','1'),
(10,'order_placed','Order Confirmed','Your order AL-4420 for Pineapples has been confirmed by the farmer.','/buyer/orders','0'),
-- Bid updates
(5,'bid_received','Bid on Maize Accepted','Your bid of ₵55/bag for 50 bags of Maize has been accepted.','/buyer/orders','0'),
(11,'bid_received','New Bid','Fiifi Boateng placed a bid on Pineapple listing.','/farmer/listings','0');

-- ----------------------------------------------------------------
-- Low-stock scenario setup (run AFTER migration.sql)
-- Sets thresholds so some produce shows as "low stock" for demo
-- ----------------------------------------------------------------
UPDATE `produce` SET `low_stock_threshold` = 15.00 WHERE `name` = 'Tomatoes' AND `farmer_id` = 2;
UPDATE `produce` SET `quantity` = 7.00 WHERE `name` = 'Tomatoes' AND `farmer_id` = 2;
UPDATE `produce` SET `low_stock_threshold` = 15.00 WHERE `name` = 'Groundnuts';
UPDATE `produce` SET `quantity` = 8.00 WHERE `name` = 'Groundnuts';
