/*M!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `collections` (
  `id` int(1) DEFAULT NULL,
  `shipping_company_id` int(1) DEFAULT NULL,
  `amount` int(5) DEFAULT NULL,
  `collection_date` varchar(19) DEFAULT NULL,
  `notes` varchar(10) DEFAULT NULL,
  `created_by` int(1) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `delivery_agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_agents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(4) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `email` varchar(17) DEFAULT NULL,
  `address` varchar(7) DEFAULT NULL,
  `national_id` bigint(14) DEFAULT NULL,
  `user_id` int(1) DEFAULT NULL,
  `shipping_company_id` int(1) DEFAULT NULL,
  `max_edit_count` int(2) DEFAULT NULL,
  `is_active` int(1) DEFAULT NULL,
  `notes` varchar(9) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` int(1) DEFAULT NULL,
  `title` varchar(5) DEFAULT NULL,
  `amount` int(4) DEFAULT NULL,
  `expense_date` varchar(19) DEFAULT NULL,
  `notes` varchar(10) DEFAULT NULL,
  `created_by` int(1) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` varchar(10) DEFAULT NULL,
  `uuid` varchar(10) DEFAULT NULL,
  `connection` varchar(10) DEFAULT NULL,
  `queue` varchar(10) DEFAULT NULL,
  `payload` varchar(10) DEFAULT NULL,
  `exception` varchar(10) DEFAULT NULL,
  `failed_at` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(2) DEFAULT NULL,
  `migration` varchar(73) DEFAULT NULL,
  `batch` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` varchar(10) DEFAULT NULL,
  `model_type` varchar(10) DEFAULT NULL,
  `model_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` int(1) DEFAULT NULL,
  `model_type` varchar(15) DEFAULT NULL,
  `model_id` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(10) DEFAULT NULL,
  `token` varchar(10) DEFAULT NULL,
  `created_at` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_role` (
  `id` varchar(10) DEFAULT NULL,
  `role_id` varchar(10) DEFAULT NULL,
  `permission_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int(2) DEFAULT NULL,
  `name` varchar(26) DEFAULT NULL,
  `guard_name` varchar(3) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL,
  `description` varchar(24) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` varchar(10) DEFAULT NULL,
  `tokenable_type` varchar(10) DEFAULT NULL,
  `tokenable_id` varchar(10) DEFAULT NULL,
  `name` varchar(10) DEFAULT NULL,
  `token` varchar(10) DEFAULT NULL,
  `abilities` varchar(10) DEFAULT NULL,
  `last_used_at` varchar(10) DEFAULT NULL,
  `expires_at` varchar(10) DEFAULT NULL,
  `created_at` varchar(10) DEFAULT NULL,
  `updated_at` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(1) DEFAULT NULL,
  `name` varchar(15) DEFAULT NULL,
  `price` int(3) DEFAULT NULL,
  `colors` varchar(22) DEFAULT NULL,
  `sizes` varchar(58) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` int(2) DEFAULT NULL,
  `role_id` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permission` (
  `id` varchar(10) DEFAULT NULL,
  `role_id` varchar(10) DEFAULT NULL,
  `permission_id` varchar(10) DEFAULT NULL,
  `created_at` varchar(10) DEFAULT NULL,
  `updated_at` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int(1) DEFAULT NULL,
  `name` varchar(14) DEFAULT NULL,
  `guard_name` varchar(3) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL,
  `description` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int(2) DEFAULT NULL,
  `key` varchar(19) DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL,
  `description` varchar(11) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shipment_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipment_statuses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort_order` int(11) DEFAULT 0,
  `name` varchar(13) DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `is_default` int(1) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL,
  `row_color` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shipments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_company_id` int(1) DEFAULT NULL,
  `delivery_agent_id` varchar(10) DEFAULT NULL,
  `status_id` int(2) DEFAULT NULL,
  `tracking_number` varchar(11) DEFAULT NULL,
  `customer_name` varchar(37) DEFAULT NULL,
  `customer_phone` varchar(25) DEFAULT NULL,
  `customer_address` varchar(17) DEFAULT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `product_description` varchar(20) DEFAULT NULL,
  `quantity` int(1) DEFAULT NULL,
  `cost_price` int(1) DEFAULT NULL,
  `selling_price` int(3) DEFAULT NULL,
  `shipping_date` varchar(19) DEFAULT NULL,
  `delivery_date` varchar(10) DEFAULT NULL,
  `return_date` varchar(10) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `agent_notes` varchar(10) DEFAULT NULL,
  `edit_count` int(1) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL,
  `total_amount` int(3) DEFAULT NULL,
  `color` varchar(6) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `governorate` varchar(100) DEFAULT NULL,
  `shipping_price` int(2) DEFAULT NULL,
  `product_id` int(1) DEFAULT NULL,
  `shipping_company` varchar(25) DEFAULT NULL,
  `is_printed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shipping_companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` int(1) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sqlite_sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sqlite_sequence` (
  `name` varchar(18) DEFAULT NULL,
  `seq` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(2) DEFAULT NULL,
  `name` varchar(6) DEFAULT NULL,
  `email` varchar(19) DEFAULT NULL,
  `email_verified_at` varchar(10) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `remember_token` varchar(10) DEFAULT NULL,
  `created_at` varchar(19) DEFAULT NULL,
  `updated_at` varchar(19) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `role_id` varchar(2) DEFAULT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `address` varchar(93) DEFAULT NULL,
  `is_active` int(1) DEFAULT NULL,
  `expires_at` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/*M!999999\- enable the sandbox mode */ 
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_12_100000_create_password_reset_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_04_26_180405_create_delivery_agents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_04_26_180405_create_permissions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_04_26_180405_create_role_permission_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_04_26_180405_create_roles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_04_26_180405_create_shipping_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_04_26_180406_create_collections_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_04_26_180406_create_expenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_04_26_180406_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_04_26_180406_create_shipment_statuses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_04_26_180406_create_shipments_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_04_27_025811_create_products_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_04_27_040907_add_total_color_size_to_shipments_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_04_29_121241_add_governorate_to_shipments_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_04_29_121826_add_shipping_price_to_shipments_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_04_29_122224_add_product_id_to_shipments_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_04_29_150130_add_shipping_company_to_shipments_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_04_29_161449_modify_shipping_company_id_nullable',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_04_30_015920_add_row_color_to_shipment_statuses_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_05_01_025742_add_role_id_to_users_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_05_01_031205_add_phone_address_is_active_to_users_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_05_01_031540_create_permission_role_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_04_26_182939_create_delivery_agents_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_04_28_085733_add_missing_fields_to_shipments_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_04_28_090043_2025_04_28_085733_add_missing_fields_to_shipments_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_05_01_050759_create_permission_tables',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_05_01_070809_add_expires_at_to_users_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_05_01_073501_add_description_to_permissions_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_05_01_081543_add_description_to_roles_table',18);
