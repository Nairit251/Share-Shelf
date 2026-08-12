-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2026 at 05:12 PM
-- Integrated: migration.sql + migration_fixes.sql + guest support ticket fields
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `share_shelf`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `Assigned_Date` date NOT NULL,
  PRIMARY KEY (`Admin_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Assigned_Date`) VALUES
(47, '2026-01-01'),
(48, '2026-02-10'),
(49, '2026-03-22'),
(50, '2026-05-08');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `Cart_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  PRIMARY KEY (`Cart_ID`),
  KEY `User_ID` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`Cart_ID`, `User_ID`) VALUES
(1, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 6),
(6, 8),
(7, 9),
(8, 11),
(9, 12),
(10, 13),
(11, 15),
(12, 16),
(13, 18),
(14, 19),
(15, 21),
(16, 22),
(17, 24),
(18, 26),
(19, 28),
(20, 31),
(21, 34),
(22, 37),
(23, 40),
(24, 45),
(25, 49);

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `Cart_Item_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Cart_ID` int(11) NOT NULL,
  `Item_ID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  PRIMARY KEY (`Cart_Item_ID`),
  KEY `Cart_ID` (`Cart_ID`),
  KEY `Item_ID` (`Item_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_item`
--

INSERT INTO `cart_item` (`Cart_Item_ID`, `Cart_ID`, `Item_ID`, `Quantity`) VALUES
(1, 1, 1, 1),
(2, 1, 17, 1),
(3, 2, 2, 1),
(4, 3, 4, 1),
(5, 3, 19, 1),
(6, 4, 5, 1),
(7, 5, 8, 1),
(8, 5, 29, 1),
(9, 6, 13, 1),
(10, 7, 14, 1),
(11, 7, 27, 1),
(12, 8, 15, 1),
(13, 9, 16, 1),
(14, 9, 42, 1),
(15, 10, 28, 1),
(16, 11, 31, 1),
(17, 12, 32, 1),
(18, 13, 36, 2),
(19, 14, 37, 1),
(20, 15, 41, 1),
(21, 16, 45, 1),
(22, 16, 5, 1),
(23, 17, 47, 1),
(24, 18, 50, 1),
(25, 19, 13, 1),
(26, 19, 29, 1),
(27, 20, 1, 1),
(28, 21, 16, 1),
(29, 21, 27, 1),
(30, 22, 17, 1),
(31, 23, 31, 1),
(32, 24, 36, 3),
(33, 25, 42, 1),
(34, 25, 45, 1);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `Category_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Category_Name` varchar(100) NOT NULL,
  `Parent_Category_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Category_ID`),
  KEY `Parent_Category_ID` (`Parent_Category_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Category_ID`, `Category_Name`, `Parent_Category_ID`) VALUES
(1, 'Electronics', NULL),
(2, 'Books', NULL),
(3, 'Clothing', NULL),
(4, 'Home & Kitchen', NULL),
(5, 'Sports & Outdoors', NULL),
(6, 'Beauty & Personal Care', NULL),
(7, 'Toys & Games', NULL),
(8, 'Furniture', NULL),
(9, 'Stationery', NULL),
(10, 'Medical Equipment', NULL),
(11, 'Mobile Phones', 1),
(12, 'Laptops', 1),
(13, 'Electronic Accessories', 1),
(14, 'Academic Books', 2),
(15, 'Novels', 2),
(16, 'Men\'s Clothing', 3),
(17, 'Women\'s Clothing', 3),
(18, 'Baby Clothing', 3),
(19, 'Kitchen Appliances', 4),
(20, 'Home Decor', 4),
(21, 'Fitness Equipment', 5),
(22, 'Outdoor Gear', 5),
(23, 'Makeup', 6),
(24, 'Perfumes', 6),
(25, 'Beauty Accessories', 6),
(26, 'Board Games', 7),
(27, 'Educational Toys', 7),
(28, 'Bedroom Furniture', 8),
(29, 'Office Furniture', 8),
(30, 'Mobility Aids', 10),
(31, 'Monitoring Devices', 10),
(32, 'Rehabilitation Equipment', 10);

-- --------------------------------------------------------

--
-- Table structure for table `claim`
--

CREATE TABLE `claim` (
  `Claim_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Item_ID` int(11) DEFAULT NULL,
  `User_ID` int(11) NOT NULL,
  `Claim_Date` date NOT NULL,
  `Status` varchar(50) NOT NULL,
  `Pickup_Date` date DEFAULT NULL,
  `Pickup_Time` time DEFAULT NULL,
  `Pickup_Location` varchar(100) DEFAULT NULL,
  `Pickup_Status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Claim_ID`),
  KEY `Item_ID` (`Item_ID`),
  KEY `User_ID` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `claim`
--

