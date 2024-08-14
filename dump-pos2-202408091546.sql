-- MySQL dump 10.13  Distrib 5.6.51, for Win64 (x86_64)
--
-- Host: localhost    Database: pos2
-- ------------------------------------------------------
-- Server version	5.6.51

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `_docprefix`
--

DROP TABLE IF EXISTS `_docprefix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `_docprefix` (
  `docType` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `prefix` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `_docprefix_doctype_unique` (`docType`),
  UNIQUE KEY `_docprefix_prefix_unique` (`prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `_docprefix`
--

LOCK TABLES `_docprefix` WRITE;
/*!40000 ALTER TABLE `_docprefix` DISABLE KEYS */;
INSERT INTO `_docprefix` VALUES ('bookings','BO'),('memberships','M'),('purchases','PO'),('sales','SO');
/*!40000 ALTER TABLE `_docprefix` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_details`
--

DROP TABLE IF EXISTS `booking_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `buy_price` decimal(18,0) NOT NULL DEFAULT '0',
  `sell_price` decimal(18,0) NOT NULL DEFAULT '0',
  `sub_total` decimal(18,0) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_details_booking_id_foreign` (`booking_id`),
  KEY `booking_details_item_id_foreign` (`item_id`),
  CONSTRAINT `booking_details_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`code`),
  CONSTRAINT `booking_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_details`
--

LOCK TABLES `booking_details` WRITE;
/*!40000 ALTER TABLE `booking_details` DISABLE KEYS */;
INSERT INTO `booking_details` VALUES (1,'BO230812-0001',1,1,45000,50000,50000,'','2023-08-12 04:39:58','2023-08-12 04:56:50');
/*!40000 ALTER TABLE `booking_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `code` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `nik` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `necessary` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `date_booking` date NOT NULL,
  `discount` decimal(18,0) NOT NULL DEFAULT '0',
  `sub_total` decimal(18,0) NOT NULL DEFAULT '0',
  `tax` decimal(18,0) NOT NULL DEFAULT '0',
  `total` decimal(18,0) NOT NULL DEFAULT '0',
  `status` enum('pending','confirm','cancel','close') COLLATE utf8_unicode_ci NOT NULL,
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_created` bigint(20) unsigned DEFAULT NULL,
  `membership_code` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  UNIQUE KEY `bookings_code_unique` (`code`),
  KEY `bookings_membership_code_foreign` (`membership_code`),
  KEY `bookings_user_created_foreign` (`user_created`),
  KEY `bookings_user_updated_foreign` (`user_updated`),
  CONSTRAINT `bookings_membership_code_foreign` FOREIGN KEY (`membership_code`) REFERENCES `memberships` (`code`),
  CONSTRAINT `bookings_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `bookings_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES ('BO230812-0001','3503060901020001','Hendik Endtato','083987459348','Jalan Mawar no 5','Pedes','2023-08-12',0,50000,0,50000,'confirm',0,'2023-08-12 04:39:58','2023-08-12 04:56:50',1,'M00001',1);
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_details`
--

DROP TABLE IF EXISTS `cart_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `buy_price` decimal(18,0) NOT NULL DEFAULT '0',
  `sell_price` decimal(18,0) NOT NULL DEFAULT '0',
  `sub_total` decimal(18,0) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `served` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_details_cart_id_foreign` (`cart_id`),
  KEY `cart_details_item_id_foreign` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_details`
--

LOCK TABLES `cart_details` WRITE;
/*!40000 ALTER TABLE `cart_details` DISABLE KEYS */;
INSERT INTO `cart_details` VALUES (9,4,8,1,0,20000,20000,NULL,0,NULL,NULL),(10,4,4,1,0,6600,6600,NULL,0,NULL,NULL),(16,9,7,1,0,50000,50000,'Tambah cabe',0,NULL,NULL),(17,9,5,1,0,68750,68750,NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `cart_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `table` int(11) DEFAULT NULL,
  `disc` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `disc_rp` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax_rp` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_created` bigint(20) unsigned DEFAULT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carts_user_created_foreign` (`user_created`),
  KEY `carts_user_updated_foreign` (`user_updated`),
  CONSTRAINT `carts_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `carts_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (4,6,'10','2','2660','478.8',1,NULL,'2024-05-30 08:36:47',NULL),(9,10,NULL,NULL,'0','0',1,NULL,'2024-05-31 02:38:04',NULL);
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_in`
--

DROP TABLE IF EXISTS `cash_in`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_dokumen` varchar(50) DEFAULT NULL,
  `akun_kasbank` varchar(50) DEFAULT NULL,
  `tgl_transaksi` date DEFAULT NULL,
  `total_nominal` double DEFAULT NULL,
  `terima_dari` varchar(255) DEFAULT NULL,
  `total_pembayaran` double DEFAULT NULL,
  `total_biaya` double DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_in`
--

LOCK TABLES `cash_in` WRITE;
/*!40000 ALTER TABLE `cash_in` DISABLE KEYS */;
INSERT INTO `cash_in` VALUES (18,'CashIn-0001','19','2023-09-21',200000,'-',200000,200000,0,'2023-09-21 10:33:39',NULL),(19,'BKM-20230922-01','19','2023-09-22',250000,'tes',250000,250000,0,'2023-09-22 13:53:26',NULL),(20,'BKM-20231019-01','4','2023-10-19',200000,'tes',200000,200000,0,'2023-10-19 08:50:43',NULL),(21,'BKM-20231020-01','4','2023-10-20',150000,'tes',150000,150000,0,'2023-10-20 08:56:07',NULL);
/*!40000 ALTER TABLE `cash_in` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_in_details`
--

DROP TABLE IF EXISTS `cash_in_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash_in_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cash_in` int(11) NOT NULL,
  `akun_pendapatan` varchar(50) DEFAULT NULL,
  `tgl_pelaksanaan` date DEFAULT NULL,
  `nominal` double DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_in_details`
--

LOCK TABLES `cash_in_details` WRITE;
/*!40000 ALTER TABLE `cash_in_details` DISABLE KEYS */;
INSERT INTO `cash_in_details` VALUES (8,18,'91','2023-09-21',100000,'-','2023-09-21 10:33:39',NULL),(9,18,'93','2023-09-21',100000,'-','2023-09-21 10:33:39',NULL),(10,19,'91','2023-09-22',50000,'Tes1','2023-09-22 13:53:26',NULL),(11,19,'94','2023-09-22',25000,'Tes2','2023-09-22 13:53:26',NULL),(12,19,'93','2023-09-22',100000,'Tes3','2023-09-22 13:53:26',NULL),(13,19,'92','2023-09-22',75000,'Tes4','2023-09-22 13:53:26',NULL),(14,20,'101','2023-10-19',125000,'-','2023-10-19 08:50:43',NULL),(15,20,'102','2023-10-19',50000,'-','2023-10-19 08:50:44',NULL),(16,20,'101','2023-10-19',25000,'-','2023-10-19 08:50:44',NULL),(17,21,'100','2023-10-20',100000,'-','2023-10-20 08:56:07',NULL),(18,21,'107','2023-10-20',50000,'-','2023-10-20 08:56:07',NULL);
/*!40000 ALTER TABLE `cash_in_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_out`
--

DROP TABLE IF EXISTS `cash_out`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash_out` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_dokumen` varchar(50) DEFAULT NULL,
  `akun_kasbank` varchar(50) DEFAULT NULL,
  `tgl_transaksi` date DEFAULT NULL,
  `total_nominal` double DEFAULT NULL,
  `bayar_kepada` varchar(255) DEFAULT NULL,
  `total_pembayaran` double DEFAULT NULL,
  `total_biaya` double DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_out`
--

LOCK TABLES `cash_out` WRITE;
/*!40000 ALTER TABLE `cash_out` DISABLE KEYS */;
INSERT INTO `cash_out` VALUES (11,'BKK-20230131-08',NULL,'2023-07-27',20000,'tesss',20000,20000,0,'2023-07-27 10:06:48',NULL),(14,'BKK-20230131-09',NULL,'2023-07-27',100000,'tess',100000,100000,0,'2023-07-27 10:11:20',NULL),(15,'BKK-20230727-01',NULL,'2023-07-27',100000,'Supplier 1',100000,100000,0,'2023-07-27 11:41:53',NULL),(17,'BKK-20230914-01','19','2023-09-14',200000,'Tes',200000,200000,0,'2023-09-14 14:56:40',NULL),(18,'BKK-20230922-01','19','2023-09-22',100000,'-',100000,100000,0,'2023-09-22 14:03:04',NULL),(19,'BKK-20231019-01','4','2023-10-19',150000,'tes',150000,150000,0,'2023-10-19 08:55:39',NULL);
/*!40000 ALTER TABLE `cash_out` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_out_details`
--

DROP TABLE IF EXISTS `cash_out_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cash_out_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cash_out` int(11) NOT NULL,
  `akun_biaya` varchar(50) DEFAULT NULL,
  `tgl_pelaksanaan` date DEFAULT NULL,
  `nominal` double DEFAULT NULL,
  `keperluan` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_out_details`
--

LOCK TABLES `cash_out_details` WRITE;
/*!40000 ALTER TABLE `cash_out_details` DISABLE KEYS */;
INSERT INTO `cash_out_details` VALUES (1,14,'1',NULL,50000,'tes','2023-07-27 10:11:20',NULL),(2,14,'3',NULL,50000,'tes2','2023-07-27 10:11:20',NULL),(3,15,'1',NULL,50000,'Tes keperluan 1','2023-07-27 11:41:53',NULL),(4,15,'3',NULL,35000,'Tes keperluan 2','2023-07-27 11:41:53',NULL),(5,15,'1',NULL,15000,'Tes keperluan 3','2023-07-27 11:41:53',NULL),(6,17,'98','2023-09-14',150000,'tes','2023-09-14 14:56:40',NULL),(7,17,'99','2023-09-15',50000,'tes2','2023-09-14 14:56:40',NULL),(8,18,'103','2023-09-22',50000,'Tes1','2023-09-22 14:03:04',NULL),(9,18,'102','2023-09-22',50000,'Tes2','2023-09-22 14:03:04',NULL),(10,19,'79','2023-10-19',100000,'listrik','2023-10-19 08:55:39',NULL),(11,19,'78','2023-10-19',50000,'telpon','2023-10-19 08:55:39',NULL);
/*!40000 ALTER TABLE `cash_out_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `as_parent` tinyint(1) NOT NULL DEFAULT '0',
  `parent` bigint(20) unsigned DEFAULT NULL,
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `user_created` bigint(20) unsigned NOT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_code_unique` (`code`),
  KEY `categories_user_created_foreign` (`user_created`),
  KEY `categories_user_updated_foreign` (`user_updated`),
  KEY `categories_parent_foreign` (`parent`),
  CONSTRAINT `categories_parent_foreign` FOREIGN KEY (`parent`) REFERENCES `categories` (`id`),
  CONSTRAINT `categories_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `categories_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'BO','BOOKING','',1,NULL,1,1,NULL,'2022-02-12 17:00:00',NULL),(2,'SO','SALES','',1,NULL,1,1,NULL,'2022-02-12 17:00:00',NULL),(3,'PO','PURCHASE','',1,NULL,1,1,NULL,'2022-02-12 17:00:00',NULL),(4,'FOOD_SALES','FOOD_SALES',NULL,1,2,1,1,1,'2022-02-12 17:00:00','2024-05-25 07:10:16'),(5,'DRINK_SALES','DRINK_SALES',NULL,1,2,1,1,1,'2022-02-12 17:00:00','2024-05-25 07:10:24'),(6,'OTHER_SALES','OTHER_SALES','',1,2,1,1,NULL,'2022-02-12 17:00:00',NULL),(7,'SERVICE_SALES','SERVICE_SALES','',1,2,1,1,NULL,'2022-02-12 17:00:00',NULL),(8,'BAHAN_PURCHASE','BAHAN_PURCHASE','',1,3,1,1,NULL,'2022-02-12 17:00:00',NULL),(9,'OTHER_PURCHASE','OTHER_PURCHASE','',1,3,1,1,NULL,'2022-02-12 17:00:00',NULL),(10,'SERVICE_PURCHASE','SERVICE_PURCHASE','-',0,2,1,1,NULL,'2022-03-12 17:00:00',NULL),(11,'KD-001','JUICE',NULL,0,2,1,1,1,'2024-03-18 04:12:14','2024-03-18 06:08:04');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chart_of_accounts`
--

DROP TABLE IF EXISTS `chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chart_of_accounts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `chart_of_account_id` bigint(20) DEFAULT NULL,
  `code_account_default` varchar(255) CHARACTER SET latin1 NOT NULL,
  `code_parent` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `code_account_alias` varchar(255) CHARACTER SET latin1 NOT NULL,
  `is_coa_alias` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `group_of_account` enum('aktiva','hutang','modal','pendapatan','harga_pokok_penjualan','biaya_operasional','biaya_dan_pendapatan_lainnya') CHARACTER SET latin1 NOT NULL,
  `type_of_account` enum('header','detail') CHARACTER SET latin1 NOT NULL,
  `type_of_business` enum('dagang','jasa','manufaktur','proyek') CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `user_created` bigint(20) NOT NULL,
  `user_updated` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chart_of_accounts`
--

LOCK TABLES `chart_of_accounts` WRITE;
/*!40000 ALTER TABLE `chart_of_accounts` DISABLE KEYS */;
INSERT INTO `chart_of_accounts` VALUES (1,0,'1.00.00.000.00','','1.00.00.000.00',0,'AKTIVA','aktiva','header','dagang','',1,0,NULL,NULL),(2,1,'1.01.00.000.00','1.00.00.000.00','1.01.00.000.00',0,'Aktiva Lancar','aktiva','header','dagang','',1,0,NULL,NULL),(3,2,'1.01.01.000.00','1.01.00.000.00','1.01.01.000.00',0,'Kas dan Setara Kas','aktiva','header','dagang','',1,0,NULL,NULL),(4,3,'1.01.01.001.00','1.01.01.000.00','1.01.01.001.00',0,'Kas','aktiva','header','dagang','',1,0,NULL,NULL),(5,4,'1.01.01.001.10','1.01.01.001.00','1.01.01.001.10',0,'Petty Cash','aktiva','detail','dagang','',1,0,NULL,NULL),(6,3,'1.01.01.002.00','1.01.01.000.00','1.01.01.002.00',0,'Bank','aktiva','header','dagang','',1,0,NULL,NULL),(7,6,'1.01.01.002.10','1.01.01.002.00','1.01.01.002.10',0,'Bank BCA','aktiva','detail','dagang','',1,0,NULL,NULL),(8,6,'1.01.01.002.20','1.01.01.002.00','1.01.01.002.20',0,'Bank Mandiri','aktiva','detail','dagang','',1,0,NULL,NULL),(9,3,'1.01.01.003.00','1.01.01.000.00','1.01.01.003.00',0,'Payment Gateway','aktiva','header','dagang','',1,0,NULL,NULL),(10,9,'1.01.01.003.10','1.01.01.003.00','1.01.01.003.10',0,'Ovo','aktiva','detail','dagang','',1,0,NULL,NULL),(11,9,'1.01.01.003.20','1.01.01.003.00','1.01.01.003.20',0,'Gopay','aktiva','detail','dagang','',1,0,NULL,NULL),(12,9,'1.01.01.003.30','1.01.01.003.00','1.01.01.003.30',0,'Shopee Pay','aktiva','detail','dagang','',1,0,NULL,NULL),(13,9,'1.01.01.003.40','1.01.01.003.00','1.01.01.003.40',0,'Dana','aktiva','detail','dagang','',1,0,NULL,NULL),(14,9,'1.01.01.003.50','1.01.01.003.00','1.01.01.003.50',0,'Link Aja','aktiva','detail','dagang','',1,0,NULL,NULL),(15,2,'1.01.02.000.00','1.01.00.000.00','1.01.02.000.00',0,'Piutang Usaha','aktiva','header','dagang','',1,0,NULL,NULL),(16,15,'1.01.02.001.00','1.01.02.000.00','1.01.02.001.00',0,'Piutang Usaha','aktiva','header','dagang','',1,0,NULL,NULL),(17,16,'1.01.02.001.10','1.01.02.001.00','1.01.02.001.10',0,'Piutang Usaha Cabang A','aktiva','detail','dagang','',1,0,NULL,NULL),(18,16,'1.01.02.001.20','1.01.02.001.00','1.01.02.001.20',0,'Piutang Usaha Cabang B','aktiva','detail','dagang','',1,0,NULL,NULL),(19,15,'1.01.02.002.00','1.01.02.000.00','1.01.02.002.00',0,'Piutang Lain-lain','aktiva','header','dagang','',1,0,NULL,NULL),(20,2,'1.01.03.000.00','1.01.00.000.00','1.01.03.000.00',0,'Persediaan','aktiva','header','dagang','',1,0,NULL,NULL),(21,37,'1.01.04.000.00','1.02.00.000.00','1.01.04.000.00',0,'Pajak Dibayar Dimuka','aktiva','header','dagang','',1,0,NULL,NULL),(22,21,'1.01.04.001.00','1.01.04.000.00','1.01.04.001.00',0,'PPN Masukan (Pembelian)','aktiva','header','dagang','',1,0,NULL,NULL),(23,21,'1.01.04.002.00','1.01.04.000.00','1.01.04.002.00',0,'PPh 21','aktiva','header','dagang','',1,0,NULL,NULL),(24,23,'1.01.04.002.10','1.01.04.002.00','1.01.04.002.10',0,'PPh Cabang A','aktiva','detail','dagang','',1,0,NULL,NULL),(25,23,'1.01.04.001.20','1.01.04.002.00','1.01.04.001.20',0,'PPh Cabang B','aktiva','detail','dagang','',1,0,NULL,NULL),(26,21,'1.01.04.003.00','1.01.04.000.00','1.01.04.003.00',0,'PPh 4 ayat 2','aktiva','header','dagang','',1,0,NULL,NULL),(27,21,'1.01.04.004.00','1.01.04.000.00','1.01.04.004.00',0,'PPh 25','aktiva','header','dagang','',1,0,NULL,NULL),(28,21,'1.01.04.005.00','1.01.04.000.00','1.01.04.005.00',0,'PPh 23','aktiva','header','dagang','',1,0,NULL,NULL),(29,2,'1.01.05.000.00','1.01.00.000.00','1.01.05.000.00',0,'Biaya Dibayar Dimuka','aktiva','header','dagang','',1,0,NULL,NULL),(30,29,'1.01.05.001.00','1.01.05.000.00','1.01.05.001.00',0,'Sewa Dibayar Dimuka','aktiva','detail','dagang','',1,0,NULL,NULL),(31,29,'1.01.05.002.00','1.01.05.000.00','1.01.05.002.00',0,'Iklan Dibayar Dimuka','aktiva','detail','dagang','',1,0,NULL,NULL),(32,29,'1.01.05.003.00','1.01.05.000.00','1.01.05.003.00',0,'Asuransi Dibayar Dimuka','aktiva','detail','dagang','',1,0,NULL,NULL),(33,29,'1.01.05.004.00','1.01.05.000.00','1.01.05.004.00',0,'Uang Muka Pembelian','aktiva','detail','dagang','',1,0,NULL,NULL),(34,2,'1.01.06.000.00','1.01.00.000.00','1.01.06.000.00',0,'Investasi Jangka Panjang','aktiva','header','dagang','',1,0,NULL,NULL),(35,34,'1.01.06.001.00','1.01.06.000.00','1.01.06.001.00',0,'Investasi Saham','aktiva','detail','dagang','',1,0,NULL,NULL),(36,34,'1.01.06.002.00','1.01.06.000.00','1.01.06.002.00',0,'Invetasi Obligasi','aktiva','detail','dagang','',1,0,NULL,NULL),(37,1,'1.02.00.000.00','1.00.00.000.00','1.02.00.000.00',0,'Aktiva Tetap','aktiva','header','dagang','',1,0,NULL,NULL),(38,37,'1.02.01.000.00','1.02.00.000.00','1.02.01.000.00',0,'Peralatan','aktiva','detail','dagang','',1,0,NULL,NULL),(39,37,'1.02.02.000.00','1.02.00.000.00','1.02.02.000.00',0,'Kendaraan','aktiva','detail','dagang','',1,0,NULL,NULL),(40,37,'1.02.03.000.00','1.02.00.000.00','1.02.03.000.00',0,'Gedung','aktiva','detail','dagang','',1,0,NULL,NULL),(41,37,'1.02.04.000.00','1.02.00.000.00','1.02.04.000.00',0,'Tanah','aktiva','detail','dagang','',1,0,NULL,NULL),(42,37,'1.02.05.000.00','1.02.00.000.00','1.02.05.000.00',0,'Akumulasi Penyusutan Peralatan','aktiva','detail','dagang','',1,0,NULL,NULL),(43,37,'1.02.06.000.00','1.02.00.000.00','1.02.06.000.00',0,'Akumulasi Penyusutan Kendaraan','aktiva','detail','dagang','',1,0,NULL,NULL),(44,37,'1.02.07.000.00','1.02.00.000.00','1.02.07.000.00',0,'Akumulasi Penyusutan Gedung','aktiva','detail','dagang','',1,0,NULL,NULL),(45,37,'1.02.08.000.00','1.02.00.000.00','1.02.08.000.00',0,'Akumulasi Penyusutan Tanah','aktiva','detail','dagang','',1,0,NULL,NULL),(46,1,'1.03.00.000.00','1.00.00.000.00','1.03.00.000.00',0,'Aktiva Tetap Tidak Berwujud','aktiva','header','dagang','',1,0,NULL,NULL),(47,46,'1.03.01.000.00','1.03.00.000.00','1.03.01.000.00',0,'Hak Paten','aktiva','detail','dagang','',1,0,NULL,NULL),(48,46,'1.03.02.000.00','1.03.00.000.00','1.03.02.000.00',0,'Hak Cipta','aktiva','detail','dagang','',1,0,NULL,NULL),(49,46,'1.03.03.000.00','1.03.00.000.00','1.03.03.000.00',0,'Merk Dagang','aktiva','detail','dagang','',1,0,NULL,NULL),(50,46,'1.03.04.000.00','1.03.00.000.00','1.03.04.000.00',0,'Goodwill','aktiva','detail','dagang','',1,0,NULL,NULL),(51,46,'1.03.05.000.00','1.03.00.000.00','1.03.05.000.00',0,'Franchise','aktiva','detail','dagang','',1,0,NULL,NULL),(52,1,'1.04.00.000.00','1.00.00.000.00','1.04.00.000.00',0,'Aktiva Lain-lain','aktiva','header','dagang','',1,0,NULL,NULL),(53,52,'1.04.01.000.00','1.04.00.000.00','1.04.01.000.00',0,'Mesin Yang Tidak Digunakan','aktiva','detail','dagang','',1,0,NULL,NULL),(54,52,'1.04.02.000.00','1.04.00.000.00','1.04.02.000.00',0,'Beban Yang Ditangguhkan','aktiva','detail','dagang','',1,0,NULL,NULL),(55,52,'1.04.03.000.00','1.04.00.000.00','1.04.03.000.00',0,'Piutang Kepada Pemegang Saham','aktiva','detail','dagang','',1,0,NULL,NULL),(56,52,'1.04.04.000.00','1.04.00.000.00','1.04.04.000.00',0,'Beban Emisi Saham','aktiva','detail','dagang','',1,0,NULL,NULL),(57,0,'2.00.00.000.00','','2.00.00.000.00',0,'KEWAJIBAN','hutang','header','dagang','',1,0,NULL,NULL),(58,57,'2.01.00.000.00','2.00.00.000.00','2.01.00.000.00',0,'Kewajiban Jangka Pendek','hutang','header','dagang','',1,0,NULL,NULL),(59,58,'2.01.01.000.00','2.01.00.000.00','2.01.01.000.00',0,'Hutang Usaha','hutang','header','dagang','',1,0,NULL,NULL),(60,59,'2.01.01.001.00','2.01.01.000.00','2.01.01.001.00',0,'Hutang Usaha Cabang A','hutang','detail','dagang','',1,0,NULL,NULL),(61,59,'2.01.01.002.00','2.01.01.000.00','2.01.01.002.00',0,'Hutang Usaha Cabang B','hutang','detail','dagang','',1,0,NULL,NULL),(62,58,'2.01.02.000.00','2.01.00.000.00','2.01.02.000.00',0,'Uang Muka Penjualan','hutang','header','dagang','',1,0,NULL,NULL),(63,58,'2.01.03.000.00','2.01.00.000.00','2.01.03.000.00',0,'Hutang Pajak','hutang','header','dagang','',1,0,NULL,NULL),(64,63,'2.01.03.001.00','2.01.03.000.00','2.01.03.001.00',0,'PPn Keluaran (Penjualan)','hutang','detail','dagang','',1,0,NULL,NULL),(65,63,'2.01.03.002.00','2.01.03.000.00','2.01.03.002.00',0,'PPnBM','hutang','detail','dagang','',1,0,NULL,NULL),(66,63,'2.01.03.003.00','2.01.03.000.00','2.01.03.003.00',0,'PPh 21','hutang','header','dagang','',1,0,NULL,NULL),(67,66,'2.01.03.003.10','2.01.03.003.00','2.01.03.003.10',0,'PPh 21 Cabang A','hutang','detail','dagang','',1,0,NULL,NULL),(68,66,'2.01.03.003.20','2.01.03.003.00','2.01.03.003.20',0,'PPh 21 Cabang B','hutang','detail','dagang','',1,0,NULL,NULL),(69,63,'2.01.03.004.00','2.01.03.000.00','2.01.03.004.00',0,'PPh 22','hutang','detail','dagang','',1,0,NULL,NULL),(70,63,'2.01.03.005.00','2.01.03.000.00','2.01.03.005.00',0,'PPh 23','hutang','detail','dagang','',1,0,NULL,NULL),(71,63,'2.01.03.006.00','2.01.03.000.00','2.01.03.006.00',0,'PPh 4 Ayat 2','hutang','detail','dagang','',1,0,NULL,NULL),(72,63,'2.01.03.007.00','2.01.03.000.00','2.01.03.007.00',0,'PPh 25','hutang','detail','dagang','',1,0,NULL,NULL),(73,63,'2.01.03.008.00','2.01.03.000.00','2.01.03.008.00',0,'PPh 29','hutang','detail','dagang','',1,0,NULL,NULL),(74,63,'2.01.03.009.00','2.01.03.000.00','2.01.03.009.00',0,'PBB','hutang','detail','dagang','',1,0,NULL,NULL),(75,58,'2.01.04.000.00','2.01.00.000.00','2.01.04.000.00',0,'Utang Wesel','hutang','header','dagang','',1,0,NULL,NULL),(76,58,'2.01.05.000.00','2.01.00.000.00','2.01.05.000.00',0,'Biaya yang masih harus dibayar','hutang','header','dagang','',1,0,NULL,NULL),(77,76,'2.01.05.001.00','2.01.05.000.00','2.01.05.001.00',0,'Biaya YMH Dibayar - Bunga Pinjaman','hutang','detail','dagang','',1,0,NULL,NULL),(78,76,'2.01.05.002.00','2.01.05.000.00','2.01.05.002.00',0,'Biaya YMH Dibayar - Telephone','hutang','detail','dagang','',1,0,NULL,NULL),(79,76,'2.01.05.003.00','2.01.05.000.00','2.01.05.003.00',0,'Biaya YMH Dibayar - Listrik','hutang','detail','dagang','',1,0,NULL,NULL),(80,76,'2.01.05.004.00','2.01.05.000.00','2.01.05.004.00',0,'Biaya YMH Dibayar - Sewa','hutang','detail','dagang','',1,0,NULL,NULL),(81,76,'2.01.05.005.00','2.01.05.000.00','2.01.05.005.00',0,'Biaya YMH Dibayar - Gaji Dan Upah','hutang','detail','dagang','',1,0,NULL,NULL),(82,76,'2.01.05.006.00','2.01.05.000.00','2.01.05.006.00',0,'Biaya YMH Dibayar - Asuransi','hutang','detail','dagang','',1,0,NULL,NULL),(83,58,'2.01.06.000.00','2.01.00.000.00','2.01.06.000.00',0,'Utang Gaji','hutang','header','dagang','',1,0,NULL,NULL),(84,58,'2.01.07.000.00','2.01.00.000.00','2.01.07.000.00',0,'Utang Sewa Gedung','hutang','header','dagang','',1,0,NULL,NULL),(85,57,'2.02.00.000.00','2.00.00.000.00','2.02.00.000.00',0,'Kewajiban Jangka Panjang','hutang','header','dagang','',1,0,NULL,NULL),(86,85,'2.02.01.000.00','2.02.00.000.00','2.02.01.000.00',0,'Hutang Hipotek','hutang','detail','dagang','',1,0,NULL,NULL),(87,85,'2.02.02.000.00','2.02.00.000.00','2.02.02.000.00',0,'Hutang Obligasi','hutang','detail','dagang','',1,0,NULL,NULL),(88,85,'2.02.03.000.00','2.02.00.000.00','2.02.03.000.00',0,'Hutang Gadai','hutang','detail','dagang','',1,0,NULL,NULL),(89,57,'2.03.00.000.00','2.00.00.000.00','2.03.00.000.00',0,'Kewajiban Lain-lain','hutang','header','dagang','',1,0,NULL,NULL),(90,0,'3.00.00.000.00','','3.00.00.000.00',0,'MODAL','modal','header','dagang','',1,0,NULL,NULL),(91,90,'3.01.00.000.00','3.00.00.000.00','3.01.00.000.00',0,'Modal','modal','header','dagang','',1,0,NULL,NULL),(92,91,'3.01.01.000.00','3.01.00.000.00','3.01.01.000.00',0,'Modal/Ekuitas Pemilik','modal','header','dagang','',1,0,NULL,NULL),(93,92,'3.01.01.001.00','3.01.01.000.00','3.01.01.001.00',0,'Modal Usaha Cabang A','modal','detail','dagang','',1,0,NULL,NULL),(94,92,'3.01.01.002.00','3.01.01.000.00','3.01.01.002.00',0,'Modal Usaha Cabang B','modal','detail','dagang','',1,0,NULL,NULL),(95,91,'3.01.02.000.00','3.01.00.000.00','3.01.02.000.00',0,'Prive','modal','header','dagang','',1,0,NULL,NULL),(96,90,'3.02.00.000.00','3.00.00.000.00','3.02.00.000.00',0,'Saldo Laba','modal','header','dagang','',1,0,NULL,NULL),(97,96,'3.02.01.000.00','3.02.00.000.00','3.02.01.000.00',0,'Saldo Laba Tahun Lalu','modal','detail','dagang','',1,0,NULL,NULL),(98,96,'3.02.02.000.00','3.02.00.000.00','3.02.02.000.00',0,'Koreksi Saldo Laba','modal','detail','dagang','',1,0,NULL,NULL),(99,96,'3.02.03.000.00','3.02.00.000.00','3.02.03.000.00',0,'Saldo Laba Tahun Berjalan','modal','detail','dagang','',1,0,NULL,NULL),(100,0,'4.00.00.000.00','','4.00.00.000.00',0,'PENDAPATAN','pendapatan','header','dagang','',1,0,NULL,NULL),(101,100,'4.01.00.000.00','4.00.00.000.00','4.01.00.000.00',0,'Pendapatan Usaha','pendapatan','header','dagang','',1,0,NULL,NULL),(102,100,'4.02.00.000.00','4.00.00.000.00','4.02.00.000.00',0,'Pendapatan Jasa','pendapatan','header','dagang','',1,0,NULL,NULL),(103,102,'4.02.01.000.00','4.02.00.000.00','4.02.01.000.00',0,'Pendapatan Jasa Cabang A','pendapatan','detail','dagang','',1,0,NULL,NULL),(104,102,'4.02.02.000.00','4.02.00.000.00','4.02.02.000.00',0,'Pendapatan Jasa Cabang B','pendapatan','detail','dagang','',1,0,NULL,NULL),(105,100,'4.03.00.000.00','4.00.00.000.00','4.03.00.000.00',0,'Pendapatan Diluar Usaha','pendapatan','header','dagang','',1,0,NULL,NULL),(106,100,'4.04.00.000.00','4.00.00.000.00','4.04.00.000.00',0,'Retur Penjualan','pendapatan','header','dagang','',1,0,NULL,NULL),(107,100,'4.05.00.000.00','4.00.00.000.00','4.05.00.000.00',0,'Diskon Penjualan','pendapatan','header','dagang','',1,0,NULL,NULL),(108,0,'5.00.00.000.00','','5.00.00.000.00',0,'HARGA POKOK PENJUALAN','harga_pokok_penjualan','header','dagang','',1,0,NULL,NULL),(109,108,'5.01.00.000.00','5.00.00.000.00','5.01.00.000.00',0,'Pembelian','harga_pokok_penjualan','header','dagang','',1,0,NULL,NULL),(110,109,'5.01.01.000.00','5.01.00.000.00','5.01.01.000.00',0,'Pembelian Cabang A','harga_pokok_penjualan','detail','dagang','',1,0,NULL,NULL),(111,109,'5.01.02.000.00','5.01.00.000.00','5.01.02.000.00',0,'Pembelian Cabang B','harga_pokok_penjualan','detail','dagang','',1,0,NULL,NULL),(112,108,'5.02.00.000.00','5.00.00.000.00','5.02.00.000.00',0,'Retur Pembelian','harga_pokok_penjualan','header','dagang','',1,0,NULL,NULL),(113,108,'5.03.00.000.00','5.00.00.000.00','5.03.00.000.00',0,'Diskon Pembelian','harga_pokok_penjualan','header','dagang','',1,0,NULL,NULL),(114,0,'6.00.00.000.00','','6.00.00.000.00',0,'BIAYA OPERASIONAL','biaya_operasional','header','dagang','',1,0,NULL,NULL),(115,114,'6.01.00.000.00','6.00.00.000.00','6.01.00.000.00',0,'Biaya Gaji','biaya_operasional','header','dagang','',1,0,NULL,NULL),(116,115,'6.01.01.000.00','6.01.00.000.00','6.01.01.000.00',0,'Biaya Gaji Cabang A','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(117,115,'6.01.02.000.00','6.01.00.000.00','6.01.02.000.00',0,'Biaya Gaji Cabang B','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(118,114,'6.02.00.000.00','6.00.00.000.00','6.02.00.000.00',0,'Biaya Pemasaran','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(119,114,'6.03.00.000.00','6.00.00.000.00','6.03.00.000.00',0,'Biaya Sewa','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(120,114,'6.04.00.000.00','6.00.00.000.00','6.04.00.000.00',0,'Biaya Perlengkapan Kantor','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(121,114,'6.05.00.000.00','6.00.00.000.00','6.05.00.000.00',0,'Biaya Bunga Bank','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(122,114,'6.06.00.000.00','6.00.00.000.00','6.06.00.000.00',0,'Biaya Keamanan','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(123,114,'6.07.00.000.00','6.00.00.000.00','6.07.00.000.00',0,'Biaya Materai','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(124,114,'6.08.00.000.00','6.00.00.000.00','6.08.00.000.00',0,'Biaya Piutang Tidak Tertagih','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(125,114,'6.09.00.000.00','6.00.00.000.00','6.09.00.000.00',0,'Biaya Entertain','biaya_operasional','detail','dagang','',1,0,NULL,NULL),(126,0,'7.00.00.000.00','','7.00.00.000.00',0,'BIAYA DAN PENDAPATAN LAINNYA','biaya_dan_pendapatan_lainnya','header','dagang','',1,0,NULL,NULL),(127,126,'7.01.00.000.00','7.00.00.000.00','7.01.00.000.00',0,'Biaya Lainnya','biaya_dan_pendapatan_lainnya','header','dagang','',1,0,NULL,NULL),(128,127,'7.01.01.000.00','7.01.00.000.00','7.01.01.000.00',0,'Biaya Pajak Jasa Giro','biaya_dan_pendapatan_lainnya','detail','dagang','',1,0,NULL,NULL),(129,127,'7.01.02.000.00','7.01.00.000.00','7.01.02.000.00',0,'Biaya Selisih Kurs','biaya_dan_pendapatan_lainnya','detail','dagang','',1,0,NULL,NULL),(130,127,'7.01.03.000.00','7.01.00.000.00','7.01.03.000.00',0,'Biaya Lain-lain','biaya_dan_pendapatan_lainnya','detail','dagang','',1,0,NULL,NULL),(131,127,'7.01.04.000.00','7.01.00.000.00','7.01.04.000.00',0,'Biaya Penyesuaian Persediaan Barang','biaya_dan_pendapatan_lainnya','detail','dagang','',1,0,NULL,NULL),(132,127,'7.01.05.000.00','7.01.00.000.00','7.01.05.000.00',0,'Biaya penyusutan','biaya_dan_pendapatan_lainnya','detail','dagang','',1,0,NULL,NULL),(133,126,'7.02.00.000.00','7.00.00.000.00','7.02.00.000.00',0,'Pendapatan Lainnya','biaya_dan_pendapatan_lainnya','header','dagang','',1,0,NULL,NULL),(134,133,'7.02.01.000.00','7.02.00.000.00','7.02.01.000.00',0,'Pendapatan Bunga Deposito','biaya_dan_pendapatan_lainnya','detail','dagang','',1,0,NULL,NULL),(135,133,'7.02.02.000.00','7.02.00.000.00','7.02.02.000.00',0,'Pendapatan Selisih Kurs','biaya_dan_pendapatan_lainnya','detail','dagang','',1,0,NULL,NULL),(136,133,'7.02.03.000.00','7.02.00.000.00','7.02.03.000.00',0,'Pendapatan Lain Hutang Tidak Terbayar','biaya_dan_pendapatan_lainnya','detail','dagang','',1,0,NULL,NULL),(137,133,'7.02.04.000.00','7.02.00.000.00','7.02.04.000.00',0,'Pendapatan Pengiriman dan Pengangkutan','biaya_dan_pendapatan_lainnya','detail','dagang','',1,0,NULL,NULL);
/*!40000 ALTER TABLE `chart_of_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address1` longtext COLLATE utf8_unicode_ci,
  `address2` longtext COLLATE utf8_unicode_ci,
  `address3` longtext COLLATE utf8_unicode_ci,
  `phone` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `instagram` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_created` bigint(20) unsigned NOT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `companies_user_created_foreign` (`user_created`),
  KEY `companies_user_updated_foreign` (`user_updated`),
  CONSTRAINT `companies_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `companies_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'Hendik Endtato Edison','Toko 1','Jl. Raya aimas-klamono',NULL,NULL,NULL,'6282238205636','demo@gmail.com',NULL,NULL,NULL,NULL,'-',1,1,'2023-05-23 02:47:27','2024-06-05 06:08:05');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configurations`
--

DROP TABLE IF EXISTS `configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configurations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `total_cart` int(11) NOT NULL,
  `change_authorization` tinyint(1) NOT NULL DEFAULT '0',
  `set_inventory` tinyint(1) NOT NULL DEFAULT '0',
  `set_edit_authorization` tinyint(1) NOT NULL DEFAULT '0',
  `print_footer1` longtext COLLATE utf8_unicode_ci,
  `print_footer2` longtext COLLATE utf8_unicode_ci,
  `print_footer3` longtext COLLATE utf8_unicode_ci,
  `user_created` bigint(20) unsigned NOT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `configurations_user_created_foreign` (`user_created`),
  KEY `configurations_user_updated_foreign` (`user_updated`),
  CONSTRAINT `configurations_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `configurations_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configurations`
--

LOCK TABLES `configurations` WRITE;
/*!40000 ALTER TABLE `configurations` DISABLE KEYS */;
INSERT INTO `configurations` VALUES (1,15,0,0,0,'*****Terima Kasih*****',NULL,NULL,1,1,'2023-05-23 02:47:27','2024-06-05 06:08:05');
/*!40000 ALTER TABLE `configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_bahan_baku`
--

DROP TABLE IF EXISTS `item_bahan_baku`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_bahan_baku` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `kd_item_bahan_baku` varchar(100) DEFAULT NULL,
  `nm_item_bahan_baku` text,
  `kd_satuan` varchar(100) DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_bahan_baku`
--

LOCK TABLES `item_bahan_baku` WRITE;
/*!40000 ALTER TABLE `item_bahan_baku` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_bahan_baku` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `big_quantity` int(11) DEFAULT NULL,
  `small_quantity` int(11) NOT NULL,
  `big_unit_id` bigint(20) unsigned DEFAULT NULL,
  `small_unit_id` bigint(20) unsigned NOT NULL,
  `buy_price` decimal(18,0) NOT NULL DEFAULT '0',
  `sell_price` decimal(18,0) NOT NULL DEFAULT '0',
  `profit` decimal(18,0) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `user_created` bigint(20) unsigned NOT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `items_code_unique` (`code`),
  KEY `items_user_created_foreign` (`user_created`),
  KEY `items_user_updated_foreign` (`user_updated`),
  KEY `items_category_id_foreign` (`category_id`),
  KEY `items_big_unit_id_foreign` (`big_unit_id`),
  KEY `items_small_unit_id_foreign` (`small_unit_id`),
  CONSTRAINT `items_big_unit_id_foreign` FOREIGN KEY (`big_unit_id`) REFERENCES `units` (`id`),
  CONSTRAINT `items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `items_small_unit_id_foreign` FOREIGN KEY (`small_unit_id`) REFERENCES `units` (`id`),
  CONSTRAINT `items_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `items_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'Mie Goreng','A001',4,NULL,1,NULL,1,45000,50000,0,1,0,NULL,1,NULL,'2023-05-23 03:00:27',NULL),(2,'NASI GORENG SEAFOOD','A002',4,NULL,1,NULL,1,20000,45000,0,1,0,'SPECIAL PAKAI TELOR',1,NULL,'2023-09-12 02:15:56',NULL),(3,'ES PELANGI','A003',5,NULL,1,NULL,1,15000,20000,0,1,0,'Varian vanila, pandan, dan coconut',1,NULL,'2023-09-12 02:18:10',NULL),(4,'ES MOCI','A004',5,NULL,1,NULL,1,13000,17000,0,1,0,NULL,1,NULL,'2023-09-12 02:19:08',NULL),(5,'Es Kopi','A005',5,NULL,1,NULL,1,30000,35000,0,1,0,NULL,1,1,'2023-09-12 02:21:34','2023-09-12 02:22:22'),(6,'ES JUICE','A006',5,NULL,1,NULL,1,14000,20000,0,1,0,NULL,1,NULL,'2023-09-12 02:23:03',NULL),(7,'SATE AYAM','A007',4,NULL,1,NULL,1,21000,40000,0,1,0,NULL,1,NULL,'2023-09-12 02:36:13',NULL),(8,'AYAM BUMBU KUNING','A008',4,NULL,1,NULL,1,19000,50000,0,1,0,NULL,1,NULL,'2023-09-12 02:37:19',NULL),(9,'Roti Bakar','A009',4,NULL,1,NULL,1,10000,15000,0,1,0,'Roti bakar enak',1,1,'2023-09-12 02:57:55','2023-09-12 03:36:46'),(11,'Tepung','A010',8,NULL,1,NULL,1,0,0,0,1,0,NULL,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_apps`
--

DROP TABLE IF EXISTS `logs_apps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_apps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'ex: merubah data PR20220101-0002 / membuat data PR20210101-003',
  `ip` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_apps_user_id_foreign` (`user_id`),
  CONSTRAINT `logs_apps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9583 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_apps`
--

LOCK TABLES `logs_apps` WRITE;
/*!40000 ALTER TABLE `logs_apps` DISABLE KEYS */;
INSERT INTO `logs_apps` VALUES (8678,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store/update','127.0.0.1','2024-05-31 02:50:20',NULL),(8679,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 02:50:21',NULL),(8680,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 02:50:23',NULL),(8681,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 02:50:32',NULL),(8682,1,'kode transaksi :  access url: http://127.0.0.1:8002/salesReport/pendapatan','127.0.0.1','2024-05-31 02:50:33',NULL),(8683,1,'kode transaksi :  access url: http://127.0.0.1:8002/salesReport/pendapatan','127.0.0.1','2024-05-31 02:50:40',NULL),(8684,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting','127.0.0.1','2024-05-31 02:51:18',NULL),(8685,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting/edit/3','127.0.0.1','2024-05-31 02:51:33',NULL),(8686,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting','127.0.0.1','2024-05-31 02:53:33',NULL),(8687,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting/create','127.0.0.1','2024-05-31 02:53:35',NULL),(8688,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting/create','127.0.0.1','2024-05-31 02:56:38',NULL),(8689,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting/create','127.0.0.1','2024-05-31 02:59:38',NULL),(8690,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting','127.0.0.1','2024-05-31 03:00:07',NULL),(8691,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting/create','127.0.0.1','2024-05-31 03:02:06',NULL),(8692,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting/store','127.0.0.1','2024-05-31 03:02:15',NULL),(8693,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting','127.0.0.1','2024-05-31 03:02:15',NULL),(8694,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 03:02:38',NULL),(8695,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 03:02:53',NULL),(8696,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/profitsetting','127.0.0.1','2024-05-31 03:03:21',NULL),(8697,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 03:03:24',NULL),(8698,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item','127.0.0.1','2024-05-31 03:03:45',NULL),(8699,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/datatable','127.0.0.1','2024-05-31 03:03:46',NULL),(8700,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item','127.0.0.1','2024-05-31 03:03:48',NULL),(8701,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/datatable','127.0.0.1','2024-05-31 03:03:49',NULL),(8702,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/datatable','127.0.0.1','2024-05-31 03:03:54',NULL),(8703,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/create','127.0.0.1','2024-05-31 03:04:34',NULL),(8704,1,'kode transaksi :  access url: http://192.168.1.157:8002','192.168.1.207','2024-05-31 03:36:39',NULL),(8705,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboard','192.168.1.207','2024-05-31 03:36:39',NULL),(8706,1,'kode transaksi :  access url: http://192.168.1.157:8002/gambar/user/1','192.168.1.207','2024-05-31 03:36:40',NULL),(8707,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboardAjax','192.168.1.207','2024-05-31 03:36:41',NULL),(8708,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking','192.168.1.207','2024-05-31 03:38:29',NULL),(8709,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking/datatable','192.168.1.207','2024-05-31 03:38:30',NULL),(8710,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking','192.168.1.207','2024-05-31 03:38:35',NULL),(8711,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking/datatable','192.168.1.207','2024-05-31 03:38:37',NULL),(8712,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.207','2024-05-31 03:38:43',NULL),(8713,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 03:38:46',NULL),(8714,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/items/8','192.168.1.207','2024-05-31 03:38:46',NULL),(8715,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/items/4','192.168.1.207','2024-05-31 03:38:47',NULL),(8716,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/items/2','192.168.1.207','2024-05-31 03:38:47',NULL),(8717,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 03:38:49',NULL),(8718,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/items/7','192.168.1.207','2024-05-31 03:38:49',NULL),(8719,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 03:38:51',NULL),(8720,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/items/6','192.168.1.207','2024-05-31 03:38:51',NULL),(8721,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/datatable','127.0.0.1','2024-05-31 03:39:13',NULL),(8722,1,'kode transaksi : SO240531-0001 access url: http://192.168.1.157:8002/sales/store/bayar','192.168.1.207','2024-05-31 03:39:17',NULL),(8723,1,'kode transaksi : SO240531-0001 access url: http://192.168.1.157:8002/sales/print/SO240531-0001','192.168.1.207','2024-05-31 03:39:17',NULL),(8724,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/configurations/1','192.168.1.207','2024-05-31 03:39:17',NULL),(8725,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 03:39:46',NULL),(8726,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 03:41:24',NULL),(8727,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 03:41:33',NULL),(8728,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/5','127.0.0.1','2024-05-31 03:41:35',NULL),(8729,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/storeCart','127.0.0.1','2024-05-31 03:41:35',NULL),(8730,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getItem','127.0.0.1','2024-05-31 03:41:45',NULL),(8731,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getItem','127.0.0.1','2024-05-31 03:41:49',NULL),(8732,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getItem','127.0.0.1','2024-05-31 03:42:33',NULL),(8733,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 03:42:40',NULL),(8734,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 03:51:36',NULL),(8735,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 03:51:39',NULL),(8736,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 03:51:59',NULL),(8737,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 03:52:01',NULL),(8738,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.207','2024-05-31 03:56:18',NULL),(8739,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getItem','192.168.1.207','2024-05-31 03:56:25',NULL),(8740,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getItem','192.168.1.207','2024-05-31 03:56:26',NULL),(8741,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/perusahaan','192.168.1.207','2024-05-31 03:57:07',NULL),(8742,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/perusahaan/store','192.168.1.207','2024-05-31 03:57:17',NULL),(8743,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.207','2024-05-31 03:57:23',NULL),(8744,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 03:57:27',NULL),(8745,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/items/5','192.168.1.207','2024-05-31 03:57:27',NULL),(8746,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 03:57:28',NULL),(8747,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 03:57:30',NULL),(8748,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 03:57:49',NULL),(8749,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 03:57:52',NULL),(8750,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase','192.168.1.207','2024-05-31 04:04:08',NULL),(8751,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/datatable','192.168.1.207','2024-05-31 04:04:09',NULL),(8752,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/datatable','192.168.1.207','2024-05-31 04:04:17',NULL),(8753,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/create','192.168.1.207','2024-05-31 04:06:54',NULL),(8754,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/by_tipe','192.168.1.207','2024-05-31 04:11:50',NULL),(8755,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/by_tipe','192.168.1.207','2024-05-31 04:11:57',NULL),(8756,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/by_tipe','192.168.1.207','2024-05-31 04:12:01',NULL),(8757,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/by_tipe','192.168.1.207','2024-05-31 04:12:06',NULL),(8758,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/by_tipe','192.168.1.207','2024-05-31 04:12:11',NULL),(8759,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/by_tipe','192.168.1.207','2024-05-31 04:12:32',NULL),(8760,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/by_tipe','192.168.1.207','2024-05-31 04:12:37',NULL),(8761,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item','192.168.1.207','2024-05-31 04:13:30',NULL),(8762,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/datatable','192.168.1.207','2024-05-31 04:13:31',NULL),(8763,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/create','192.168.1.207','2024-05-31 04:13:35',NULL),(8764,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase','192.168.1.207','2024-05-31 04:13:51',NULL),(8765,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/datatable','192.168.1.207','2024-05-31 04:13:52',NULL),(8766,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/create','192.168.1.207','2024-05-31 04:13:55',NULL),(8767,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/by_tipe','192.168.1.207','2024-05-31 04:13:58',NULL),(8768,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/satuan','192.168.1.207','2024-05-31 04:14:52',NULL),(8769,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/satuan/datatable','192.168.1.207','2024-05-31 04:14:54',NULL),(8770,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/satuan','192.168.1.207','2024-05-31 04:14:59',NULL),(8771,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/satuan/datatable','192.168.1.207','2024-05-31 04:15:00',NULL),(8772,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/satuan/create','192.168.1.207','2024-05-31 04:15:04',NULL),(8773,1,'kode transaksi : PCS access url: http://192.168.1.157:8002/dataInduk/satuan/store','192.168.1.207','2024-05-31 04:15:25',NULL),(8774,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/satuan/datatable','192.168.1.207','2024-05-31 04:15:25',NULL),(8775,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/satuan/datatable','192.168.1.207','2024-05-31 04:15:57',NULL),(8776,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/by_tipe','192.168.1.207','2024-05-31 04:17:46',NULL),(8777,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/details','192.168.1.207','2024-05-31 04:17:48',NULL),(8778,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/create','192.168.1.207','2024-05-31 04:18:24',NULL),(8779,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/details','192.168.1.207','2024-05-31 04:18:41',NULL),(8780,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 04:18:53',NULL),(8781,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 04:18:54',NULL),(8782,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 04:18:57',NULL),(8783,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 04:19:08',NULL),(8784,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 04:19:09',NULL),(8785,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 04:19:24',NULL),(8786,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 04:19:28',NULL),(8787,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/store','192.168.1.207','2024-05-31 04:20:01',NULL),(8788,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase','192.168.1.207','2024-05-31 04:20:04',NULL),(8789,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/datatable','192.168.1.207','2024-05-31 04:20:05',NULL),(8790,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/create','192.168.1.207','2024-05-31 04:20:16',NULL),(8791,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 04:21:30',NULL),(8792,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 04:21:50',NULL),(8793,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 04:22:08',NULL),(8794,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/datatable','192.168.1.207','2024-05-31 04:22:13',NULL),(8795,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 04:22:13',NULL),(8796,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 04:22:15',NULL),(8797,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 04:22:17',NULL),(8798,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/detail/3','192.168.1.207','2024-05-31 04:22:20',NULL),(8799,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/detail/2','192.168.1.207','2024-05-31 04:22:27',NULL),(8800,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 04:22:54',NULL),(8801,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 04:31:22',NULL),(8802,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store','127.0.0.1','2024-05-31 04:31:59',NULL),(8803,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 04:32:05',NULL),(8804,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 04:32:07',NULL),(8805,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 04:32:20',NULL),(8806,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store','127.0.0.1','2024-05-31 04:32:46',NULL),(8807,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.207','2024-05-31 06:04:36',NULL),(8808,1,'kode transaksi :  access url: http://192.168.1.157:8002/keanggotaan','192.168.1.207','2024-05-31 06:04:52',NULL),(8809,1,'kode transaksi :  access url: http://192.168.1.157:8002/keanggotaan/datatable','192.168.1.207','2024-05-31 06:04:53',NULL),(8810,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking','192.168.1.207','2024-05-31 06:04:58',NULL),(8811,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking/datatable','192.168.1.207','2024-05-31 06:04:59',NULL),(8812,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking','192.168.1.207','2024-05-31 06:05:04',NULL),(8813,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking/datatable','192.168.1.207','2024-05-31 06:05:05',NULL),(8814,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 06:06:47',NULL),(8815,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 06:06:49',NULL),(8816,1,'kode transaksi :  access url: http://127.0.0.1:8002/booking','127.0.0.1','2024-05-31 06:06:53',NULL),(8817,1,'kode transaksi :  access url: http://127.0.0.1:8002/booking/datatable','127.0.0.1','2024-05-31 06:06:55',NULL),(8818,1,'kode transaksi :  access url: http://127.0.0.1:8002/booking/create','127.0.0.1','2024-05-31 06:06:57',NULL),(8819,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking/create','192.168.1.207','2024-05-31 06:13:22',NULL),(8820,1,'kode transaksi :  access url: http://192.168.1.157:8002/booking/datatable','192.168.1.207','2024-05-31 06:13:48',NULL),(8821,1,'kode transaksi :  access url: http://127.0.0.1:8002/booking/create','127.0.0.1','2024-05-31 06:14:41',NULL),(8822,1,'kode transaksi :  access url: http://192.168.1.157:8002','192.168.1.207','2024-05-31 06:14:44',NULL),(8823,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboard','192.168.1.207','2024-05-31 06:14:44',NULL),(8824,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboardAjax','192.168.1.207','2024-05-31 06:14:45',NULL),(8825,1,'kode transaksi :  access url: http://127.0.0.1:8002/keanggotaan','127.0.0.1','2024-05-31 06:14:46',NULL),(8826,1,'kode transaksi :  access url: http://127.0.0.1:8002/keanggotaan/datatable','127.0.0.1','2024-05-31 06:14:47',NULL),(8827,1,'kode transaksi :  access url: http://127.0.0.1:8002/keanggotaan','127.0.0.1','2024-05-31 06:14:52',NULL),(8828,1,'kode transaksi :  access url: http://127.0.0.1:8002/keanggotaan','127.0.0.1','2024-05-31 06:14:52',NULL),(8829,1,'kode transaksi :  access url: http://127.0.0.1:8002/keanggotaan','127.0.0.1','2024-05-31 06:14:52',NULL),(8830,1,'kode transaksi :  access url: http://127.0.0.1:8002/keanggotaan','127.0.0.1','2024-05-31 06:14:53',NULL),(8831,1,'kode transaksi :  access url: http://127.0.0.1:8002/keanggotaan/datatable','127.0.0.1','2024-05-31 06:14:54',NULL),(8832,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase/create','192.168.1.207','2024-05-31 06:16:19',NULL),(8833,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.207','2024-05-31 06:16:32',NULL),(8834,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 06:16:38',NULL),(8835,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 06:16:39',NULL),(8836,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:16:42',NULL),(8837,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:17:13',NULL),(8838,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:17:41',NULL),(8839,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:25:16',NULL),(8840,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:25:29',NULL),(8841,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:29:33',NULL),(8842,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 06:29:37',NULL),(8843,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 06:29:49',NULL),(8844,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:32:09',NULL),(8845,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:33:46',NULL),(8846,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:34:06',NULL),(8847,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:35:13',NULL),(8848,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 06:35:16',NULL),(8849,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 06:35:23',NULL),(8850,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:36:46',NULL),(8851,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 06:36:53',NULL),(8852,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:37:06',NULL),(8853,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:37:17',NULL),(8854,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:37:36',NULL),(8855,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:38:51',NULL),(8856,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 06:38:54',NULL),(8857,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:39:11',NULL),(8858,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 06:39:14',NULL),(8859,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 06:47:29',NULL),(8860,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:47:47',NULL),(8861,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 06:47:49',NULL),(8862,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:49:06',NULL),(8863,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:53:05',NULL),(8864,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store','127.0.0.1','2024-05-31 06:53:40',NULL),(8865,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:55:15',NULL),(8866,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store','127.0.0.1','2024-05-31 06:55:35',NULL),(8867,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 06:57:08',NULL),(8868,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 06:57:10',NULL),(8869,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 06:57:57',NULL),(8870,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 06:57:58',NULL),(8871,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 06:58:01',NULL),(8872,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 06:58:17',NULL),(8873,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 06:58:20',NULL),(8874,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:06:55',NULL),(8875,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 07:07:02',NULL),(8876,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:07:03',NULL),(8877,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:07:06',NULL),(8878,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:07:35',NULL),(8879,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 07:07:40',NULL),(8880,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:07:42',NULL),(8881,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:07:54',NULL),(8882,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 07:07:56',NULL),(8883,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:07:57',NULL),(8884,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:08:11',NULL),(8885,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:10:41',NULL),(8886,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 07:10:44',NULL),(8887,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:10:45',NULL),(8888,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:10:59',NULL),(8889,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 07:11:03',NULL),(8890,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:11:04',NULL),(8891,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:11:10',NULL),(8892,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 07:11:12',NULL),(8893,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:11:13',NULL),(8894,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:11:17',NULL),(8895,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:16:00',NULL),(8896,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 07:16:04',NULL),(8897,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:16:06',NULL),(8898,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store','127.0.0.1','2024-05-31 07:16:59',NULL),(8899,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 07:17:00',NULL),(8900,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 07:17:02',NULL),(8901,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/1','127.0.0.1','2024-05-31 07:17:06',NULL),(8902,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/1','127.0.0.1','2024-05-31 07:17:10',NULL),(8903,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/1','127.0.0.1','2024-05-31 07:20:35',NULL),(8904,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 07:21:52',NULL),(8905,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:21:53',NULL),(8906,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 07:21:58',NULL),(8907,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:21:59',NULL),(8908,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store','127.0.0.1','2024-05-31 07:22:41',NULL),(8909,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:25:10',NULL),(8910,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 07:25:17',NULL),(8911,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 07:25:21',NULL),(8912,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/details','127.0.0.1','2024-05-31 07:25:22',NULL),(8913,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store','127.0.0.1','2024-05-31 07:26:01',NULL),(8914,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 07:26:03',NULL),(8915,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 07:26:05',NULL),(8916,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/2','127.0.0.1','2024-05-31 07:26:14',NULL),(8917,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 07:26:57',NULL),(8918,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 07:26:58',NULL),(8919,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/2','127.0.0.1','2024-05-31 07:27:00',NULL),(8920,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 07:27:42',NULL),(8921,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 07:27:43',NULL),(8922,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/2','127.0.0.1','2024-05-31 07:27:45',NULL),(8923,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/1','127.0.0.1','2024-05-31 07:27:47',NULL),(8924,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 07:29:00',NULL),(8925,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 07:29:01',NULL),(8926,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/2','127.0.0.1','2024-05-31 07:29:09',NULL),(8927,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/1','127.0.0.1','2024-05-31 07:29:11',NULL),(8928,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/2','127.0.0.1','2024-05-31 07:34:09',NULL),(8929,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/2','127.0.0.1','2024-05-31 08:05:36',NULL),(8930,1,'kode transaksi :  access url: http://127.0.0.1:8002/gambar/user/1','127.0.0.1','2024-05-31 08:05:37',NULL),(8931,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 08:05:41',NULL),(8932,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 08:05:43',NULL),(8933,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/create','127.0.0.1','2024-05-31 08:05:45',NULL),(8934,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 08:05:48',NULL),(8935,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/2','127.0.0.1','2024-05-31 08:06:48',NULL),(8936,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/2','127.0.0.1','2024-05-31 08:08:50',NULL),(8937,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/2','127.0.0.1','2024-05-31 08:09:18',NULL),(8938,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/2','127.0.0.1','2024-05-31 08:11:16',NULL),(8939,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/2','127.0.0.1','2024-05-31 08:13:24',NULL),(8940,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/2','127.0.0.1','2024-05-31 08:13:50',NULL),(8941,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store/update','127.0.0.1','2024-05-31 08:14:16',NULL),(8942,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 08:14:17',NULL),(8943,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 08:14:19',NULL),(8944,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 08:15:15',NULL),(8945,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 08:15:16',NULL),(8946,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/2','127.0.0.1','2024-05-31 08:15:18',NULL),(8947,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/update/2','127.0.0.1','2024-05-31 08:15:21',NULL),(8948,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/item/by_tipe','127.0.0.1','2024-05-31 08:15:25',NULL),(8949,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/store/update','127.0.0.1','2024-05-31 08:15:39',NULL),(8950,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase','127.0.0.1','2024-05-31 08:15:41',NULL),(8951,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/datatable','127.0.0.1','2024-05-31 08:15:43',NULL),(8952,1,'kode transaksi :  access url: http://127.0.0.1:8002/purchase/detail/2','127.0.0.1','2024-05-31 08:15:46',NULL),(8953,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan','127.0.0.1','2024-05-31 08:17:37',NULL),(8954,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan/datatable','127.0.0.1','2024-05-31 08:17:39',NULL),(8955,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan','127.0.0.1','2024-05-31 08:17:43',NULL),(8956,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan','127.0.0.1','2024-05-31 08:17:44',NULL),(8957,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan','127.0.0.1','2024-05-31 08:17:44',NULL),(8958,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan','127.0.0.1','2024-05-31 08:17:44',NULL),(8959,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan/datatable','127.0.0.1','2024-05-31 08:17:45',NULL),(8960,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan/datatable','127.0.0.1','2024-05-31 08:20:48',NULL),(8961,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan','127.0.0.1','2024-05-31 08:22:11',NULL),(8962,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan/datatable','127.0.0.1','2024-05-31 08:22:13',NULL),(8963,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan/disable','127.0.0.1','2024-05-31 08:22:19',NULL),(8964,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan/datatable','127.0.0.1','2024-05-31 08:22:19',NULL),(8965,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan/disable','127.0.0.1','2024-05-31 08:22:25',NULL),(8966,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan/datatable','127.0.0.1','2024-05-31 08:22:25',NULL),(8967,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan','127.0.0.1','2024-05-31 08:22:28',NULL),(8968,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/satuan/datatable','127.0.0.1','2024-05-31 08:22:30',NULL),(8969,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-05-31 08:45:23',NULL),(8970,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-05-31 08:45:32',NULL),(8971,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-05-31 08:45:33',NULL),(8972,1,'kode transaksi : SO240530-0001 access url: http://127.0.0.1:8002/sales/print/SO240530-0001','127.0.0.1','2024-05-31 08:45:52',NULL),(8973,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/configurations/1','127.0.0.1','2024-05-31 08:45:53',NULL),(8974,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 08:53:19',NULL),(8975,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchaseReport','192.168.1.207','2024-05-31 09:02:59',NULL),(8976,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchaseReport','192.168.1.207','2024-05-31 09:03:04',NULL),(8977,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchaseReport','192.168.1.207','2024-05-31 09:03:15',NULL),(8978,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchaseReport','192.168.1.207','2024-05-31 09:03:26',NULL),(8979,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchaseReport','192.168.1.207','2024-05-31 09:03:32',NULL),(8980,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchaseReport','192.168.1.207','2024-05-31 09:03:33',NULL),(8981,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchaseReport','192.168.1.207','2024-05-31 09:03:42',NULL),(8982,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchaseReport','192.168.1.207','2024-05-31 09:03:45',NULL),(8983,1,'kode transaksi :  access url: http://192.168.1.157:8002/salesReport','192.168.1.207','2024-05-31 09:04:14',NULL),(8984,1,'kode transaksi :  access url: http://192.168.1.157:8002/salesReport','192.168.1.207','2024-05-31 09:04:17',NULL),(8985,1,'kode transaksi :  access url: http://192.168.1.157:8002/salesReport','192.168.1.207','2024-05-31 09:04:23',NULL),(8986,1,'kode transaksi :  access url: http://192.168.1.157:8002/kasirReport','192.168.1.207','2024-05-31 09:04:41',NULL),(8987,1,'kode transaksi :  access url: http://192.168.1.157:8002/kasirReport','192.168.1.207','2024-05-31 09:04:53',NULL),(8988,1,'kode transaksi :  access url: http://192.168.1.157:8002/kasirReport','192.168.1.207','2024-05-31 09:04:53',NULL),(8989,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:10:15',NULL),(8990,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:10:17',NULL),(8991,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/8','127.0.0.1','2024-05-31 09:10:17',NULL),(8992,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:10:36',NULL),(8993,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:10:41',NULL),(8994,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:10:42',NULL),(8995,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:10:42',NULL),(8996,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:10:43',NULL),(8997,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/4','127.0.0.1','2024-05-31 09:10:43',NULL),(8998,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/2','127.0.0.1','2024-05-31 09:10:43',NULL),(8999,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:11:32',NULL),(9000,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:11:35',NULL),(9001,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:11:35',NULL),(9002,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:11:35',NULL),(9003,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:11:35',NULL),(9004,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:11:50',NULL),(9005,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:11:50',NULL),(9006,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:11:51',NULL),(9007,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:11:51',NULL),(9008,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:11:52',NULL),(9009,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:12:05',NULL),(9010,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:16:32',NULL),(9011,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:16:36',NULL),(9012,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:16:36',NULL),(9013,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:16:36',NULL),(9014,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:16:36',NULL),(9015,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:16:59',NULL),(9016,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:16:59',NULL),(9017,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:17:00',NULL),(9018,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:17:00',NULL),(9019,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:17:02',NULL),(9020,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:17:16',NULL),(9021,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:17:19',NULL),(9022,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:17:19',NULL),(9023,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:17:19',NULL),(9024,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:17:20',NULL),(9025,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:18:32',NULL),(9026,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:18:35',NULL),(9027,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:18:35',NULL),(9028,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:18:36',NULL),(9029,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:18:36',NULL),(9030,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:19:03',NULL),(9031,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:19:06',NULL),(9032,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:19:07',NULL),(9033,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:19:07',NULL),(9034,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:19:07',NULL),(9035,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:19:42',NULL),(9036,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:19:44',NULL),(9037,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:19:44',NULL),(9038,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:19:44',NULL),(9039,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:19:45',NULL),(9040,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:19:53',NULL),(9041,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:19:55',NULL),(9042,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:19:55',NULL),(9043,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:19:55',NULL),(9044,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:19:55',NULL),(9045,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:25:54',NULL),(9046,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:26:02',NULL),(9047,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:26:02',NULL),(9048,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:26:02',NULL),(9049,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:26:02',NULL),(9050,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/removeCart','127.0.0.1','2024-05-31 09:26:17',NULL),(9051,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:26:17',NULL),(9052,1,'kode transaksi :  access url: http://192.168.1.157:8002/salesReport','192.168.1.207','2024-05-31 09:26:19',NULL),(9053,1,'kode transaksi :  access url: http://192.168.1.157:8002/salesReport','192.168.1.207','2024-05-31 09:26:22',NULL),(9054,1,'kode transaksi :  access url: http://192.168.1.157:8002/salesReport','192.168.1.207','2024-05-31 09:26:26',NULL),(9055,1,'kode transaksi :  access url: http://192.168.1.157:8002/kasirReport','192.168.1.207','2024-05-31 09:26:29',NULL),(9056,1,'kode transaksi :  access url: http://192.168.1.157:8002/kasirReport','192.168.1.207','2024-05-31 09:26:39',NULL),(9057,1,'kode transaksi :  access url: http://192.168.1.157:8002/kasirReport','192.168.1.207','2024-05-31 09:26:39',NULL),(9058,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:26:39',NULL),(9059,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:26:45',NULL),(9060,1,'kode transaksi :  access url: http://192.168.1.157:8002/salesReport/pendapatan','192.168.1.207','2024-05-31 09:27:00',NULL),(9061,1,'kode transaksi :  access url: http://192.168.1.157:8002/salesReport/pendapatan','192.168.1.207','2024-05-31 09:27:07',NULL),(9062,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:28:11',NULL),(9063,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:28:11',NULL),(9064,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:28:11',NULL),(9065,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:29:49',NULL),(9066,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:29:53',NULL),(9067,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:29:53',NULL),(9068,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:29:53',NULL),(9069,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:29:53',NULL),(9070,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:32:27',NULL),(9071,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:32:29',NULL),(9072,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:32:30',NULL),(9073,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:32:30',NULL),(9074,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:32:30',NULL),(9075,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/profitsetting','192.168.1.207','2024-05-31 09:33:13',NULL),(9076,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item','192.168.1.207','2024-05-31 09:33:35',NULL),(9077,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/datatable','192.168.1.207','2024-05-31 09:33:37',NULL),(9078,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/edit/4','192.168.1.207','2024-05-31 09:33:44',NULL),(9079,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategoriPenjualan','192.168.1.207','2024-05-31 09:34:15',NULL),(9080,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategoriPenjualan/datatable','192.168.1.207','2024-05-31 09:34:16',NULL),(9081,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategoriPenjualan','192.168.1.207','2024-05-31 09:34:21',NULL),(9082,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategoriPenjualan/datatable','192.168.1.207','2024-05-31 09:34:22',NULL),(9083,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:34:22',NULL),(9084,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:34:26',NULL),(9085,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:34:26',NULL),(9086,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:34:26',NULL),(9087,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:34:27',NULL),(9088,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/profitsetting','192.168.1.207','2024-05-31 09:34:48',NULL),(9089,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item','192.168.1.207','2024-05-31 09:34:52',NULL),(9090,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/datatable','192.168.1.207','2024-05-31 09:34:53',NULL),(9091,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/item/edit/4','192.168.1.207','2024-05-31 09:34:55',NULL),(9092,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategori','192.168.1.207','2024-05-31 09:36:35',NULL),(9093,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategori/datatable','192.168.1.207','2024-05-31 09:36:36',NULL),(9094,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategori','192.168.1.207','2024-05-31 09:36:41',NULL),(9095,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategori/datatable','192.168.1.207','2024-05-31 09:36:42',NULL),(9096,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/profitsetting','192.168.1.207','2024-05-31 09:37:09',NULL),(9097,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategori','192.168.1.207','2024-05-31 09:37:26',NULL),(9098,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategori/datatable','192.168.1.207','2024-05-31 09:37:27',NULL),(9099,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:37:28',NULL),(9100,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategori/create','192.168.1.207','2024-05-31 09:37:33',NULL),(9101,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategoriPenjualan','192.168.1.207','2024-05-31 09:37:45',NULL),(9102,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategoriPenjualan/datatable','192.168.1.207','2024-05-31 09:37:46',NULL),(9103,1,'kode transaksi :  access url: http://192.168.1.157:8002/dataInduk/kategoriPenjualan/edit/2','192.168.1.207','2024-05-31 09:37:52',NULL),(9104,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:37:54',NULL),(9105,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:37:58',NULL),(9106,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:37:58',NULL),(9107,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:37:58',NULL),(9108,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:37:58',NULL),(9109,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:38:01',NULL),(9110,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.207','2024-05-31 09:38:02',NULL),(9111,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getItem','192.168.1.207','2024-05-31 09:38:09',NULL),(9112,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 09:38:14',NULL),(9113,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.207','2024-05-31 09:38:54',NULL),(9114,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 09:38:58',NULL),(9115,1,'kode transaksi : SO240530-0001 access url: http://127.0.0.1:8002/sales/print/SO240530-0001','127.0.0.1','2024-05-31 09:40:59',NULL),(9116,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:41:09',NULL),(9117,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.207','2024-05-31 09:41:35',NULL),(9118,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 09:41:41',NULL),(9119,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.207','2024-05-31 09:41:41',NULL),(9120,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.207','2024-05-31 09:41:41',NULL),(9121,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.207','2024-05-31 09:41:41',NULL),(9122,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 09:41:44',NULL),(9123,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.207','2024-05-31 09:41:44',NULL),(9124,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.207','2024-05-31 09:41:44',NULL),(9125,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.207','2024-05-31 09:41:45',NULL),(9126,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 09:41:49',NULL),(9127,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.207','2024-05-31 09:41:58',NULL),(9128,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 09:42:01',NULL),(9129,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.207','2024-05-31 09:42:01',NULL),(9130,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.207','2024-05-31 09:42:01',NULL),(9131,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.207','2024-05-31 09:42:02',NULL),(9132,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.207','2024-05-31 09:42:09',NULL),(9133,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-05-31 09:47:47',NULL),(9134,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-05-31 09:47:50',NULL),(9135,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:47:50',NULL),(9136,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:47:50',NULL),(9137,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-05-31 09:47:50',NULL),(9138,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:47:57',NULL),(9139,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:49:00',NULL),(9140,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:49:23',NULL),(9141,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:49:27',NULL),(9142,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:49:39',NULL),(9143,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:49:51',NULL),(9144,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:50:17',NULL),(9145,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:50:33',NULL),(9146,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:54:45',NULL),(9147,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-05-31 09:57:33',NULL),(9148,1,'kode transaksi :  access url: http://127.0.0.1:8002/dashboard','127.0.0.1','2024-06-03 06:25:32',NULL),(9149,1,'kode transaksi :  access url: http://127.0.0.1:8002/gambar/user/1','127.0.0.1','2024-06-03 06:25:33',NULL),(9150,1,'kode transaksi :  access url: http://127.0.0.1:8002/dashboardAjax','127.0.0.1','2024-06-03 06:25:36',NULL),(9151,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 06:25:41',NULL),(9152,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 06:25:53',NULL),(9153,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 06:25:53',NULL),(9154,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 06:30:49',NULL),(9155,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-03 06:33:31',NULL),(9156,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-03 06:33:34',NULL),(9157,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/7','127.0.0.1','2024-06-03 06:33:39',NULL),(9158,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-03 06:33:39',NULL),(9159,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/storeCart','127.0.0.1','2024-06-03 06:33:40',NULL),(9160,1,'kode transaksi : SO240603-0001 access url: http://127.0.0.1:8002/sales/store/bayar','127.0.0.1','2024-06-03 06:33:51',NULL),(9161,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-03 06:33:55',NULL),(9162,1,'kode transaksi : SO240603-0001 access url: http://127.0.0.1:8002/sales/store/bayar','127.0.0.1','2024-06-03 06:34:07',NULL),(9163,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-03 06:36:42',NULL),(9164,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-03 06:36:47',NULL),(9165,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-03 06:36:47',NULL),(9166,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-03 06:36:48',NULL),(9167,1,'kode transaksi : SO240603-0001 access url: http://127.0.0.1:8002/sales/store/bayar','127.0.0.1','2024-06-03 06:37:23',NULL),(9168,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-03 06:38:46',NULL),(9169,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-03 06:38:50',NULL),(9170,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-03 06:38:50',NULL),(9171,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-03 06:38:51',NULL),(9172,1,'kode transaksi : SO240603-0001 access url: http://127.0.0.1:8002/sales/store/bayar','127.0.0.1','2024-06-03 06:39:01',NULL),(9173,1,'kode transaksi : SO240603-0001 access url: http://127.0.0.1:8002/sales/print/SO240603-0001','127.0.0.1','2024-06-03 06:39:01',NULL),(9174,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/configurations/1','127.0.0.1','2024-06-03 06:39:01',NULL),(9175,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-03 06:39:06',NULL),(9176,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-03 06:40:34',NULL),(9177,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/8','127.0.0.1','2024-06-03 06:40:37',NULL),(9178,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-03 06:40:37',NULL),(9179,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/storeCart','127.0.0.1','2024-06-03 06:40:37',NULL),(9180,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/4','127.0.0.1','2024-06-03 06:40:38',NULL),(9181,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-03 06:40:38',NULL),(9182,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/storeCart','127.0.0.1','2024-06-03 06:40:38',NULL),(9183,1,'kode transaksi : SO240603-0002 access url: http://127.0.0.1:8002/sales/store/bayar','127.0.0.1','2024-06-03 06:40:58',NULL),(9184,1,'kode transaksi : SO240603-0002 access url: http://127.0.0.1:8002/sales/print/SO240603-0002','127.0.0.1','2024-06-03 06:40:58',NULL),(9185,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-03 06:41:18',NULL),(9186,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-03 06:41:20',NULL),(9187,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/2','127.0.0.1','2024-06-03 06:41:23',NULL),(9188,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-03 06:41:23',NULL),(9189,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/storeCart','127.0.0.1','2024-06-03 06:41:23',NULL),(9190,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/6','127.0.0.1','2024-06-03 06:41:24',NULL),(9191,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-03 06:41:24',NULL),(9192,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/storeCart','127.0.0.1','2024-06-03 06:41:24',NULL),(9193,1,'kode transaksi : SO240603-0003 access url: http://127.0.0.1:8002/sales/store/bayar','127.0.0.1','2024-06-03 06:41:32',NULL),(9194,1,'kode transaksi : SO240603-0003 access url: http://127.0.0.1:8002/sales/print/SO240603-0003','127.0.0.1','2024-06-03 06:41:32',NULL),(9195,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-03 06:41:36',NULL),(9196,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 06:58:40',NULL),(9197,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:32:05',NULL),(9198,1,'kode transaksi :  access url: http://127.0.0.1:8002/gambar/user/1','127.0.0.1','2024-06-03 08:32:05',NULL),(9199,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:50:06',NULL),(9200,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:50:26',NULL),(9201,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:50:35',NULL),(9202,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:50:43',NULL),(9203,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:50:53',NULL),(9204,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:51:48',NULL),(9205,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:51:57',NULL),(9206,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:52:50',NULL),(9207,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:54:09',NULL),(9208,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:58:38',NULL),(9209,1,'kode transaksi :  access url: http://127.0.0.1:8002/gambar/user/1','127.0.0.1','2024-06-03 08:58:39',NULL),(9210,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:59:11',NULL),(9211,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:59:11',NULL),(9212,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 08:59:52',NULL),(9213,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 09:00:18',NULL),(9214,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 09:00:18',NULL),(9215,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 09:00:37',NULL),(9216,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 09:00:41',NULL),(9217,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-03 09:01:03',NULL),(9218,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboard','192.168.1.1','2024-06-04 01:32:13',NULL),(9219,1,'kode transaksi :  access url: http://192.168.1.157:8002/gambar/user/1','192.168.1.1','2024-06-04 01:32:30',NULL),(9220,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboardAjax','192.168.1.1','2024-06-04 01:35:12',NULL),(9221,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboard','192.168.1.1','2024-06-04 01:35:59',NULL),(9222,1,'kode transaksi :  access url: http://192.168.1.157:8002/gambar/user/1','192.168.1.1','2024-06-04 01:36:01',NULL),(9223,1,'kode transaksi :  access url: http://192.168.1.157:8002/gambar/user/1','192.168.1.1','2024-06-04 01:36:02',NULL),(9224,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboardAjax','192.168.1.1','2024-06-04 01:36:48',NULL),(9225,1,'kode transaksi :  access url: http://192.168.1.157:8002/purchase','192.168.1.1','2024-06-04 01:36:52',NULL),(9226,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/create','192.168.1.1','2024-06-04 01:52:00',NULL),(9227,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/getCart','192.168.1.1','2024-06-04 01:52:25',NULL),(9228,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.1','2024-06-04 01:52:25',NULL),(9229,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.1','2024-06-04 01:52:25',NULL),(9230,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/items/8','192.168.1.1','2024-06-04 01:52:26',NULL),(9231,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/chart/tax_discount','192.168.1.1','2024-06-04 01:52:26',NULL),(9232,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/items/4','192.168.1.1','2024-06-04 01:52:26',NULL),(9233,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/print/sementara/6','192.168.1.1','2024-06-04 01:52:36',NULL),(9234,1,'kode transaksi :  access url: http://192.168.1.157:8002/img/configurations/1','192.168.1.1','2024-06-04 01:52:37',NULL),(9235,1,'kode transaksi :  access url: http://127.0.0.1:8002','127.0.0.1','2024-06-04 02:28:19',NULL),(9236,1,'kode transaksi :  access url: http://127.0.0.1:8002/dashboard','127.0.0.1','2024-06-04 02:28:20',NULL),(9237,1,'kode transaksi :  access url: http://127.0.0.1:8002/dashboardAjax','127.0.0.1','2024-06-04 02:28:20',NULL),(9238,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 02:28:22',NULL),(9239,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-04 02:28:24',NULL),(9240,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 02:28:24',NULL),(9241,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 02:28:25',NULL),(9242,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 02:28:25',NULL),(9243,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/8','127.0.0.1','2024-06-04 02:28:25',NULL),(9244,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 02:28:31',NULL),(9245,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 02:29:38',NULL),(9246,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 02:29:40',NULL),(9247,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-04 02:29:48',NULL),(9248,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 02:29:49',NULL),(9249,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 02:29:49',NULL),(9250,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 02:29:49',NULL),(9251,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 02:29:59',NULL),(9252,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 02:30:10',NULL),(9253,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 02:30:13',NULL),(9254,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 02:30:18',NULL),(9255,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 03:53:45',NULL),(9256,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 03:53:48',NULL),(9257,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-04 03:53:51',NULL),(9258,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 03:53:51',NULL),(9259,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 03:53:52',NULL),(9260,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 03:53:52',NULL),(9261,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 03:53:55',NULL),(9262,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 03:54:02',NULL),(9263,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 03:54:02',NULL),(9264,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 03:54:32',NULL),(9265,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 03:54:46',NULL),(9266,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 03:54:47',NULL),(9267,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 03:55:04',NULL),(9268,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 03:55:08',NULL),(9269,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-04 03:55:10',NULL),(9270,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 03:55:10',NULL),(9271,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 03:55:11',NULL),(9272,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 03:55:11',NULL),(9273,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 03:55:14',NULL),(9274,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-04 03:55:22',NULL),(9275,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-04 03:55:24',NULL),(9276,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 03:55:24',NULL),(9277,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 03:55:24',NULL),(9278,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-04 03:55:25',NULL),(9279,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 03:55:27',NULL),(9280,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 03:55:32',NULL),(9281,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 03:55:55',NULL),(9282,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 03:56:26',NULL),(9283,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 03:57:01',NULL),(9284,1,'kode transaksi :  access url: http://192.168.1.157:8002','192.168.1.1','2024-06-04 07:24:59',NULL),(9285,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboard','192.168.1.1','2024-06-04 07:25:00',NULL),(9286,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboardAjax','192.168.1.1','2024-06-04 07:25:20',NULL),(9287,1,'kode transaksi :  access url: http://192.168.1.157:8002','192.168.1.1','2024-06-04 09:06:04',NULL),(9288,1,'kode transaksi :  access url: http://192.168.1.157:8002','192.168.1.1','2024-06-04 09:07:40',NULL),(9289,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboard','192.168.1.1','2024-06-04 09:07:40',NULL),(9290,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-04 09:11:23',NULL),(9291,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboard','192.168.1.1','2024-06-04 09:12:12',NULL),(9292,1,'kode transaksi :  access url: http://127.0.0.1:8002/dashboard','127.0.0.1','2024-06-05 04:15:48',NULL),(9293,1,'kode transaksi :  access url: http://127.0.0.1:8002/gambar/user/1','127.0.0.1','2024-06-05 04:15:49',NULL),(9294,1,'kode transaksi :  access url: http://127.0.0.1:8002/dashboardAjax','127.0.0.1','2024-06-05 04:15:52',NULL),(9295,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-05 04:15:54',NULL),(9296,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-05 04:15:56',NULL),(9297,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-05 04:15:56',NULL),(9298,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-05 04:15:56',NULL),(9299,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-05 04:15:56',NULL),(9300,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/8','127.0.0.1','2024-06-05 04:15:57',NULL),(9301,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/items/4','127.0.0.1','2024-06-05 04:15:57',NULL),(9302,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-05 04:16:02',NULL),(9303,1,'kode transaksi :  access url: http://127.0.0.1:8002/img/configurations/1','127.0.0.1','2024-06-05 04:16:02',NULL),(9304,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-05 04:17:05',NULL),(9305,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-05 04:17:27',NULL),(9306,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-05 04:17:33',NULL),(9307,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-05 04:17:47',NULL),(9308,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-05 04:17:56',NULL),(9309,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-05 04:17:56',NULL),(9310,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-05 04:18:00',NULL),(9311,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-05 04:18:03',NULL),(9312,1,'kode transaksi :  access url: http://127.0.0.1:8002/kasirReport','127.0.0.1','2024-06-05 04:18:06',NULL),(9313,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/perusahaan','127.0.0.1','2024-06-05 06:07:37',NULL),(9314,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/perusahaan/store','127.0.0.1','2024-06-05 06:08:04',NULL),(9315,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-05 06:08:43',NULL),(9316,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-05 06:08:44',NULL),(9317,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-05 06:08:45',NULL),(9318,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-05 06:08:45',NULL),(9319,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-05 06:08:45',NULL),(9320,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-05 06:08:48',NULL),(9321,1,'kode transaksi :  access url: http://127.0.0.1:8002/dataInduk/perusahaan','127.0.0.1','2024-06-05 06:10:17',NULL),(9322,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/create','127.0.0.1','2024-06-05 06:11:38',NULL),(9323,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/getCart','127.0.0.1','2024-06-05 06:11:40',NULL),(9324,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-05 06:11:40',NULL),(9325,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-05 06:11:40',NULL),(9326,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/chart/tax_discount','127.0.0.1','2024-06-05 06:11:40',NULL),(9327,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-05 06:11:43',NULL),(9328,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-05 06:12:26',NULL),(9329,1,'kode transaksi :  access url: http://192.168.1.157:8002','192.168.1.1','2024-06-05 06:17:17',NULL),(9330,1,'kode transaksi :  access url: http://192.168.1.157:8002','192.168.1.1','2024-06-05 06:17:19',NULL),(9331,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboard','192.168.1.1','2024-06-05 06:17:21',NULL),(9332,1,'kode transaksi :  access url: http://127.0.0.1:8002/sales/print/sementara/6','127.0.0.1','2024-06-05 06:19:47',NULL),(9333,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/print/sementara/6','192.168.1.1','2024-06-05 06:20:07',NULL),(9334,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/print/sementara/6','192.168.1.1','2024-06-05 06:20:43',NULL),(9335,1,'kode transaksi :  access url: http://192.168.1.157:8002','192.168.1.1','2024-06-05 08:29:02',NULL),(9336,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboard','192.168.1.1','2024-06-05 08:29:03',NULL),(9337,1,'kode transaksi :  access url: http://192.168.1.157:8002/sales/print/sementara/6','192.168.1.1','2024-06-05 08:29:04',NULL),(9338,1,'kode transaksi :  access url: http://192.168.1.157:8002/dashboardAjax','192.168.1.1','2024-06-05 08:29:18',NULL),(9339,1,'kode transaksi :  access url: http://127.0.0.1:8003/dashboard','127.0.0.1','2024-06-18 03:24:02',NULL),(9340,1,'kode transaksi :  access url: http://127.0.0.1:8003/gambar/user/1','127.0.0.1','2024-06-18 03:24:03',NULL),(9341,1,'kode transaksi :  access url: http://127.0.0.1:8003/dashboardAjax','127.0.0.1','2024-06-18 03:24:03',NULL),(9342,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 03:24:09',NULL),(9343,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 03:24:10',NULL),(9344,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 03:24:14',NULL),(9345,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 03:24:14',NULL),(9346,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 03:24:16',NULL),(9347,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 03:24:21',NULL),(9348,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 03:24:23',NULL),(9349,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 03:27:59',NULL),(9350,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 03:28:00',NULL),(9351,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/create','127.0.0.1','2024-06-18 03:28:28',NULL),(9352,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 03:29:34',NULL),(9353,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 03:29:35',NULL),(9354,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 04:26:27',NULL),(9355,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 04:26:28',NULL),(9356,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 04:26:32',NULL),(9357,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 04:26:38',NULL),(9358,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 04:26:40',NULL),(9359,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/disable','127.0.0.1','2024-06-18 04:26:43',NULL),(9360,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 04:26:44',NULL),(9361,1,'kode transaksi :  access url: http://127.0.0.1:8003/purchaseReport','127.0.0.1','2024-06-18 08:04:29',NULL),(9362,1,'kode transaksi :  access url: http://127.0.0.1:8003/gambar/user/1','127.0.0.1','2024-06-18 08:04:30',NULL),(9363,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/profitsetting','127.0.0.1','2024-06-18 09:25:46',NULL),(9364,1,'kode transaksi :  access url: http://127.0.0.1:8003/gambar/user/1','127.0.0.1','2024-06-18 09:25:47',NULL),(9365,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 09:25:58',NULL),(9366,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 09:25:59',NULL),(9367,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 09:26:04',NULL),(9368,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori','127.0.0.1','2024-06-18 09:26:04',NULL),(9369,1,'kode transaksi :  access url: http://127.0.0.1:8003/dataInduk/kategori/datatable','127.0.0.1','2024-06-18 09:26:05',NULL),(9370,1,'kode transaksi :  access url: http://192.168.1.157:8000/dashboard','192.168.1.1','2024-08-03 02:13:16',NULL),(9371,1,'kode transaksi :  access url: http://192.168.1.157:8000/gambar/user/1','192.168.1.1','2024-08-03 02:13:21',NULL),(9372,1,'kode transaksi :  access url: http://192.168.1.157:8000/dashboardAjax','192.168.1.1','2024-08-03 02:13:22',NULL),(9373,1,'kode transaksi :  access url: http://127.0.0.1:8001','127.0.0.1','2024-08-08 03:44:26',NULL),(9374,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboard','127.0.0.1','2024-08-08 03:44:27',NULL),(9375,1,'kode transaksi :  access url: http://127.0.0.1:8001/gambar/user/1','127.0.0.1','2024-08-08 03:44:27',NULL),(9376,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboardAjax','127.0.0.1','2024-08-08 03:44:30',NULL),(9377,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/create','127.0.0.1','2024-08-08 03:44:40',NULL),(9378,1,'kode transaksi :  access url: http://127.0.0.1:8001/purchase','127.0.0.1','2024-08-08 03:45:54',NULL),(9379,1,'kode transaksi :  access url: http://127.0.0.1:8001/purchase/datatable','127.0.0.1','2024-08-08 03:45:55',NULL),(9380,1,'kode transaksi :  access url: http://127.0.0.1:8001/purchase','127.0.0.1','2024-08-08 03:46:08',NULL),(9381,1,'kode transaksi :  access url: http://127.0.0.1:8001/purchase','127.0.0.1','2024-08-08 03:46:14',NULL),(9382,1,'kode transaksi :  access url: http://127.0.0.1:8001/gambar/user/1','127.0.0.1','2024-08-08 03:46:14',NULL),(9383,1,'kode transaksi :  access url: http://127.0.0.1:8001/purchase/datatable','127.0.0.1','2024-08-08 03:46:16',NULL),(9384,1,'kode transaksi :  access url: http://127.0.0.1:8001/purchase','127.0.0.1','2024-08-08 03:46:24',NULL),(9385,1,'kode transaksi :  access url: http://127.0.0.1:8001/purchase/datatable','127.0.0.1','2024-08-08 03:46:26',NULL),(9386,1,'kode transaksi :  access url: http://127.0.0.1:8001/purchase','127.0.0.1','2024-08-08 03:46:43',NULL),(9387,1,'kode transaksi :  access url: http://127.0.0.1:8001/gambar/user/1','127.0.0.1','2024-08-08 03:46:44',NULL),(9388,1,'kode transaksi :  access url: http://127.0.0.1:8001/purchase/datatable','127.0.0.1','2024-08-08 03:46:45',NULL),(9389,1,'kode transaksi :  access url: http://127.0.0.1:8001','127.0.0.1','2024-08-08 03:46:57',NULL),(9390,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboard','127.0.0.1','2024-08-08 03:46:57',NULL),(9391,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboardAjax','127.0.0.1','2024-08-08 03:46:59',NULL),(9392,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboard','127.0.0.1','2024-08-08 03:47:02',NULL),(9393,1,'kode transaksi :  access url: http://127.0.0.1:8001/gambar/user/1','127.0.0.1','2024-08-08 03:47:03',NULL),(9394,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboardAjax','127.0.0.1','2024-08-08 03:47:04',NULL),(9395,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboard','127.0.0.1','2024-08-08 03:47:07',NULL),(9396,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboardAjax','127.0.0.1','2024-08-08 03:47:09',NULL),(9397,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboard','127.0.0.1','2024-08-08 03:47:19',NULL),(9398,1,'kode transaksi :  access url: http://127.0.0.1:8001/gambar/user/1','127.0.0.1','2024-08-08 03:47:19',NULL),(9399,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboardAjax','127.0.0.1','2024-08-08 03:47:21',NULL),(9400,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboard','127.0.0.1','2024-08-08 03:47:30',NULL),(9401,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboardAjax','127.0.0.1','2024-08-08 03:47:31',NULL),(9402,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboard','127.0.0.1','2024-08-08 03:47:38',NULL),(9403,1,'kode transaksi :  access url: http://127.0.0.1:8001/dashboardAjax','127.0.0.1','2024-08-08 03:47:39',NULL),(9404,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/create','127.0.0.1','2024-08-08 03:47:40',NULL),(9405,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/create','127.0.0.1','2024-08-08 03:47:45',NULL),(9406,1,'kode transaksi :  access url: http://127.0.0.1:8001/gambar/user/1','127.0.0.1','2024-08-08 03:47:45',NULL),(9407,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/create','127.0.0.1','2024-08-08 03:47:55',NULL),(9408,1,'kode transaksi :  access url: http://127.0.0.1:8001/gambar/user/1','127.0.0.1','2024-08-08 03:47:55',NULL),(9409,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/create','127.0.0.1','2024-08-08 03:48:01',NULL),(9410,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/getCart','127.0.0.1','2024-08-08 03:48:25',NULL),(9411,1,'kode transaksi :  access url: http://127.0.0.1:8001/img/items/7','127.0.0.1','2024-08-08 03:48:28',NULL),(9412,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/chart/tax_discount','127.0.0.1','2024-08-08 03:48:28',NULL),(9413,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/storeCart','127.0.0.1','2024-08-08 03:48:28',NULL),(9414,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/storeCart','127.0.0.1','2024-08-08 03:48:36',NULL),(9415,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/chart/tax_discount','127.0.0.1','2024-08-08 03:48:37',NULL),(9416,1,'kode transaksi :  access url: http://127.0.0.1:8001/img/items/6','127.0.0.1','2024-08-08 03:48:37',NULL),(9417,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/chart/tax_discount','127.0.0.1','2024-08-08 03:48:38',NULL),(9418,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/storeCart','127.0.0.1','2024-08-08 03:48:38',NULL),(9419,1,'kode transaksi : SO240808-0001 access url: http://127.0.0.1:8001/sales/store/bayar','127.0.0.1','2024-08-08 03:48:53',NULL),(9420,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/create','127.0.0.1','2024-08-08 03:49:20',NULL),(9421,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/getItem','127.0.0.1','2024-08-08 04:10:32',NULL),(9422,1,'kode transaksi :  access url: http://127.0.0.1:8001/keanggotaan','127.0.0.1','2024-08-08 04:11:04',NULL),(9423,1,'kode transaksi :  access url: http://127.0.0.1:8001/keanggotaan/datatable','127.0.0.1','2024-08-08 04:11:05',NULL),(9424,1,'kode transaksi :  access url: http://127.0.0.1:8001/keanggotaan','127.0.0.1','2024-08-08 04:11:10',NULL),(9425,1,'kode transaksi :  access url: http://127.0.0.1:8001/keanggotaan','127.0.0.1','2024-08-08 04:11:10',NULL),(9426,1,'kode transaksi :  access url: http://127.0.0.1:8001/keanggotaan','127.0.0.1','2024-08-08 04:11:11',NULL),(9427,1,'kode transaksi :  access url: http://127.0.0.1:8001/keanggotaan','127.0.0.1','2024-08-08 04:11:11',NULL),(9428,1,'kode transaksi :  access url: http://127.0.0.1:8001/keanggotaan','127.0.0.1','2024-08-08 04:11:11',NULL),(9429,1,'kode transaksi :  access url: http://127.0.0.1:8001/keanggotaan/datatable','127.0.0.1','2024-08-08 04:11:12',NULL),(9430,1,'kode transaksi :  access url: http://127.0.0.1:8001/iframe','127.0.0.1','2024-08-08 04:49:54',NULL),(9431,1,'kode transaksi :  access url: http://127.0.0.1:8001/sales/create','127.0.0.1','2024-08-08 04:49:57',NULL),(9432,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:18:20',NULL),(9433,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:21:42',NULL),(9434,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:21:57',NULL),(9435,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:24:21',NULL),(9436,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:25:03',NULL),(9437,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:25:20',NULL),(9438,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:25:32',NULL),(9439,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:25:42',NULL),(9440,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:27:29',NULL),(9441,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:30:29',NULL),(9442,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:33:51',NULL),(9443,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:34:37',NULL),(9444,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:34:45',NULL),(9445,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:35:01',NULL),(9446,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:36:04',NULL),(9447,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:36:09',NULL),(9448,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:37:16',NULL),(9449,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:37:36',NULL),(9450,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:38:01',NULL),(9451,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:39:00',NULL),(9452,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:41:04',NULL),(9453,1,'kode transaksi :  access url: http://127.0.0.1:8000','127.0.0.1','2024-08-08 06:41:26',NULL),(9454,1,'kode transaksi :  access url: http://127.0.0.1:8000/dashboard','127.0.0.1','2024-08-08 06:41:27',NULL),(9455,1,'kode transaksi :  access url: http://127.0.0.1:8000/dashboard','127.0.0.1','2024-08-08 06:42:34',NULL),(9456,1,'kode transaksi :  access url: http://127.0.0.1:8000/logout','127.0.0.1','2024-08-08 06:42:38',NULL),(9457,1,'kode transaksi :  access url: http://127.0.0.1:8000/dashboard','127.0.0.1','2024-08-08 06:42:47',NULL),(9458,1,'kode transaksi :  access url: http://127.0.0.1:8000/dashboard','127.0.0.1','2024-08-08 06:45:43',NULL),(9459,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:45:44',NULL),(9460,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:48:43',NULL),(9461,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:49:01',NULL),(9462,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:49:02',NULL),(9463,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:49:24',NULL),(9464,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:50:06',NULL),(9465,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:50:26',NULL),(9466,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:52:06',NULL),(9467,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:52:38',NULL),(9468,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:52:46',NULL),(9469,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:52:54',NULL),(9470,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:53:03',NULL),(9471,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:53:19',NULL),(9472,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:54:05',NULL),(9473,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:54:37',NULL),(9474,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:55:43',NULL),(9475,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:56:56',NULL),(9476,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:57:17',NULL),(9477,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:57:28',NULL),(9478,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 06:58:50',NULL),(9479,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:00:09',NULL),(9480,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:00:41',NULL),(9481,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:00:53',NULL),(9482,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:01:20',NULL),(9483,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:02:14',NULL),(9484,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:06:59',NULL),(9485,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:07:49',NULL),(9486,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:09:33',NULL),(9487,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:10:37',NULL),(9488,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:10:53',NULL),(9489,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:11:12',NULL),(9490,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:11:26',NULL),(9491,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:12:04',NULL),(9492,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:12:44',NULL),(9493,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:14:05',NULL),(9494,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:14:15',NULL),(9495,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:23:16',NULL),(9496,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:24:04',NULL),(9497,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:27:13',NULL),(9498,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:27:42',NULL),(9499,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:27:46',NULL),(9500,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:28:11',NULL),(9501,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:28:28',NULL),(9502,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:28:52',NULL),(9503,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:29:47',NULL),(9504,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:29:58',NULL),(9505,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:34:43',NULL),(9506,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:35:40',NULL),(9507,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:36:10',NULL),(9508,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:42:39',NULL),(9509,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:44:24',NULL),(9510,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:45:33',NULL),(9511,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:45:46',NULL),(9512,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:47:31',NULL),(9513,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:47:53',NULL),(9514,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:47:55',NULL),(9515,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:48:29',NULL),(9516,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:48:36',NULL),(9517,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:48:37',NULL),(9518,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:50:35',NULL),(9519,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:50:38',NULL),(9520,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:50:53',NULL),(9521,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:51:01',NULL),(9522,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:51:03',NULL),(9523,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:51:15',NULL),(9524,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:51:17',NULL),(9525,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:52:08',NULL),(9526,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:52:16',NULL),(9527,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:52:21',NULL),(9528,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:52:25',NULL),(9529,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:52:29',NULL),(9530,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:52:35',NULL),(9531,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:52:50',NULL),(9532,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:53:10',NULL),(9533,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:53:15',NULL),(9534,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:53:32',NULL),(9535,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:53:40',NULL),(9536,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:53:57',NULL),(9537,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:54:12',NULL),(9538,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:56:32',NULL),(9539,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:58:38',NULL),(9540,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:58:48',NULL),(9541,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 07:59:32',NULL),(9542,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:00:32',NULL),(9543,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:00:45',NULL),(9544,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:02:55',NULL),(9545,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:04:27',NULL),(9546,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:04:56',NULL),(9547,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:05:05',NULL),(9548,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:13:47',NULL),(9549,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:16:29',NULL),(9550,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:16:37',NULL),(9551,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:16:50',NULL),(9552,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:16:56',NULL),(9553,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:17:17',NULL),(9554,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:17:37',NULL),(9555,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:19:28',NULL),(9556,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:19:40',NULL),(9557,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:19:49',NULL),(9558,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:19:56',NULL),(9559,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:20:17',NULL),(9560,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:20:41',NULL),(9561,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:20:49',NULL),(9562,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:21:43',NULL),(9563,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:21:44',NULL),(9564,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:23:25',NULL),(9565,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:23:38',NULL),(9566,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:23:48',NULL),(9567,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:24:07',NULL),(9568,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:24:28',NULL),(9569,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:25:10',NULL),(9570,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:25:31',NULL),(9571,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:27:06',NULL),(9572,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:28:54',NULL),(9573,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:29:05',NULL),(9574,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:29:16',NULL),(9575,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:29:27',NULL),(9576,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:29:52',NULL),(9577,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:33:26',NULL),(9578,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:33:38',NULL),(9579,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:38:09',NULL),(9580,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:38:29',NULL),(9581,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:38:35',NULL),(9582,1,'kode transaksi :  access url: http://127.0.0.1:8000/sales/create','127.0.0.1','2024-08-08 08:38:53',NULL);
/*!40000 ALTER TABLE `logs_apps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_items`
--

DROP TABLE IF EXISTS `master_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_item` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nama_item` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `satuan` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `tipe` enum('Item Jadi','Bahan Baku') COLLATE utf8_unicode_ci DEFAULT NULL,
  `buy_price` double DEFAULT NULL,
  `sell_price` double DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8_unicode_ci,
  `status_bahan_baku` enum('0','1') COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_created` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_updated` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Master Item untuk Pembelian';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_items`
--

LOCK TABLES `master_items` WRITE;
/*!40000 ALTER TABLE `master_items` DISABLE KEYS */;
INSERT INTO `master_items` VALUES (1,'B001','Tepung',1,4,'Bahan Baku',5000,5500,1,NULL,NULL,NULL,'1',NULL,'2024-03-20 13:54:13'),(2,'IJ001','Mie Goreng',1,4,'Item Jadi',0,5000,1,NULL,'0','1','1','2024-02-13 14:38:14','2024-05-25 10:33:00'),(3,'B002','Telur',1,4,'Bahan Baku',2000,3000,0,NULL,NULL,'1','1','2024-02-13 14:41:08','2024-03-20 13:53:47'),(4,'IJ002','JUICE MANGGA',1,5,'Item Jadi',5000,6000,1,'Minuman jus rasa mangga',NULL,'1','1','2024-03-18 11:39:14','2024-03-19 12:01:47'),(5,'IJ003','wefwer',1,4,'Item Jadi',50000,55000,1,'-','0','1',NULL,'2024-03-20 13:26:20',NULL),(6,'IJ004','Mie Rebus',1,4,'Item Jadi',3000,5000,1,'-','1','1','1','2024-03-20 13:37:40','2024-03-20 16:10:11'),(7,'IJ005','asfsf',1,4,'Item Jadi',45000,50000,1,NULL,'1','1',NULL,'2024-03-21 10:25:26',NULL),(8,'IJ006','JUICE LECCI',1,11,'Item Jadi',12000,20000,1,'Aneka Juice','0','1','1','2024-03-23 08:31:09','2024-03-23 08:31:51'),(9,'IJ007','qwerty1234',1,4,'Item Jadi',45000,50000,1,'-','0','1',NULL,'2024-03-23 10:28:09',NULL),(10,'B003','abc',1,4,'Bahan Baku',3000,4000,1,'-','1','1',NULL,'2024-03-23 10:36:30',NULL);
/*!40000 ALTER TABLE `master_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `master_items_bahan`
--

DROP TABLE IF EXISTS `master_items_bahan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `master_items_bahan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item_jadi` int(11) DEFAULT NULL,
  `id_item_bahan` int(11) DEFAULT NULL,
  `qty_bahan` double DEFAULT NULL,
  `harga_beli_bahan` double DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `master_items_bahan`
--

LOCK TABLES `master_items_bahan` WRITE;
/*!40000 ALTER TABLE `master_items_bahan` DISABLE KEYS */;
INSERT INTO `master_items_bahan` VALUES (3,6,1,2,4000,NULL,NULL),(4,7,1,1,5000,NULL,NULL),(5,7,3,4,40000,NULL,NULL);
/*!40000 ALTER TABLE `master_items_bahan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_logins`
--

DROP TABLE IF EXISTS `member_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_logins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `device_key` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `failed_login` int(11) NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `users_acls_id` bigint(20) unsigned DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_EI` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_logins_username_unique` (`username`),
  UNIQUE KEY `member_logins_email_unique` (`email`),
  KEY `member_logins_users_acls_id_foreign` (`users_acls_id`),
  CONSTRAINT `member_logins_users_acls_id_foreign` FOREIGN KEY (`users_acls_id`) REFERENCES `users_acls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_logins`
--

LOCK TABLES `member_logins` WRITE;
/*!40000 ALTER TABLE `member_logins` DISABLE KEYS */;
INSERT INTO `member_logins` VALUES (1,'Hendik Endtato','M00001','demo@gmail.com','083987459348','$2y$10$hX6ytZJBjgGfkcP.MEg4F..tQNE9oj2VFwI19V7Ni2BsRKgN2Eb9u',NULL,1,0,NULL,NULL,NULL,NULL,1,'2023-08-12 04:37:21',NULL);
/*!40000 ALTER TABLE `member_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `memberships`
--

DROP TABLE IF EXISTS `memberships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `memberships` (
  `code` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `nik` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `nama` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `gender` enum('l','p','o') COLLATE utf8_unicode_ci NOT NULL,
  `kota` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `provinsi` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `negara` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'indonesia',
  `address` text COLLATE utf8_unicode_ci,
  `email` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `place_birth` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `date_birth` date NOT NULL,
  `expired` date NOT NULL,
  `ballance` decimal(18,0) NOT NULL DEFAULT '0',
  `point` decimal(18,0) NOT NULL DEFAULT '0',
  `status` enum('active','suspend','close') COLLATE utf8_unicode_ci NOT NULL,
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `agreement` enum('0','1','2') COLLATE utf8_unicode_ci DEFAULT '0',
  `agreement_time` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_created` bigint(20) unsigned NOT NULL,
  `member_logins_id` bigint(20) unsigned NOT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  UNIQUE KEY `memberships_code_unique` (`code`),
  UNIQUE KEY `memberships_nik_unique` (`nik`),
  UNIQUE KEY `memberships_mobile_unique` (`mobile`),
  KEY `memberships_member_logins_id_foreign` (`member_logins_id`),
  KEY `memberships_user_created_foreign` (`user_created`),
  KEY `memberships_user_updated_foreign` (`user_updated`),
  CONSTRAINT `memberships_member_logins_id_foreign` FOREIGN KEY (`member_logins_id`) REFERENCES `member_logins` (`id`),
  CONSTRAINT `memberships_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `memberships_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memberships`
--

LOCK TABLES `memberships` WRITE;
/*!40000 ALTER TABLE `memberships` DISABLE KEYS */;
INSERT INTO `memberships` VALUES ('M00001','3503060901020001','Hendik Endtato','083987459348','l','Surabaya','Jawa Timur','indonesia','Jalan Mawar no 5','edisonhendik@gmail.com','Trenggalek','2023-08-01','2030-12-31',0,0,'active',0,'1','2023-10-11 08:29:47','2023-08-12 04:37:21',NULL,1,1,NULL);
/*!40000 ALTER TABLE `memberships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2012_10_28_114235_create_users_acls_table',1),(2,'2014_10_12_000000_create_users_table',1),(3,'2014_10_12_100000_create_password_resets_table',1),(4,'2019_08_19_000000_create_failed_jobs_table',1),(5,'2021_10_28_114529_create_logs_apps_table',1),(6,'2021_10_28_114653_create_superusers_table',1),(7,'2021_11_03_174147_user_sudo',1),(8,'2021_11_15_120642_create_units_table',1),(9,'2021_11_16_104907_create_categories_table',1),(10,'2021_11_16_104912_create_items_table',1),(11,'2021_11_16_162223_create_projects_table',1),(12,'2021_11_17_162037_create_requistions_table',1),(13,'2021_11_17_162059_create_requistion_details_table',1),(14,'2022_01_24_110926_create__doc_prefixes_table',1),(15,'2022_02_14_075512_create_member_logins_table',1),(16,'2022_02_14_084310_create_memberships_table',1),(17,'2022_02_14_084320_create_bookings_table',1),(18,'2022_02_18_071544_create_sales_table',1),(19,'2022_02_23_065539_create_purchases_table',1),(20,'2022_03_15_132551_create_carts_table',1),(21,'2022_03_23_173812_create_confgurations_table',1),(22,'2022_05_11_163250_create_order_lists_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembelian`
--

DROP TABLE IF EXISTS `pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembelian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_pembelian` char(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `total` double DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembelian`
--

LOCK TABLES `pembelian` WRITE;
/*!40000 ALTER TABLE `pembelian` DISABLE KEYS */;
INSERT INTO `pembelian` VALUES (1,'Pembelian-001',NULL,200000,1,NULL,'2024-05-31 14:16:59',NULL),(2,'Pembelian-002','2024-05-31',535000,1,1,'2024-05-31 14:26:01','2024-05-31 15:15:39');
/*!40000 ALTER TABLE `pembelian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembelian_details`
--

DROP TABLE IF EXISTS `pembelian_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pembelian_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pembelian` int(11) DEFAULT NULL,
  `tipe` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `harga` double DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `total` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembelian_details`
--

LOCK TABLES `pembelian_details` WRITE;
/*!40000 ALTER TABLE `pembelian_details` DISABLE KEYS */;
INSERT INTO `pembelian_details` VALUES (1,1,'Bahan Baku',1,'Tepung',1,0,25000,2),(2,1,'Lain-lain',0,'Bahan Lain',3,0,1,150000),(6,2,'Bahan Baku',1,'Tepung',1,25000,1,25000),(7,2,'Lain-lain',0,'Bahan Lain',3,150000,3,450000),(8,2,'Lain-lain',0,'Bahan Lain',1,15000,4,60000);
/*!40000 ALTER TABLE `pembelian_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_buku_besar`
--

DROP TABLE IF EXISTS `pos_buku_besar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_buku_besar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_buku_besar` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `no_jurnal_umum` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tgl_transaksi` date DEFAULT NULL,
  `no_transaksi` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tipe` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kode_akun` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `kredit` double DEFAULT NULL,
  `keterangan` text COLLATE utf8_unicode_ci,
  `sts_doc` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_buku_besar`
--

LOCK TABLES `pos_buku_besar` WRITE;
/*!40000 ALTER TABLE `pos_buku_besar` DISABLE KEYS */;
INSERT INTO `pos_buku_besar` VALUES (1,'001','NJU-00001','2023-10-19','SO231019-0001','penjualan','3',43160,0,'-','0',NULL,NULL),(2,'001','NJU-00002','2023-10-19','BKM-20231019-01','kas masuk','4',200000,0,'-','0',NULL,NULL),(3,'001','NJU-00003','2023-10-19','BKK-20231019-01','kas keluar','4',150000,0,'-','0',NULL,NULL),(4,'001','NJU-00004','2023-10-19','TR-20231019-0001','jurnal entry','4',980000,0,NULL,'0',NULL,NULL),(5,'001','NJU-00005','2023-10-20','BKM-20231020-01','kas masuk','4',150000,0,'-','0',NULL,NULL),(6,'001','NJU-00004','2023-10-19','TR-20231019-0001','jurnal entry','15',0,1000000,NULL,'0',NULL,NULL),(7,'001','NJU-00001','2023-10-19','SO231019-0001','penjualan','64',0,3960,'-','0',NULL,NULL),(8,'001','NJU-00003','2023-10-19','BKK-20231019-01','kas keluar','78',0,50000,'telpon','0',NULL,NULL),(9,'001','NJU-00003','2023-10-19','BKK-20231019-01','kas keluar','79',0,100000,'listrik','0',NULL,NULL),(10,'001','NJU-00001','2023-10-19','SO231019-0001','penjualan','100',0,39600,'-','0',NULL,NULL),(11,'001','NJU-00005','2023-10-20','BKM-20231020-01','kas masuk','100',0,100000,'-','0',NULL,NULL),(12,'001','NJU-00002','2023-10-19','BKM-20231019-01','kas masuk','101',0,125000,'-','0',NULL,NULL),(13,'001','NJU-00002','2023-10-19','BKM-20231019-01','kas masuk','101',0,25000,'-','0',NULL,NULL),(14,'001','NJU-00002','2023-10-19','BKM-20231019-01','kas masuk','102',0,50000,'-','0',NULL,NULL),(15,'001','NJU-00001','2023-10-19','SO231019-0001','penjualan','107',400,0,'-','0',NULL,NULL),(16,'001','NJU-00004','2023-10-19','TR-20231019-0001','jurnal entry','107',20000,0,NULL,'0',NULL,NULL),(17,'001','NJU-00005','2023-10-20','BKM-20231020-01','kas masuk','107',0,50000,'-','0',NULL,NULL);
/*!40000 ALTER TABLE `pos_buku_besar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_jurnal_umum`
--

DROP TABLE IF EXISTS `pos_jurnal_umum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos_jurnal_umum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_jurnal_umum` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tgl_transaksi` date DEFAULT NULL,
  `no_transaksi` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `tipe` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `kode_akun` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `debit` double DEFAULT NULL,
  `kredit` double DEFAULT NULL,
  `sts_buku_besar` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `sts_doc` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_jurnal_umum`
--

LOCK TABLES `pos_jurnal_umum` WRITE;
/*!40000 ALTER TABLE `pos_jurnal_umum` DISABLE KEYS */;
INSERT INTO `pos_jurnal_umum` VALUES (1,'NJU-00001','2024-05-30','SO240530-0001','penjualan','3',16000,0,'0','-','0','2024-05-30 14:43:59',NULL),(2,'NJU-00001','2024-05-30','SO240530-0001','penjualan','107',0,0,'0','-','0','2024-05-30 14:43:59',NULL),(3,'NJU-00001','2024-05-30','SO240530-0001','penjualan','100',0,16000,'0','-','0','2024-05-30 14:43:59',NULL),(4,'NJU-00001','2024-05-30','SO240530-0001','penjualan','64',0,0,'0','-','0','2024-05-30 14:43:59',NULL),(5,'NJU-00002','2024-05-30','SO240530-0002','penjualan','3',26600,0,'0','-','0','2024-05-30 14:53:47',NULL),(6,'NJU-00002','2024-05-30','SO240530-0002','penjualan','107',0,0,'0','-','0','2024-05-30 14:53:47',NULL),(7,'NJU-00002','2024-05-30','SO240530-0002','penjualan','100',0,26600,'0','-','0','2024-05-30 14:53:47',NULL),(8,'NJU-00002','2024-05-30','SO240530-0002','penjualan','64',0,0,'0','-','0','2024-05-30 14:53:47',NULL),(9,'NJU-00003','2024-05-31','SO240531-0001','penjualan','3',20000,0,'0','-','0','2024-05-31 10:39:17',NULL),(10,'NJU-00003','2024-05-31','SO240531-0001','penjualan','107',0,0,'0','-','0','2024-05-31 10:39:17',NULL),(11,'NJU-00003','2024-05-31','SO240531-0001','penjualan','100',0,20000,'0','-','0','2024-05-31 10:39:17',NULL),(12,'NJU-00003','2024-05-31','SO240531-0001','penjualan','64',0,0,'0','-','0','2024-05-31 10:39:17',NULL),(13,'NJU-00004','2024-06-03','SO240603-0001','penjualan','3',50000,0,'0','-','0','2024-06-03 13:39:01',NULL),(14,'NJU-00004','2024-06-03','SO240603-0001','penjualan','107',0,0,'0','-','0','2024-06-03 13:39:01',NULL),(15,'NJU-00004','2024-06-03','SO240603-0001','penjualan','100',0,50000,'0','-','0','2024-06-03 13:39:01',NULL),(16,'NJU-00004','2024-06-03','SO240603-0001','penjualan','64',0,0,'0','-','0','2024-06-03 13:39:01',NULL),(17,'NJU-00005','2024-06-03','SO240603-0002','penjualan','3',26600,0,'0','-','0','2024-06-03 13:40:58',NULL),(18,'NJU-00005','2024-06-03','SO240603-0002','penjualan','107',0,0,'0','-','0','2024-06-03 13:40:58',NULL),(19,'NJU-00005','2024-06-03','SO240603-0002','penjualan','100',0,26600,'0','-','0','2024-06-03 13:40:58',NULL),(20,'NJU-00005','2024-06-03','SO240603-0002','penjualan','64',0,0,'0','-','0','2024-06-03 13:40:58',NULL),(21,'NJU-00006','2024-06-03','SO240603-0003','penjualan','3',20000,0,'0','-','0','2024-06-03 13:41:32',NULL),(22,'NJU-00006','2024-06-03','SO240603-0003','penjualan','107',0,0,'0','-','0','2024-06-03 13:41:32',NULL),(23,'NJU-00006','2024-06-03','SO240603-0003','penjualan','100',0,20000,'0','-','0','2024-06-03 13:41:32',NULL),(24,'NJU-00006','2024-06-03','SO240603-0003','penjualan','64',0,0,'0','-','0','2024-06-03 13:41:32',NULL),(25,'NJU-00007','2024-08-08','SO240808-0001','penjualan','3',60000,0,'0','-','0','2024-08-08 10:48:53',NULL),(26,'NJU-00007','2024-08-08','SO240808-0001','penjualan','107',0,0,'0','-','0','2024-08-08 10:48:53',NULL),(27,'NJU-00007','2024-08-08','SO240808-0001','penjualan','100',0,60000,'0','-','0','2024-08-08 10:48:53',NULL),(28,'NJU-00007','2024-08-08','SO240808-0001','penjualan','64',0,0,'0','-','0','2024-08-08 10:48:53',NULL);
/*!40000 ALTER TABLE `pos_jurnal_umum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profit_setting`
--

DROP TABLE IF EXISTS `profit_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profit_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemcode` varchar(50) DEFAULT NULL,
  `profit_type` enum('persentase','nominal') DEFAULT NULL,
  `jumlah` double DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profit_setting`
--

LOCK TABLES `profit_setting` WRITE;
/*!40000 ALTER TABLE `profit_setting` DISABLE KEYS */;
INSERT INTO `profit_setting` VALUES (2,'2','nominal',10000,'2024-03-18 09:50:15',NULL),(3,'4','persentase',10,'2024-03-18 13:13:25',NULL),(4,'5','persentase',25,'2024-05-31 10:02:15',NULL);
/*!40000 ALTER TABLE `profit_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `code` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status` enum('open','clear','cancel','close') COLLATE utf8_unicode_ci NOT NULL,
  `start_project` date NOT NULL,
  `end_project` date NOT NULL,
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `user_created` bigint(20) unsigned NOT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `projects_code_unique` (`code`),
  KEY `projects_user_created_foreign` (`user_created`),
  KEY `projects_user_updated_foreign` (`user_updated`),
  CONSTRAINT `projects_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `projects_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases`
--

DROP TABLE IF EXISTS `purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases` (
  `code` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `supplier_code` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `supplier` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `date_order` date NOT NULL,
  `status` enum('pending','confirm','cancel','close') COLLATE utf8_unicode_ci NOT NULL,
  `discount` decimal(18,0) NOT NULL DEFAULT '0',
  `sub_total` decimal(18,0) NOT NULL DEFAULT '0',
  `tax` decimal(18,0) NOT NULL DEFAULT '0',
  `total` decimal(18,0) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `user_created` bigint(20) unsigned DEFAULT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `purchases_code_unique` (`code`),
  KEY `purchases_supplier_code_foreign` (`supplier_code`),
  KEY `purchases_user_created_foreign` (`user_created`),
  KEY `purchases_user_updated_foreign` (`user_updated`),
  CONSTRAINT `purchases_supplier_code_foreign` FOREIGN KEY (`supplier_code`) REFERENCES `suppliers` (`code`),
  CONSTRAINT `purchases_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `purchases_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases`
--

LOCK TABLES `purchases` WRITE;
/*!40000 ALTER TABLE `purchases` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchases_details`
--

DROP TABLE IF EXISTS `purchases_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchases_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchases_id` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `buy_price` decimal(18,0) NOT NULL DEFAULT '0',
  `sell_price` decimal(18,0) NOT NULL DEFAULT '0',
  `sub_total` decimal(18,0) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchases_details_purchases_id_foreign` (`purchases_id`),
  KEY `purchases_details_item_id_foreign` (`item_id`),
  CONSTRAINT `purchases_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `purchases_details_purchases_id_foreign` FOREIGN KEY (`purchases_id`) REFERENCES `purchases` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchases_details`
--

LOCK TABLES `purchases_details` WRITE;
/*!40000 ALTER TABLE `purchases_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchases_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requistion_details`
--

DROP TABLE IF EXISTS `requistion_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requistion_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requistion_id` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requistion_details_requistion_id_foreign` (`requistion_id`),
  KEY `requistion_details_item_id_foreign` (`item_id`),
  CONSTRAINT `requistion_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `requistion_details_requistion_id_foreign` FOREIGN KEY (`requistion_id`) REFERENCES `requistions` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requistion_details`
--

LOCK TABLES `requistion_details` WRITE;
/*!40000 ALTER TABLE `requistion_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `requistion_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requistions`
--

DROP TABLE IF EXISTS `requistions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requistions` (
  `code` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `date_request` date NOT NULL,
  `date_need` date NOT NULL,
  `project_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('clear','draft','cancel','close') COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `user_created` bigint(20) unsigned NOT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `requistions_code_unique` (`code`),
  KEY `requistions_project_id_foreign` (`project_id`),
  KEY `requistions_user_created_foreign` (`user_created`),
  KEY `requistions_user_updated_foreign` (`user_updated`),
  CONSTRAINT `requistions_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`code`),
  CONSTRAINT `requistions_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `requistions_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requistions`
--

LOCK TABLES `requistions` WRITE;
/*!40000 ALTER TABLE `requistions` DISABLE KEYS */;
/*!40000 ALTER TABLE `requistions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales` (
  `code` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `customer` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `table` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `membership_code` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_order` date NOT NULL,
  `status` enum('pending','confirm','cancel','close') COLLATE utf8_unicode_ci NOT NULL,
  `sales_category` bigint(20) unsigned DEFAULT NULL,
  `discount` decimal(18,0) NOT NULL DEFAULT '0',
  `sub_total` decimal(18,0) NOT NULL DEFAULT '0',
  `tax` decimal(18,0) NOT NULL DEFAULT '0',
  `total` decimal(18,0) NOT NULL DEFAULT '0',
  `pay` decimal(18,0) NOT NULL DEFAULT '0',
  `cashBack` decimal(18,0) NOT NULL DEFAULT '0',
  `pMethod` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `user_created` bigint(20) unsigned DEFAULT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `sales_code_unique` (`code`),
  KEY `sales_membership_code_foreign` (`membership_code`),
  KEY `sales_user_created_foreign` (`user_created`),
  KEY `sales_user_updated_foreign` (`user_updated`),
  KEY `sales_sales_category_foreign` (`sales_category`),
  CONSTRAINT `sales_membership_code_foreign` FOREIGN KEY (`membership_code`) REFERENCES `memberships` (`code`),
  CONSTRAINT `sales_sales_category_foreign` FOREIGN KEY (`sales_category`) REFERENCES `sales_categories` (`id`),
  CONSTRAINT `sales_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `sales_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES ('SO240530-0001',NULL,'5',NULL,'2024-05-30','close',NULL,0,16000,0,16000,70000,43400,'Tunai',NULL,0,1,NULL,'2024-05-30 07:43:59',NULL),('SO240530-0002',NULL,'5',NULL,'2024-05-30','close',NULL,0,26600,0,26600,50000,23400,'Tunai',NULL,0,1,NULL,'2024-05-30 07:53:47',NULL),('SO240531-0001',NULL,'2',NULL,'2024-05-31','close',NULL,0,20000,0,20000,50000,30000,'Tunai',NULL,0,1,NULL,'2024-05-31 03:39:17',NULL),('SO240603-0001',NULL,'GO',NULL,'2024-06-03','close',NULL,0,50000,0,50000,50000,0,'Tunai',NULL,0,1,NULL,'2024-06-03 06:39:01',NULL),('SO240603-0002',NULL,'GR',NULL,'2024-06-03','close',NULL,0,26600,0,26600,50000,23400,'Tunai',NULL,0,1,NULL,'2024-06-03 06:40:58',NULL),('SO240603-0003',NULL,'SP',NULL,'2024-06-03','close',NULL,0,20000,0,20000,20000,0,'Tunai',NULL,0,1,NULL,'2024-06-03 06:41:32',NULL),('SO240808-0001',NULL,'1',NULL,'2024-08-08','close',NULL,0,60000,0,60000,100000,40000,'Tunai',NULL,0,1,NULL,'2024-08-08 03:48:53',NULL);
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_categories`
--

DROP TABLE IF EXISTS `sales_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mark_up` decimal(18,0) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `user_created` bigint(20) unsigned DEFAULT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_categories_user_created_foreign` (`user_created`),
  KEY `sales_categories_user_updated_foreign` (`user_updated`),
  CONSTRAINT `sales_categories_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `sales_categories_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_categories`
--

LOCK TABLES `sales_categories` WRITE;
/*!40000 ALTER TABLE `sales_categories` DISABLE KEYS */;
INSERT INTO `sales_categories` VALUES (1,'Umum',0,NULL,1,NULL,1,0,'2021-05-12 08:58:32',NULL),(2,'Grab Food',10,NULL,1,NULL,1,0,'2021-05-12 08:58:32',NULL),(3,'Go Food',10,NULL,1,NULL,1,0,'2021-05-12 08:58:32',NULL),(4,'Shopee Food',10,NULL,1,NULL,1,0,'2021-05-12 08:58:32',NULL);
/*!40000 ALTER TABLE `sales_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_details`
--

DROP TABLE IF EXISTS `sales_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sales_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sales_id` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `buy_price` decimal(18,0) NOT NULL DEFAULT '0',
  `sell_price` decimal(18,0) NOT NULL DEFAULT '0',
  `sub_total` decimal(18,0) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `served` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_details_sales_id_foreign` (`sales_id`),
  KEY `sales_details_item_id_foreign` (`item_id`),
  CONSTRAINT `sales_details_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`),
  CONSTRAINT `sales_details_sales_id_foreign` FOREIGN KEY (`sales_id`) REFERENCES `sales` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_details`
--

LOCK TABLES `sales_details` WRITE;
/*!40000 ALTER TABLE `sales_details` DISABLE KEYS */;
INSERT INTO `sales_details` VALUES (1,'SO240530-0001',2,1,0,5000,5000,NULL,0,'2024-05-30 07:43:59',NULL),(2,'SO240530-0001',6,1,3000,5000,5000,NULL,0,'2024-05-30 07:43:59',NULL),(3,'SO240530-0001',4,1,5000,6000,6000,NULL,0,'2024-05-30 07:43:59',NULL),(4,'SO240530-0002',2,1,0,15000,15000,NULL,0,'2024-05-30 07:53:47',NULL),(5,'SO240530-0002',6,1,3000,5000,5000,NULL,0,'2024-05-30 07:53:47',NULL),(6,'SO240530-0002',4,1,5000,6600,6600,NULL,0,'2024-05-30 07:53:47',NULL),(7,'SO240531-0001',2,1,0,15000,15000,NULL,0,'2024-05-31 03:39:17',NULL),(8,'SO240531-0001',6,1,3000,5000,5000,NULL,0,'2024-05-31 03:39:17',NULL),(9,'SO240603-0001',7,1,45000,50000,50000,NULL,0,'2024-06-03 06:39:01',NULL),(10,'SO240603-0002',8,1,12000,20000,20000,NULL,0,'2024-06-03 06:40:58',NULL),(11,'SO240603-0002',4,1,5000,6600,6600,NULL,0,'2024-06-03 06:40:58',NULL),(12,'SO240603-0003',2,1,0,15000,15000,NULL,0,'2024-06-03 06:41:32',NULL),(13,'SO240603-0003',6,1,3000,5000,5000,NULL,0,'2024-06-03 06:41:32',NULL),(14,'SO240808-0001',7,1,45000,50000,50000,NULL,0,'2024-08-08 03:48:53',NULL),(15,'SO240808-0001',6,2,3000,5000,10000,NULL,0,'2024-08-08 03:48:53',NULL);
/*!40000 ALTER TABLE `sales_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `code` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `nama` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `kota` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `provinsi` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `negara` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'indonesia',
  `address` text COLLATE utf8_unicode_ci,
  `email` varchar(191) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` enum('active','suspend','close') COLLATE utf8_unicode_ci NOT NULL,
  `status_EI` tinyint(1) NOT NULL DEFAULT '0',
  `user_created` bigint(20) unsigned NOT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `suppliers_code_unique` (`code`),
  UNIQUE KEY `suppliers_mobile_unique` (`mobile`),
  KEY `suppliers_user_created_foreign` (`user_created`),
  KEY `suppliers_user_updated_foreign` (`user_updated`),
  CONSTRAINT `suppliers_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `suppliers_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units`
--

DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `status_EI` tinyint(1) NOT NULL DEFAULT '1',
  `user_created` bigint(20) unsigned NOT NULL,
  `user_updated` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_code_unique` (`code`),
  KEY `units_user_created_foreign` (`user_created`),
  KEY `units_user_updated_foreign` (`user_updated`),
  CONSTRAINT `units_user_created_foreign` FOREIGN KEY (`user_created`) REFERENCES `users` (`id`),
  CONSTRAINT `units_user_updated_foreign` FOREIGN KEY (`user_updated`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units`
--

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;
INSERT INTO `units` VALUES (1,'PCS','PIECES',1,0,1,NULL,'2022-02-12 17:00:00',NULL),(2,'PKG','PACKAGES',1,0,1,NULL,'2022-02-12 17:00:00',NULL),(3,'SET','SET',1,0,1,NULL,'2022-02-12 17:00:00',NULL);
/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `device_key` varchar(191) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `failed_login` int(11) NOT NULL DEFAULT '0',
  `last_login` datetime DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `sudo` tinyint(1) NOT NULL DEFAULT '0',
  `users_acls_id` bigint(20) unsigned DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_EI` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_users_acls_id_foreign` (`users_acls_id`),
  CONSTRAINT `users_users_acls_id_foreign` FOREIGN KEY (`users_acls_id`) REFERENCES `users_acls` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'administrator','admin','admin@coba.com','6281336367798','$2y$10$yq6d2/C.nn2mnuvugaEJcOS/OjNsFaATpn20UAyFNF5F.1JyTIwVa','',1,0,'2024-08-08 15:38:53',NULL,1,1,NULL,1,NULL,NULL),(2,'Tester','kasir1','kasir1@gmail','0837943849234','$2y$10$lbrEK7PwSuZ60Wmi3WT2deVJfZUjLomcIhAlFUbOuoP6413G5ugqu',NULL,1,0,NULL,NULL,0,2,NULL,1,'2023-09-13 03:42:34',NULL),(3,'kasir 1 e','kasir1 e','kasir1@gmail.com','08769876587','$2y$10$e614jh1GW25itewqqtCM2.3DZQVCv8dI6QKaLH9ZEtFl55gtTxBlq',NULL,1,0,'2023-09-25 11:51:19',NULL,0,2,NULL,1,'2023-09-25 04:48:24',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_acls`
--

DROP TABLE IF EXISTS `users_acls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_acls` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `application` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `company` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `master` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `purchase` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sales` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `log_apps` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dashboard` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `master_acl` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `master_category` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `master_item` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `master_sales_category` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `master_unit` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `master_user` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `master_docPrefix` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `master_coa` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `purchase_order` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `transaction_purchase` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sales_order` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `transaction_sales` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `membership` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `booking` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `purchase_report` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sales_report` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `overall_report` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `member_profile` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `member_booking` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `jurnal_umum` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cash_in` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cash_out` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pembelian` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_acls_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_acls`
--

LOCK TABLES `users_acls` WRITE;
/*!40000 ALTER TABLE `users_acls` DISABLE KEYS */;
INSERT INTO `users_acls` VALUES (0,'Super User','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudep','cr','cr','crudie'),(1,'adminstrator','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crudie','crud','','',''),(2,'Kasir 1','','','','','crudie','','crudie','','','','','','','','','','','crudie','','','','','','','','','','','','');
/*!40000 ALTER TABLE `users_acls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'pos2'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-08-09 15:46:10