INSERT INTO `claim` (`Claim_ID`, `Item_ID`, `User_ID`, `Claim_Date`, `Status`, `Pickup_Date`, `Pickup_Time`, `Pickup_Location`, `Pickup_Status`) VALUES
(1, 3, 18, '2026-01-05', 'Completed', '2026-01-07', '11:00:00', 'Badda', 'Picked Up'),
(2, 3, 27, '2026-01-05', 'Rejected', NULL, NULL, 'Badda', 'Cancelled'),
(3, 3, 45, '2026-01-06', 'Rejected', NULL, NULL, 'Badda', 'Cancelled'),
(4, 6, 11, '2026-01-10', 'Pending', NULL, NULL, 'Kazla', 'Pending'),
(5, 6, 42, '2026-01-11', 'Pending', NULL, NULL, 'Kazla', 'Pending'),
(6, 9, 28, '2026-01-14', 'Approved', '2026-01-17', '15:00:00', 'Boyra', 'Pending'),
(7, 9, 5, '2026-01-15', 'Pending', NULL, NULL, 'Boyra', 'Pending'),
(8, 10, 34, '2026-01-18', 'Completed', '2026-01-20', '14:00:00', 'Daulatpur', 'Picked Up'),
(9, 10, 12, '2026-01-18', 'Rejected', NULL, NULL, 'Daulatpur', 'Cancelled'),
(10, 11, 6, '2026-01-22', 'Pending', NULL, NULL, 'Boalia', 'Pending'),
(11, 12, 39, '2026-01-25', 'Pending', NULL, NULL, 'Amberkhana', 'Pending'),
(12, 18, 2, '2026-01-28', 'Completed', '2026-01-30', '11:30:00', 'Dhanmondi', 'Picked Up'),
(13, 18, 17, '2026-01-28', 'Rejected', NULL, NULL, 'Dhanmondi', 'Cancelled'),
(14, 18, 26, '2026-01-29', 'Rejected', NULL, NULL, 'Dhanmondi', 'Cancelled'),
(15, 18, 49, '2026-01-30', 'Rejected', NULL, NULL, 'Dhanmondi', 'Cancelled'),
(16, 20, 14, '2026-02-02', 'Approved', '2026-02-05', '10:00:00', 'Sonadanga', 'Pending'),
(17, 20, 31, '2026-02-03', 'Pending', NULL, NULL, 'Sonadanga', 'Pending'),
(18, 21, 46, '2026-02-06', 'Pending', NULL, NULL, 'Ganginarpar', 'Pending'),
(19, 21, 19, '2026-02-07', 'Pending', NULL, NULL, 'Ganginarpar', 'Pending'),
(20, 22, 8, '2026-02-10', 'Pending', NULL, NULL, 'Bashundhara', 'Pending'),
(21, 22, 25, '2026-02-11', 'Pending', NULL, NULL, 'Bashundhara', 'Pending'),
(22, 23, 44, '2026-02-14', 'Pending', NULL, NULL, 'Tongi', 'Pending'),
(23, 24, 37, '2026-02-17', 'Pending', NULL, NULL, 'Jahaj Company', 'Pending'),
(24, 25, 13, '2026-02-20', 'Pending', NULL, NULL, 'Kotbari', 'Pending'),
(25, 25, 32, '2026-02-21', 'Pending', NULL, NULL, 'Kotbari', 'Pending'),
(26, 30, 7, '2026-02-25', 'Completed', '2026-02-27', '17:00:00', 'Maijdee', 'Picked Up'),
(27, 30, 18, '2026-02-26', 'Rejected', NULL, NULL, 'Maijdee', 'Cancelled'),
(28, 30, 40, '2026-02-27', 'Rejected', NULL, NULL, 'Maijdee', 'Cancelled'),
(29, 33, 1, '2026-03-01', 'Approved', '2026-03-03', '09:30:00', 'Board Bazar', 'Pending'),
(30, 33, 22, '2026-03-02', 'Pending', NULL, NULL, 'Board Bazar', 'Pending'),
(31, 34, 15, '2026-03-04', 'Pending', NULL, NULL, 'Modern', 'Pending'),
(32, 34, 43, '2026-03-05', 'Pending', NULL, NULL, 'Modern', 'Pending'),
(33, 35, 21, '2026-03-08', 'Pending', NULL, NULL, 'Paltan', 'Pending'),
(34, 35, 30, '2026-03-09', 'Pending', NULL, NULL, 'Paltan', 'Pending'),
(35, 35, 47, '2026-03-10', 'Pending', NULL, NULL, 'Paltan', 'Pending'),
(36, 38, 23, '2026-03-12', 'Pending', NULL, NULL, 'Moulvibazar', 'Pending'),
(37, 38, 16, '2026-03-13', 'Pending', NULL, NULL, 'Moulvibazar', 'Pending'),
(38, 43, 10, '2026-03-16', 'Pending', NULL, NULL, 'Boyra', 'Pending'),
(39, 43, 36, '2026-03-17', 'Pending', NULL, NULL, 'Boyra', 'Pending'),
(40, 44, 24, '2026-03-20', 'Pending', NULL, NULL, 'Zindabazar', 'Pending'),
(41, 46, 33, '2026-03-23', 'Pending', NULL, NULL, 'Charpara', 'Pending'),
(42, 48, 20, '2026-03-26', 'Approved', '2026-03-28', '09:00:00', 'Monihar', 'Pending'),
(43, 48, 29, '2026-03-27', 'Pending', NULL, NULL, 'Monihar', 'Pending'),
(44, 49, 4, '2026-03-30', 'Pending', NULL, NULL, 'Zindabazar', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `Item_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  `Category_ID` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Condition` varchar(50) NOT NULL,
  `Item_Type` varchar(50) NOT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `Pickup_Location` varchar(100) DEFAULT NULL,
  `Status` varchar(50) NOT NULL,
  `Approval_Status` varchar(50) NOT NULL,
  `Admin_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Item_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `Category_ID` (`Category_ID`),
  KEY `fk_item_admin` (`Admin_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`Item_ID`, `User_ID`, `Category_ID`, `Title`, `Description`, `Condition`, `Item_Type`, `Price`, `Quantity`, `Pickup_Location`, `Status`, `Approval_Status`, `Admin_ID`) VALUES
(1, 11, 12, 'Dell Inspiron 15 Laptop', 'Intel Core i5, 8GB RAM, 256GB SSD. Minor scratches. Includes original charger.', 'Good', 'Sale', 35000.00, 1, 'Rampura', 'Available', 'Approved', 47),
(2, 3, 11, 'Samsung Galaxy A32', '64GB, 6GB RAM. Includes charger and protective case.', 'Like New', 'Sale', 18000.00, 1, 'Agrabad', 'Available', 'Approved', 47),
(3, 21, 13, 'Logitech M185 Wireless Mouse', 'Unused. Original packaging included.', 'New', 'Donation', 0.00, 1, 'Badda', 'Unavailable', 'Approved', 47),
(4, 47, 12, 'HP Pavilion 14 Laptop', 'Intel Core i5, 8GB RAM, 512GB SSD. Includes charger.', 'Good', 'Sale', 32000.00, 1, 'Bashundhara', 'Available', 'Approved', 47),
(5, 15, 14, 'Engineering Mathematics by Kreyszig', '10th Edition. No missing pages. Some highlighted sections.', 'Good', 'Sale', 450.00, 1, 'Kotwali', 'Available', 'Approved', 47),
(6, 23, 14, 'HSC Physics 2nd Paper', 'Latest edition. Excellent condition with clean pages.', 'Like New', 'Donation', 0.00, 1, 'Kazla', 'Available', 'Approved', 47),
(7, 18, 15, 'Harry Potter and the Philosopher\'s Stone', 'Paperback edition. Pages are clean.', 'Like New', 'Sale', 300.00, 1, 'Pabna Sadar', 'Reserved', 'Approved', 47),
(8, 16, 16, 'Men\'s Denim Jacket', 'Blue denim jacket. Size: L. No tears or stains.', 'Good', 'Sale', 800.00, 1, 'Mohammadpur', 'Available', 'Approved', 47),
(9, 22, 17, 'Women\'s Cotton Kurti', 'Maroon cotton kurti. Size: M. Worn only twice.', 'Like New', 'Donation', 0.00, 1, 'Boyra', 'Reserved', 'Approved', 47),
(10, 30, 18, 'Baby Winter Jacket', 'Warm jacket for babies aged 1-2 years. Size: 90 cm.', 'Good', 'Donation', 0.00, 1, 'Daulatpur', 'Unavailable', 'Approved', 47),
(11, 4, 19, 'Philips Rice Cooker', '1.8L capacity. Fully functional with inner pot.', 'Good', 'Donation', 0.00, 1, 'Boalia', 'Available', 'Approved', 47),
(12, 20, 20, 'Modern Table Lamp', 'LED study lamp with adjustable brightness.', 'Like New', 'Donation', 0.00, 1, 'Amberkhana', 'Unavailable', 'Pending', NULL),
(13, 7, 21, 'Decathlon Yoga Mat', '6 mm thick exercise mat. Clean and well maintained.', 'Good', 'Sale', 750.00, 1, 'Barishal Sadar', 'Available', 'Approved', 48),
(14, 35, 23, 'Maybelline Fit Me Foundation', 'Shade 128 Warm Nude. Unopened. Purchased wrong shade.', 'New', 'Sale', 700.00, 1, 'Nathullabad', 'Available', 'Approved', 48),
(15, 47, 24, 'Lattafa Yara Eau de Parfum', '100 ml sealed bottle. Original packaging included.', 'New', 'Sale', 2600.00, 1, 'Bashundhara', 'Available', 'Approved', 48),
(16, 2, 11, 'iPhone SE (2020)', '64GB, Black. Includes original charger and box. Battery health 87%.', 'Good', 'Sale', 22000.00, 1, 'Mirpur', 'Available', 'Approved', 48),
(17, 13, 13, 'JBL Tune 510BT Headphones', 'Wireless Bluetooth headphones. Includes charging cable.', 'Like New', 'Sale', 3200.00, 1, 'Fatullah', 'Available', 'Approved', 48),
(18, 1, 15, 'The Alchemist', 'Paperback novel. Excellent condition with no torn pages.', 'Like New', 'Donation', 0.00, 1, 'Dhanmondi', 'Unavailable', 'Approved', 48),
(19, 27, 14, 'Calculus Early Transcendentals', '8th Edition. Engineering textbook with minimal highlighting.', 'Good', 'Sale', 850.00, 1, 'Agrabad', 'Available', 'Approved', 48),
(20, 5, 16, 'Men\'s Formal Blazer', 'Navy blue. Size: XL. Worn twice for formal events.', 'Like New', 'Donation', 0.00, 1, 'Sonadanga', 'Reserved', 'Approved', 48),
(21, 10, 17, 'Women\'s Winter Shawl', 'Soft wool shawl. Color: Beige. Suitable for winter.', 'Good', 'Donation', 0.00, 1, 'Ganginarpar', 'Available', 'Approved', 48),
(22, 33, 18, 'Baby Cotton Romper Set', 'Three-piece romper set. Size: 6–9 months. Washed and neatly packed.', 'Like New', 'Donation', 0.00, 1, 'Bashundhara', 'Available', 'Approved', 48),
(23, 12, 19, 'Prestige Electric Kettle', '1.5L electric kettle. Works perfectly.', 'Good', 'Donation', 0.00, 1, 'Tongi', 'Available', 'Approved', 48),
(24, 9, 20, 'Wooden Wall Clock', 'Classic wooden design. Diameter: 12 inches.', 'Like New', 'Donation', 0.00, 1, 'Jahaj Company', 'Available', 'Approved', 48),
(25, 31, 21, 'Yonex Badminton Racket', 'Lightweight racket with carrying cover.', 'Good', 'Donation', 0.00, 1, 'Kotbari', 'Available', 'Approved', 48),
(26, 36, 22, 'Camping Sleeping Bag', 'Single-person sleeping bag. Suitable for mild weather.', 'Like New', 'Sale', 2500.00, 1, 'Chelopara', 'Unavailable', 'Pending', NULL),
(27, 6, 23, 'MAC Studio Fix Powder Foundation', 'Shade NC25. Factory sealed and unopened.', 'New', 'Sale', 3800.00, 1, 'Zindabazar', 'Available', 'Approved', 49),
(28, 50, 24, 'Davidoff Cool Water Eau de Toilette', '125ml. Original sealed bottle.', 'New', 'Sale', 4200.00, 1, 'Sylhet', 'Available', 'Approved', 49),
(29, 44, 25, 'Real Techniques Makeup Brush Set', 'Unused 5-piece makeup brush set.', 'New', 'Sale', 1500.00, 1, 'Khilgaon', 'Available', 'Approved', 49),
(30, 17, 26, 'Monopoly Classic Board Game', 'Complete set with all cards and tokens.', 'Good', 'Donation', 0.00, 1, 'Maijdee', 'Unavailable', 'Approved', 49),
(31, 8, 28, 'Wooden Study Desk', 'Engineered wood study desk (120 x 60 cm). Minor scratches. Easy to assemble.', 'Good', 'Sale', 4500.00, 1, 'Shib Bari', 'Available', 'Approved', 49),
(32, 24, 29, 'Ergonomic Office Chair', 'Black mesh office chair with adjustable height. Comfortable and sturdy.', 'Like New', 'Sale', 3500.00, 1, 'Sonapur', 'Available', 'Approved', 49),
(33, 40, 28, 'Bookshelf', 'Three-tier wooden bookshelf. Suitable for books and decor items.', 'Good', 'Donation', 0.00, 1, 'Board Bazar', 'Reserved', 'Approved', 49),
(34, 32, 9, 'Casio fx-991ES Plus Scientific Calculator', 'Original calculator with protective cover. Ideal for HSC and university students.', 'Like New', 'Donation', 0.00, 1, 'Modern', 'Available', 'Approved', 49),
(35, 25, 9, 'A4 File Organizer', 'Expandable file organizer with 13 pockets. Used lightly.', 'Good', 'Donation', 0.00, 1, 'Paltan', 'Available', 'Approved', 49),
(36, 14, 9, 'Spiral Notebook Set', 'Pack of 5 ruled notebooks (A4 size). Unused.', 'New', 'Sale', 450.00, 5, 'Rupatoli', 'Available', 'Approved', 49),
(37, 41, 30, 'Omron Blood Pressure Monitor', 'Digital BP monitor with cuff and storage pouch. Fully functional.', 'Like New', 'Sale', 2800.00, 1, 'Madhabdi', 'Available', 'Approved', 49),
(38, 19, 31, 'Aluminum Walking Crutches', 'Adjustable height. Lightweight and suitable for adults.', 'Good', 'Donation', 0.00, 1, 'Moulvibazar', 'Available', 'Approved', 49),
(39, 42, 32, 'Tynor Knee Support Brace', 'Size: Large. Washable and suitable for knee support during recovery.', 'New', 'Sale', 900.00, 1, 'Bahadur Bazar', 'Unavailable', 'Pending', NULL),
(40, 43, 31, 'Accu-Chek Active Glucometer', 'Includes carrying case and test strip container (without strips).', 'Good', 'Sale', 1800.00, 1, 'Tilagor', 'Unavailable', 'Approved', 50),
(41, 26, 11, 'Google Pixel 6', '128GB storage. Includes original charger and transparent case.', 'Like New', 'Sale', 32000.00, 1, 'Rampura', 'Available', 'Approved', 50),
(42, 48, 13, 'Anker 20000mAh Power Bank', 'Supports fast charging. Includes USB-C cable.', 'Like New', 'Sale', 2800.00, 1, 'Kotwali', 'Available', 'Approved', 50),
(43, 45, 15, 'Atomic Habits by James Clear', 'Paperback edition. Pages are clean with no markings.', 'Like New', 'Donation', 0.00, 1, 'Boyra', 'Available', 'Approved', 50),
(44, 50, 17, 'Women\'s Winter Hoodie', 'Color: Black. Size: L. Worn only a few times.', 'Good', 'Donation', 0.00, 1, 'Zindabazar', 'Available', 'Approved', 50),
(45, 39, 20, 'Decorative Wall Mirror', 'Round wall mirror (18-inch). Frame in excellent condition.', 'Like New', 'Sale', 1200.00, 1, 'Sonapur', 'Available', 'Approved', 50),
(46, 34, 22, 'Quechua Hiking Backpack', '30L hiking backpack with rain cover included.', 'Good', 'Donation', 0.00, 1, 'Charpara', 'Available', 'Approved', 50),
(47, 49, 24, 'Lattafa Khamrah Eau de Parfum', '100ml sealed bottle. Authentic and unopened.', 'New', 'Sale', 4200.00, 1, 'Panchlaish', 'Available', 'Approved', 50),
(48, 37, 27, 'LEGO Classic Creative Bricks', 'Complete set with original storage box.', 'Like New', 'Donation', 0.00, 1, 'Monihar', 'Reserved', 'Approved', 50),
(49, 38, 19, 'Panasonic Microwave Oven', '20L capacity. Fully functional with user manual.', 'Good', 'Donation', 0.00, 1, 'Zindabazar', 'Unavailable', 'Pending', NULL),
(50, 47, 12, 'Lenovo IdeaPad Slim 3', 'Intel Core i5, 8GB RAM, 512GB SSD. Includes original charger.', 'Like New', 'Sale', 42000.00, 1, 'Bashundhara', 'Available', 'Approved', 50),
(51, 11, 11, 'Samsung Galaxy S21', '128GB, Phantom Gray. Includes original charger.', 'Like New', 'Sale', 28000.00, 1, 'Mirpur', 'Unavailable', 'Pending', NULL),
(52, 28, 23, 'Maybelline Fit Me Foundation', 'Opened foundation bottle with approximately 40% product remaining.', 'Used', 'Donation', 0.00, 1, 'Dhanmondi', 'Unavailable', 'Rejected', 48),
(53, 18, 14, 'Data Structures and Algorithms', '3rd Edition academic textbook. Clean pages with minimal highlighting. Suitable for CSE students.', 'Like New', 'Sale', 650.00, 1, 'Uttara', 'Available', 'Approved', 49),
(54, 41, 30, 'Beurer Digital Thermometer', 'Unused digital thermometer with original box. Quantity: 2 units available.', 'New', 'Donation', 0.00, 2, 'Mohammadpur', 'Reserved', 'Approved', 50),
(55, 6, 19, 'Philips Rice Cooker', 'Heating plate is damaged and exposed wiring is visible.', 'Poor', 'Donation', 0.00, 1, 'Bashundhara', 'Unavailable', 'Rejected', 47),
(56, 22, 21, 'Adjustable Dumbbell Set', 'One of the weight plates has a large crack and does not lock securely onto the handle.', 'Poor', 'Donation', 0.00, 1, 'Gulshan', 'Unavailable', 'Rejected', 47),
(57, 15, 25, 'Used Makeup Brush Set', 'Brushes have been used multiple times and show cosmetic residue.', 'Used', 'Donation', 0.00, 1, 'Dhanmondi', 'Unavailable', 'Rejected', 48),
(58, 34, 11, 'iPhone 13 (Activation Locked)', 'Phone is locked with the previous owner\'s Apple ID and cannot be activated.', 'Good', 'Sale', 25000.00, 1, 'Uttara', 'Unavailable', 'Rejected', 49),
(59, 8, 20, 'Broken Glass Coffee Table', 'Glass top has multiple large cracks and sharp edges.', 'Poor', 'Donation', 0.00, 1, 'Mirpur', 'Unavailable', 'Rejected', 50),
(60, 27, 22, 'Torn Camping Tent', 'Large tears in the fabric and broken support poles make the tent unusable.', 'Poor', 'Donation', 0.00, 1, 'Sylhet', 'Unavailable', 'Rejected', 47);

-- --------------------------------------------------------

--
-- Table structure for table `item_image`
--

CREATE TABLE `item_image` (
  `Image_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Item_ID` int(11) NOT NULL,
  `Image_URL` varchar(500) NOT NULL,
  `Is_Primary` tinyint(1) NOT NULL,
  PRIMARY KEY (`Image_ID`),
  KEY `Item_ID` (`Item_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_image`
--

INSERT INTO `item_image` (`Image_ID`, `Item_ID`, `Image_URL`, `Is_Primary`) VALUES
(1, 1, 'images/items/dell_inspiron15_front.jpg', 1),
(2, 1, 'images/items/dell_inspiron15_keyboard.jpg', 0),
(3, 1, 'images/items/dell_inspiron15_back.jpg', 0),
(4, 2, 'images/items/samsung_galaxy_a32_front.jpg', 1),
(5, 2, 'images/items/samsung_galaxy_a32_back.jpg', 0),
(6, 3, 'images/items/logitech_m185.jpg', 1),
(7, 4, 'images/items/hp_pavilion14_front.jpg', 1),
(8, 4, 'images/items/hp_pavilion14_keyboard.jpg', 0),
(9, 4, 'images/items/hp_pavilion14_side.jpg', 0),
(10, 5, 'images/items/engineering_mathematics_book.jpg', 1),
(11, 6, 'images/items/hsc_physics_book.jpg', 1),
(12, 7, 'images/items/harry_potter_book.jpg', 1),
(13, 8, 'images/items/mens_denim_jacket_front.jpg', 1),
(14, 8, 'images/items/mens_denim_jacket_back.jpg', 0),
(15, 9, 'images/items/womens_cotton_kurti.jpg', 1),
(16, 10, 'images/items/baby_winter_jacket.jpg', 1),
(17, 11, 'images/items/philips_rice_cooker_front.jpg', 1),
(18, 11, 'images/items/philips_rice_cooker_inside.jpg', 0),
(19, 12, 'images/items/modern_table_lamp.jpg', 1),
(20, 13, 'images/items/decathlon_yoga_mat.jpg', 1),
(21, 14, 'images/items/maybelline_fitme_foundation.jpg', 1),
(22, 15, 'images/items/lattafa_yara_box.jpg', 1),
(23, 15, 'images/items/lattafa_yara_bottle.jpg', 0),
(24, 16, 'images/items/iphone_se_front.jpg', 1),
(25, 16, 'images/items/iphone_se_back.jpg', 0),
(26, 17, 'images/items/jbl_tune510bt.jpg', 1),
(27, 18, 'images/items/the_alchemist_book.jpg', 1),
(28, 19, 'images/items/calculus_book.jpg', 1),
(29, 20, 'images/items/mens_formal_blazer.jpg', 1),
(30, 21, 'images/items/womens_winter_shawl.jpg', 1),
(31, 22, 'images/items/baby_romper_set.jpg', 1),
(32, 23, 'images/items/prestige_electric_kettle.jpg', 1),
(33, 24, 'images/items/wooden_wall_clock.jpg', 1),
(34, 25, 'images/items/yonex_badminton_racket_front.jpg', 1),
(35, 25, 'images/items/yonex_badminton_racket_cover.jpg', 0),
(36, 26, 'images/items/camping_sleeping_bag.jpg', 1),
(37, 27, 'images/items/mac_studio_fix_powder.jpg', 1),
(38, 28, 'images/items/davidoff_coolwater_box.jpg', 1),
(39, 28, 'images/items/davidoff_coolwater_bottle.jpg', 0),
(40, 29, 'images/items/real_techniques_brush_set.jpg', 1),
(41, 30, 'images/items/monopoly_classic.jpg', 1),
(42, 31, 'images/items/wooden_study_desk_front.jpg', 1),
(43, 31, 'images/items/wooden_study_desk_top.jpg', 0),
(44, 32, 'images/items/office_chair_front.jpg', 1),
(45, 32, 'images/items/office_chair_back.jpg', 0),
(46, 33, 'images/items/bookshelf.jpg', 1),
(47, 34, 'images/items/casio_fx991es_plus.jpg', 1),
(48, 35, 'images/items/file_organizer.jpg', 1),
(49, 36, 'images/items/spiral_notebook_set.jpg', 1),
(50, 37, 'images/items/omron_bp_monitor.jpg', 1),
(51, 38, 'images/items/aluminum_crutches.jpg', 1),
(52, 39, 'images/items/tynor_knee_brace.jpg', 1),
(53, 40, 'images/items/accuchek_glucometer.jpg', 1),
(54, 41, 'images/items/google_pixel6_front.jpg', 1),
(55, 41, 'images/items/google_pixel6_back.jpg', 0),
(56, 42, 'images/items/anker_powerbank.jpg', 1),
(57, 43, 'images/items/atomic_habits_book.jpg', 1),
(58, 44, 'images/items/womens_winter_hoodie.jpg', 1),
(59, 45, 'images/items/decorative_wall_mirror.jpg', 1),
(60, 46, 'images/items/quechua_hiking_backpack.jpg', 1),
(61, 47, 'images/items/lattafa_khamrah_box.jpg', 1),
(62, 47, 'images/items/lattafa_khamrah_bottle.jpg', 0),
(63, 48, 'images/items/lego_classic_bricks.jpg', 1),
(64, 49, 'images/items/panasonic_microwave.jpg', 1),
(65, 50, 'images/items/lenovo_ideapad_slim3_front.jpg', 1),
(66, 50, 'images/items/lenovo_ideapad_slim3_keyboard.jpg', 0),
(67, 51, 'images/items/samsung_galaxy_s21.jpg', 1),
(68, 52, 'images/items/maybelline_fit_me_foundation.jpg', 1),
(69, 53, 'images/items/data_structures_algorithms.jpg', 1),
(70, 54, 'images/items/beurer_digital_thermometer.jpg', 1),
(71, 55, 'images/items/philips_rice_cooker_rejected.jpg', 1),
(72, 56, 'images/items/adjustable_dumbbell_set.jpg', 1),
(73, 57, 'images/items/used_makeup_brush_set.jpg', 1),
(74, 58, 'images/items/iphone13_activation_locked.jpg', 1),
(75, 59, 'images/items/broken_glass_coffee_table.jpg', 1),
(76, 60, 'images/items/torn_camping_tent.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Purchase_ID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Payment_Method` varchar(50) NOT NULL,
  `Payment_Status` varchar(50) NOT NULL,
  `Payment_Date` date NOT NULL,
  PRIMARY KEY (`Payment_ID`),
  KEY `Purchase_ID` (`Purchase_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`Payment_ID`, `Purchase_ID`, `Amount`, `Payment_Method`, `Payment_Status`, `Payment_Date`) VALUES
(1, 1, 35000.00, 'Bank Transfer', 'Paid', '2026-01-08'),
(2, 2, 18000.00, 'bKash', 'Paid', '2026-01-15'),
(3, 3, 450.00, 'COD', 'Paid', '2026-01-22'),
(4, 4, 800.00, 'Nagad', 'Paid', '2026-01-28'),
(5, 5, 4050.00, 'bKash', 'Paid', '2026-02-03'),
(6, 6, 700.00, 'COD', 'Pending', '2026-02-06'),
(7, 7, 2600.00, 'Bank Transfer', 'Paid', '2026-02-12'),
(8, 8, 2500.00, 'bKash', 'Pending', '2026-02-18'),
(9, 9, 2800.00, 'Nagad', 'Paid', '2026-02-22'),
(10, 10, 4200.00, 'bKash', 'Paid', '2026-03-01'),
(11, 11, 1500.00, 'COD', 'Paid', '2026-03-06'),
(12, 12, 1200.00, 'Bank Transfer', 'Pending', '2026-03-10'),
(13, 13, 4200.00, 'bKash', 'Paid', '2026-03-16'),
(14, 14, 42000.00, 'Card', 'Pending', '2026-03-20'),
(15, 15, 4850.00, 'Nagad', 'Paid', '2026-03-27');

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `Purchase_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Buyer_ID` int(11) NOT NULL,
  `Purchase_Date` date NOT NULL,
  `Total_Amount` decimal(10,2) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `Pickup_Date` date DEFAULT NULL,
  `Pickup_Time` time DEFAULT NULL,
  `Pickup_Location` varchar(100) DEFAULT NULL,
  `Pickup_Status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Purchase_ID`),
  KEY `Buyer_ID` (`Buyer_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase`
--

INSERT INTO `purchase` (`Purchase_ID`, `Buyer_ID`, `Purchase_Date`, `Total_Amount`, `Status`, `Pickup_Date`, `Pickup_Time`, `Pickup_Location`, `Pickup_Status`) VALUES
(1, 2, '2026-01-08', 35000.00, 'Completed', '2026-01-10', '11:00:00', 'Rampura', 'Picked Up'),
(2, 8, '2026-01-15', 18000.00, 'Completed', '2026-01-17', '15:30:00', 'Agrabad', 'Picked Up'),
(3, 5, '2026-01-22', 450.00, 'Completed', '2026-01-24', '10:00:00', 'Kotwali', 'Picked Up'),
(4, 12, '2026-01-28', 800.00, 'Completed', '2026-01-30', '16:00:00', 'Mohammadpur', 'Picked Up'),
(5, 17, '2026-02-03', 4050.00, 'Completed', '2026-02-05', '12:00:00', 'Fatullah', 'Picked Up'),
(6, 21, '2026-02-08', 700.00, 'Reserved', '2026-02-11', '14:30:00', 'Nathullabad', 'Pending'),
(7, 24, '2026-02-12', 2600.00, 'Completed', '2026-02-14', '13:00:00', 'Bashundhara', 'Picked Up'),
(8, 29, '2026-02-17', 2500.00, 'Pending', NULL, NULL, 'Chelopara', 'Pending'),
(9, 31, '2026-02-22', 2800.00, 'Completed', '2026-02-24', '10:30:00', 'Madhabdi', 'Picked Up'),
(10, 35, '2026-03-01', 4200.00, 'Completed', '2026-03-03', '15:00:00', 'Sylhet', 'Picked Up'),
(11, 39, '2026-03-06', 1500.00, 'Completed', '2026-03-08', '11:30:00', 'Khilgaon', 'Picked Up'),
(12, 41, '2026-03-11', 1200.00, 'Reserved', '2026-03-14', '16:30:00', 'Sonapur', 'Pending'),
(13, 44, '2026-03-16', 4200.00, 'Completed', '2026-03-18', '12:30:00', 'Panchlaish', 'Picked Up'),
(14, 46, '2026-03-22', 42000.00, 'Pending', NULL, NULL, 'Bashundhara', 'Pending'),
(15, 50, '2026-03-27', 4850.00, 'Completed', '2026-03-29', '10:00:00', 'Rupatoli', 'Picked Up');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_item`
--

CREATE TABLE `purchase_item` (
  `Purchase_Item_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Purchase_ID` int(11) NOT NULL,
  `Item_ID` int(11) DEFAULT NULL,
  `Quantity` int(11) NOT NULL,
  `Unit_Price` decimal(10,2) NOT NULL,
  `Seller_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Purchase_Item_ID`),
  KEY `Purchase_ID` (`Purchase_ID`),
  KEY `Item_ID` (`Item_ID`),
  KEY `Seller_ID` (`Seller_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_item`
--

INSERT INTO `purchase_item` (`Purchase_Item_ID`, `Purchase_ID`, `Item_ID`, `Quantity`, `Unit_Price`, `Seller_ID`) VALUES
(1, 1, 1, 1, 35000.00, 11),
(2, 2, 2, 1, 18000.00, 3),
(3, 3, 5, 1, 450.00, 15),
(4, 4, 8, 1, 800.00, 16),
(5, 5, 17, 1, 3200.00, 13),
(6, 5, 19, 1, 850.00, 27),
(7, 6, 14, 1, 700.00, 35),
(8, 7, 15, 1, 2600.00, 47),
(9, 8, 26, 1, 2500.00, 36),
(10, 9, 37, 1, 2800.00, 41),
(11, 10, 28, 1, 4200.00, 50),
(12, 11, 29, 1, 1500.00, 44),
(13, 12, 45, 1, 1200.00, 39),
(14, 13, 47, 1, 4200.00, 49),
(15, 14, 50, 1, 42000.00, 47),
(16, 15, 36, 3, 450.00, 14),
(17, 15, 32, 1, 3500.00, 24);

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE `rating` (
  `Rating_ID` int(11) NOT NULL AUTO_INCREMENT,
  `From_User_ID` int(11) NOT NULL,
  `To_User_ID` int(11) NOT NULL,
  `Score` int(11) NOT NULL,
  `Comment` varchar(255) DEFAULT NULL,
  `Rating_Date` date NOT NULL,
  PRIMARY KEY (`Rating_ID`),
  KEY `From_User_ID` (`From_User_ID`),
  KEY `To_User_ID` (`To_User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating`
--

INSERT INTO `rating` (`Rating_ID`, `From_User_ID`, `To_User_ID`, `Score`, `Comment`, `Rating_Date`) VALUES
(1, 2, 11, 5, 'Very friendly seller.', '2026-01-10'),
(2, 8, 3, 5, 'Smooth transaction.', '2026-01-17'),
(3, 5, 15, 4, 'Book was in good condition.', '2026-01-24'),
(4, 12, 16, 5, 'Exactly as described.', '2026-01-30'),
(5, 17, 13, 5, 'Highly recommended.', '2026-02-05'),
(6, 24, 47, 5, 'Authentic perfume.', '2026-02-14'),
(7, 31, 41, 4, 'Item received on time.', '2026-02-24'),
(8, 35, 50, 5, 'Great communication.', '2026-03-03'),
(9, 39, 44, 4, 'Good experience overall.', '2026-03-08'),
(10, 44, 49, 5, 'Very cooperative seller.', '2026-03-18'),
(11, 21, 30, 5, 'Thank you for the donation.', '2026-02-27'),
(12, 46, 10, 5, 'Very kind donor.', '2026-02-08'),
(13, 28, 22, 4, 'Quick approval process.', '2026-01-17'),
(14, 14, 5, 5, 'Excellent experience.', '2026-02-05'),
(15, 23, 33, 5, 'Helpful and responsive.', '2026-03-15'),
(16, 18, 1, 5, 'Book was exactly as described.', '2026-01-30'),
(17, 37, 9, 4, 'Pickup was easy.', '2026-02-19'),
(18, 7, 17, 5, 'Would recommend.', '2026-02-27'),
(19, 20, 37, 5, 'Very polite donor.', '2026-03-28'),
(20, 50, 14, 5, 'Everything went perfectly.', '2026-03-29');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `Report_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  `Item_ID` int(11) DEFAULT NULL,
  `Reason` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Status` varchar(50) NOT NULL,
  `Admin_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Report_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `Item_ID` (`Item_ID`),
  KEY `fk_report_admin` (`Admin_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`Report_ID`, `User_ID`, `Item_ID`, `Reason`, `Description`, `Status`, `Admin_ID`) VALUES
(1, 8, 14, 'Misleading Description', 'The product condition does not match the description provided.', 'Resolved', 47),
(2, 17, 29, 'Duplicate Listing', 'This item appears to have been listed more than once.', 'Resolved', 47),
(3, 24, 26, 'Seller Unresponsive', 'The seller did not respond after expressing interest.', 'Pending', NULL),
(4, 35, 45, 'Inappropriate Content', 'The uploaded description contains inappropriate language.', 'Resolved', 48),
(5, 19, 39, 'Incorrect Category', 'The item is listed under the wrong category.', 'Pending', NULL),
(6, 11, 31, 'Spam Listing', 'This listing appears to be spam or not genuine.', 'Resolved', 47),
(7, 28, 18, 'Damaged Item', 'The received item was more damaged than described.', 'Under Review', 49),
(8, 42, 50, 'Price Manipulation', 'The seller changed the agreed price before pickup.', 'Pending', NULL),
(9, 30, 23, 'Fake Product', 'The item may not be authentic as claimed.', 'Resolved', 48),
(10, 46, 5, 'Other', 'Please verify the listing details.', 'Under Review', 49);

-- --------------------------------------------------------

--
-- Table structure for table `support_ticket`
--

CREATE TABLE `support_ticket` (
  `Ticket_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) DEFAULT NULL,
  `Contact_Name` varchar(100) DEFAULT NULL,
  `Contact_Email` varchar(255) DEFAULT NULL,
  `Contact_Phone` varchar(15) DEFAULT NULL,
  `Subject` varchar(100) NOT NULL,
  `Message` varchar(255) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `Reply` varchar(500) DEFAULT NULL,
  `Reply_Date` datetime DEFAULT NULL,
  `Admin_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Ticket_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `fk_ticket_admin` (`Admin_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_ticket`
--

INSERT INTO `support_ticket` (`Ticket_ID`, `User_ID`, `Contact_Name`, `Contact_Email`, `Contact_Phone`, `Subject`, `Message`, `Status`, `Reply`, `Reply_Date`, `Admin_ID`) VALUES
(1, 5, NULL, NULL, NULL, 'Unable to Login', 'I forgot my password and cannot access my account.', 'Resolved', NULL, NULL, 47),
(2, 18, NULL, NULL, NULL, 'Claim Not Updating', 'My claim status has not changed for several days.', 'Pending', NULL, NULL, NULL),
(3, 24, NULL, NULL, NULL, 'Payment Issue', 'Payment was deducted but not reflected in my purchase.', 'Resolved', NULL, NULL, 47),
(4, 31, NULL, NULL, NULL, 'Edit Item', 'I cannot update my item information after posting.', 'Resolved', NULL, NULL, 48),
(5, 39, NULL, NULL, NULL, 'Pickup Time Change', 'I would like to reschedule the pickup time.', 'Pending', NULL, NULL, NULL),
(6, 42, NULL, NULL, NULL, 'Report Follow-up', 'I would like an update regarding my submitted report.', 'In Progress', NULL, NULL, 49),
(7, 47, NULL, NULL, NULL, 'Image Upload Error', 'Images fail to upload while creating a listing.', 'Resolved', NULL, NULL, 48),
(8, 50, NULL, NULL, NULL, 'Account Verification', 'Please verify my account so I can list more items.', 'Pending', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `First_Name` varchar(50) NOT NULL,
  `Last_Name` varchar(50) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Phone` varchar(15) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `District` varchar(50) NOT NULL,
  `Area` varchar(50) NOT NULL,
  `Street` varchar(50) NOT NULL,
  `Is_Banned` tinyint(1) NOT NULL DEFAULT 0,
  `Is_Deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`User_ID`),
  UNIQUE KEY `uq_user_phone` (`Phone`),
  UNIQUE KEY `uq_user_email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`User_ID`, `First_Name`, `Last_Name`, `Email`, `Phone`, `Password`, `District`, `Area`, `Street`) VALUES
(1, 'Ayesha', 'Rahman', 'ayesha.rahman01@gmail.com', '01710000001', 'Ar@012345!', 'Dhaka', 'Dhanmondi', 'Road 5'),
(2, 'Nusrat', 'Jahan', 'nusrat.jahan02@gmail.com', '01710000002', 'Nj@012345!', 'Dhaka', 'Mirpur', 'Section 10'),
(3, 'Tanvir', 'Hasan', 'tanvir.hasan03@gmail.com', '01710000003', 'Th@012345!', 'Chattogram', 'Panchlaish', 'Road 2'),
(4, 'Sadia', 'Islam', 'sadia.islam04@gmail.com', '01710000004', 'Si@012345!', 'Rajshahi', 'Boalia', 'Court Road'),
(5, 'Mehedi', 'Hasan', 'mehedi.hasan05@gmail.com', '01710000005', 'Mh@012345!', 'Khulna', 'Sonadanga', 'Road 8'),
(6, 'Farzana', 'Akter', 'farzana.akter06@gmail.com', '01710000006', 'Fa@012345!', 'Sylhet', 'Zindabazar', 'Station Road'),
(7, 'Rakib', 'Hossain', 'rakib.hossain07@gmail.com', '01710000007', 'Rh@012345!', 'Barishal', 'Sadar', 'College Road'),
(8, 'Mim', NULL, 'mim08@gmail.com', '01710000008', 'Mi@012345!', 'Cumilla', 'Kotwali', 'Racecourse'),
(9, 'Shanto', 'Sarker', 'shanto.sarker09@gmail.com', '01710000009', 'Ss@012345!', 'Rangpur', 'Jahaj Company', 'Road 4'),
(10, 'Jannat', 'Ara', NULL, '01710000010', 'Ja@012345!', 'Mymensingh', 'Ganginarpar', 'Road 1'),
(11, 'Imran', 'Kabir', 'imran.kabir11@gmail.com', '01710000011', 'Ik#543210!', 'Dhaka', 'Uttara', 'Sector 7'),
(12, 'Raisa', 'Sultana', 'raisa.sultana12@gmail.com', '01710000012', 'Rs#543210!', 'Gazipur', 'Tongi', 'College Gate'),
(13, 'Sabbir', 'Ahmed', 'sabbir.ahmed13@gmail.com', '01710000013', 'Sa#543210!', 'Narayanganj', 'Fatullah', 'Road 6'),
(14, 'Mahi', 'Rahman', 'mahi.rahman14@gmail.com', '01710000014', 'Mr#543210!', 'Bogura', 'Sadar', 'Station Road'),
(15, 'Nafis', 'Islam', 'nafis.islam15@gmail.com', '01710000015', 'Ni#543210!', 'Jashore', 'Kotwali', 'Rail Road'),
(16, 'Tania', 'Khan', NULL, '01710000016', 'Tk#543210!', 'Dhaka', 'Mohammadpur', 'Ring Road'),
(17, 'Adnan', 'Karim', 'adnan.karim17@gmail.com', '01710000017', 'Ak#543210!', 'Noakhali', 'Maijdee', 'Main Road'),
(18, 'Sumaiya', 'Akter', 'sumaiya.akter18@gmail.com', '01710000018', 'Sa#543210!', 'Pabna', 'Sadar', 'Hospital Road'),
(19, 'Sohan', 'Rahman', 'sohan.rahman19@gmail.com', '01710000019', 'Sr#543210!', 'Dinajpur', 'Sadar', 'Road 3'),
(20, 'Maria', 'Haque', 'maria.haque20@gmail.com', '01710000020', 'Mh#543210!', 'Sylhet', 'Amberkhana', 'Road 9'),
(21, 'Arif', 'Mahmud', NULL, '01710000021', 'Am#543210!', 'Dhaka', 'Badda', 'Road 11'),
(22, 'Tasnia', 'Noor', 'tasnia.noor22@gmail.com', '01710000022', 'Tn#543210!', 'Khulna', 'Khalishpur', 'Road 2'),
(23, 'Fahim', 'Uddin', 'fahim.uddin23@gmail.com', '01710000023', 'Fu#543210!', 'Rajshahi', 'Kazla', 'University Road'),
(24, 'Rifat', 'Hasan', 'rifat.hasan24@gmail.com', '01710000024', 'Rh#543210!', 'Cumilla', 'Sadar', 'Road 7'),
(25, 'Nabila', 'Ahmed', 'nabila.ahmed25@gmail.com', '01710000025', 'Na#543210!', 'Dhaka', 'Banani', 'Road 12'),
(26, 'Ishmam', 'Tahmid', 'ishmam.tahmid26@gmail.com', '01710000026', 'It@012345!', 'Dhaka', 'Rampura', 'Road 4'),
(27, 'Tahmid', 'Zarif', 'tahmid.zarif27@gmail.com', '01710000027', 'Tz@012345!', 'Chattogram', 'Agrabad', 'Road 11'),
(28, 'Nawal', 'Faiyaz', 'nawal.faiyaz28@gmail.com', '01710000028', 'Nf@012345!', 'Sylhet', 'Subidbazar', 'Lane 2'),
(29, 'Ifaz', 'Mahir', NULL, '01710000029', 'Im@012345!', 'Rajshahi', 'Laxmipur', 'Road 7'),
(30, 'Raida', 'Tasnim', 'raida.tasnim30@gmail.com', '01710000030', 'Rt@012345!', 'Khulna', 'Daulatpur', 'Road 6'),
(31, 'Arian', 'Nabeel', 'arian.nabeel31@gmail.com', '01710000031', 'An@012345!', 'Cumilla', 'Kotbari', 'Road 3'),
(32, 'Muntasir', 'Rayan', 'muntasir.rayan32@gmail.com', '01710000032', 'Mr@012345!', 'Rangpur', 'Modern', 'Road 1'),
(33, 'Yusra', 'Mahjabin', 'yusra.mahjabin33@gmail.com', '01710000033', 'Ym@012345!', 'Dhaka', 'Bashundhara', 'Block C'),
(34, 'Fariha', 'Anzum', 'fariha.anzum34@gmail.com', '01710000034', 'Fa@012345!', 'Mymensingh', 'Charpara', 'Road 8'),
(35, 'Nairuz', 'Jafrin', 'nairuz.jafrin35@gmail.com', '01710000035', 'Nj@012345!', 'Barishal', 'Nathullabad', 'Road 2'),
(36, 'Ibtida', 'Rahat', 'ibtida.rahat36@gmail.com', '01710000036', 'Ir@012345!', 'Bogura', 'Chelopara', 'Road 5'),
(37, 'Mehrab', 'Tahsin', NULL, '01710000037', 'Mt@012345!', 'Jashore', 'Monihar', 'Road 10'),
(38, 'Zuha', 'Tabassum', 'zuha.tabassum38@gmail.com', '01710000038', 'Zt#543210!', 'Pabna', 'Ataikula', 'Road 9'),
(39, 'Rifah', NULL, 'rifah39@gmail.com', '01710000039', 'Ri#543210!', 'Noakhali', 'Sonapur', 'Main Road'),
(40, 'Afnan', 'Mushfiq', 'afnan.mushfiq40@gmail.com', '01710000040', 'Am#543210!', 'Gazipur', 'Board Bazar', 'Road 4'),
(41, 'Nuha', 'Sharmeen', 'nuha.sharmeen41@gmail.com', '01710000041', 'Ns#543210!', 'Narsingdi', 'Madhabdi', 'Road 12'),
(42, 'Mahveen', 'Afsar', 'mahveen.afsar42@gmail.com', '01710000042', 'Ma#543210!', 'Dinajpur', 'Bahadur Bazar', 'Road 6'),
(43, 'Zayan', 'Abrar', 'zayan.abrar43@gmail.com', '01710000043', 'Za#543210!', 'Sylhet', 'Tilagor', 'Road 3'),
(44, 'Anika', 'Rafsan', 'anika.rafsan44@gmail.com', '01710000044', 'Ar#543210!', 'Dhaka', 'Khilgaon', 'Road 14'),
(45, 'Rafid', 'Nawshad', 'rafid.nawshad45@gmail.com', '01710000045', 'Rn#543210!', 'Khulna', 'Boyra', 'Road 7'),
(46, 'Afnan', 'Rahman', 'afnan.rahman46@gmail.com', '01710000046', 'Ar#543210!', 'Sylhet', 'Uposhohor', 'Block B'),
(47, 'Sumaiya', 'Yasmin Nairit', 'sumaiya.nairit47@gmail.com', '01710000047', 'Sy#543210!', 'Dhaka', 'Bashundhara', 'Block D'),
(48, 'Sadaf', 'Abdullah', 'sadaf.abdullah48@gmail.com', '01710000048', 'Sa#543210!', 'Jashore', 'Kotwali', 'Rail Road'),
(49, 'Nawsheen', 'Nawer Tori', 'nawsheen.tori49@gmail.com', '01710000049', 'Nn#543210!', 'Chattogram', 'Panchlaish', 'Road 8'),
(50, 'Nazha', 'Islam', 'nazha.islam50@gmail.com', '01710000050', 'Ni#543210!', 'Sylhet', 'Zindabazar', 'Station Road');

--
-- Indexes for dumped tables
--



--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`Admin_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`Cart_ID`) REFERENCES `cart` (`Cart_ID`),
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE CASCADE;

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `category_ibfk_1` FOREIGN KEY (`Parent_Category_ID`) REFERENCES `category` (`Category_ID`);

--
-- Constraints for table `claim`
--
ALTER TABLE `claim`
  ADD CONSTRAINT `claim_ibfk_1` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `claim_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `fk_item_admin` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`Admin_ID`),
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `item_ibfk_2` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`Category_ID`);

--
-- Constraints for table `item_image`
--
ALTER TABLE `item_image`
  ADD CONSTRAINT `item_image_ibfk_1` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Purchase_ID`) REFERENCES `purchase` (`Purchase_ID`);

--
-- Constraints for table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`Buyer_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `purchase_item`
--
ALTER TABLE `purchase_item`
  ADD CONSTRAINT `purchase_item_ibfk_1` FOREIGN KEY (`Purchase_ID`) REFERENCES `purchase` (`Purchase_ID`),
  ADD CONSTRAINT `purchase_item_ibfk_2` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_item_seller_fk` FOREIGN KEY (`Seller_ID`) REFERENCES `user` (`User_ID`) ON DELETE SET NULL;

--
-- Constraints for table `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`From_User_ID`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `rating_ibfk_2` FOREIGN KEY (`To_User_ID`) REFERENCES `user` (`User_ID`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `fk_report_admin` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`Admin_ID`),
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`),
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`Item_ID`) REFERENCES `item` (`Item_ID`) ON DELETE SET NULL;

--
-- Constraints for table `support_ticket`
--
ALTER TABLE `support_ticket`
  ADD CONSTRAINT `fk_ticket_admin` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`Admin_ID`),
  ADD CONSTRAINT `support_ticket_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
