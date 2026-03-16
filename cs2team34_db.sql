-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 12, 2026 at 01:27 PM
-- Server version: 8.0.45-0ubuntu0.22.04.1
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cs2team34_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `created_at`) VALUES
(10, 'admin', '$2y$10$YYrY/0QscGLjzxP29U3k3eJWUoQdmcQUNNhD554rApssvk3e8fr6y', '2025-10-28 18:12:06');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `contact_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `responded` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`contact_id`, `name`, `email`, `message`, `responded`, `created_at`) VALUES
(1, 'simran', '240364746@aston.ac.uk', 'just testing, do you make any halal meals', 0, '2025-12-05 02:56:07'),
(2, 'simran', '240364746@aston.ac.uk', 'do you have a festive menu?', 0, '2025-12-05 03:04:13');

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `meal_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `price` decimal(6,2) NOT NULL,
  `calories` int DEFAULT NULL,
  `category` enum('breakfast','lunch','dinner','snack','dessert','bento') COLLATE utf8mb4_general_ci NOT NULL,
  `stock` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `protein` int DEFAULT NULL,
  `carbs` int DEFAULT NULL,
  `sugars` int DEFAULT NULL,
  `fat` int DEFAULT NULL,
  `saturates` int DEFAULT NULL,
  `fiber` int DEFAULT NULL,
  `salt` decimal(4,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`meal_id`, `name`, `description`, `price`, `calories`, `category`, `stock`, `created_at`, `image`, `protein`, `carbs`, `sugars`, `fat`, `saturates`, `fiber`, `salt`) VALUES
(1, 'Chocolate Protein Overnight Oats', 'Oats soaked in chocolate protein shake, topped with banana chips, blueberries, peanut butter and cacao nibs.', '3.50', 450, 'breakfast', 11, '2025-11-25 03:57:43', 'chocolate protein overnight oats.png', 28, 52, 18, 14, 3, 8, '0.3'),
(2, 'Protein Pancake Stack', 'Fluffy protein pancakes with vanilla whey protein, topped with yoghurt, syrup and berries.', '4.00', 420, 'breakfast', 15, '2025-11-25 03:57:43', 'protein pancake stack.png', 32, 48, 22, 10, 4, 5, '0.4'),
(3, 'Mediterranean Breakfast Wrap', 'Scrambled eggs, spinach, olives, feta, sun-dried tomatoes in a whole wheat wrap.', '4.50', 390, 'breakfast', 15, '2025-11-25 03:57:43', 'mediterranean breakfast wrap.png', 22, 32, 4, 18, 6, 5, '1.8'),
(4, 'Chia Pudding', 'Chia seeds soaked in milk, topped with mango, pineapple, coconut flakes and passion fruit.', '3.20', 380, 'breakfast', 5, '2025-11-25 03:57:43', 'tropical chia pudding.png', 10, 38, 24, 20, 12, 14, '0.1'),
(5, 'Turkey Breakfast Rolls', 'Caramelised onion turkey sausage, melted cheddar, puff pastry with onion seeds.', '3.80', 520, 'breakfast', 14, '2025-11-25 03:57:43', 'turkey breakfast rolls.png', 28, 36, 4, 28, 12, 2, '2.1'),
(9, 'Chicken Katsu Curry', 'Crispy breaded chicken cutlet with Japanese curry sauce, jasmine rice, pickled vegetables.', '6.80', 680, 'lunch', 14, '2025-11-25 03:57:43', 'chicken katsu curry.png', 38, 82, 8, 18, 3, 4, '2.2'),
(10, 'Cajun Chicken Pasta', 'Penne pasta in creamy Cajun sauce with grilled chicken, peppers, parmesan.', '6.40', 640, 'lunch', 14, '2025-11-25 03:57:43', 'cajun chicken pasta.png', 42, 58, 6, 24, 12, 4, '1.8'),
(11, 'Peri-Peri Chicken Wrap', 'Spicy grilled chicken with lettuce, tomato, onion and peri-peri mayo.', '5.00', 480, 'lunch', 20, '2025-11-25 03:57:43', 'peri-peri chicken wrap.png', 36, 38, 5, 18, 3, 4, '1.6'),
(12, 'Falafel and Hummus Bowl', 'Falafel balls on quinoa with hummus, salad, pickled veg, tahini drizzle.', '5.20', 520, 'lunch', 20, '2025-11-25 03:57:43', 'falafel and hummus bowl.png', 20, 56, 8, 24, 3, 12, '1.8'),
(16, 'Sticky Teriyaki Chicken Bowl', 'Glazed teriyaki chicken thighs with jasmine rice, stir-fried vegetables, edamame.', '7.20', 680, 'dinner', 0, '2025-11-25 03:57:43', 'sticky teriyaki chicken bowl.png', 44, 78, 16, 18, 4, 5, '2.8'),
(17, 'Butter Chicken', 'Tender chicken in creamy tomato-butter sauce with basmati rice and naan.', '7.40', 740, 'dinner', 14, '2025-11-25 03:57:43', 'butter chicken.png', 42, 86, 12, 24, 12, 5, '2.6'),
(18, 'Mediterranean Baked Salmon', 'Herb-crusted salmon with roasted potatoes, asparagus, tomatoes, lemon butter sauce.', '7.00', 560, 'dinner', 10, '2025-11-25 03:57:43', 'mediterranean baked salmon.png', 38, 36, 5, 28, 8, 6, '1.4'),
(24, 'Energy Ball Mix', 'Bite-sized energy balls made from oats, dates, nuts, cocoa.', '2.50', 280, 'snack', 30, '2025-11-25 03:57:43', 'energy ball mix.png', 8, 32, 20, 12, 2, 5, '0.1'),
(25, 'Fruit Box', 'Fresh seasonal fruit pieces — apple, grapes, strawberries, melon, blueberries.', '2.50', 120, 'snack', 29, '2025-11-25 03:57:43', 'fruit box.png', 2, 28, 26, 1, 0, 4, '0.0'),
(27, 'Cheesecake Pot', 'Creamy vanilla cheesecake layered with biscuit crumbs.', '3.80', 380, 'dessert', 25, '2025-11-25 03:57:43', 'vanilla cheesecake.png', 6, 36, 26, 24, 14, 1, '0.6'),
(28, 'Apple Crumble Pot', 'Warm spiced apple compote topped with buttery crumble.', '3.20', 280, 'dessert', 25, '2025-11-25 03:57:43', 'apple crumble.png', 3, 42, 28, 12, 7, 4, '0.2'),
(30, 'Tiramisu Cup', 'Coffee-soaked ladyfingers layered with mascarpone cream, cocoa dusting.', '3.80', 320, 'dessert', 12, '2025-11-25 03:57:43', 'tiramisu.png', 7, 32, 18, 18, 11, 1, '0.3'),
(32, 'Eton Mess Pot', 'Crushed meringue layered with whipped cream and strawberries.', '3.50', 280, 'dessert', 20, '2025-11-25 03:57:43', 'eton mess.png', 3, 38, 36, 14, 9, 2, '0.1'),
(35, 'Classic Japanese Bento', 'Teriyaki salmon and chicken with rice, beans, pickled vegetables, mandarin.', '6.00', 560, 'bento', 20, '2025-11-25 03:57:43', 'classic japenese bento.png', 36, 62, 12, 14, 3, 5, '2.4'),
(36, 'Vegan Rainbow Bento', 'Teriyaki tofu, brown rice, sweet potato, avocado, raw vegetables, tahini dressing.', '5.80', 520, 'bento', 19, '2025-11-25 03:57:43', 'vegan rainbow bento.png', 22, 58, 10, 22, 3, 12, '1.4'),
(37, 'Mediterranean Bento', 'Falafel, hummus, tzatziki, tabbouleh, stuffed vine leaves, pita, olives.', '5.80', 480, 'bento', 20, '2025-11-25 03:57:43', 'mediterranean bento.png', 16, 52, 6, 22, 3, 10, '2.6'),
(38, 'Tex-Mex Fiesta Bento', 'Chicken or beef with Mexican rice, beans, guacamole, salsa, tortilla chips.', '6.00', 560, 'bento', 20, '2025-11-25 03:57:43', 'tex mex fiesta bento.png', 32, 64, 6, 16, 4, 10, '2.2'),
(39, 'Greek Island Bento', 'Chicken souvlaki skewers with lemon orzo salad, roasted vegetables, tzatziki, baklava.', '6.20', 620, 'bento', 20, '2025-11-25 03:57:43', 'greek island bento.png', 34, 68, 18, 20, 6, 6, '2.2'),
(40, 'Sushi Box', 'Assorted sushi including salmon nigiri, California rolls, edamame, pickled ginger, wasabi and soy sauce.', '5.50', 420, 'lunch', 20, '2025-12-04 23:05:52', 'sushi box.png', 28, 54, 8, 8, 2, 4, '3.8'),
(41, 'Popcorn Tub', 'Freshly popped corn lightly seasoned with sea salt or sweet caramel.', '2.00', 160, 'snack', 30, '2025-12-04 23:05:52', 'caramel popcorn.png', 3, 30, 1, 3, 1, 5, '0.8'),
(42, 'Coconut & Date Bars', 'Homemade-style coconut and date bars made from oats, coconut oil, and vanilla.', '2.50', 220, 'snack', 25, '2025-12-04 23:05:52', 'coconut and date bars.png', 3, 32, 24, 10, 8, 4, '0.1'),
(43, 'Veg and Dip Box', 'Crunchy vegetable sticks with creamy hummus or ranch dip.', '2.80', 180, 'snack', 30, '2025-12-04 23:05:52', 'veg and dip box.png', 6, 16, 8, 10, 2, 6, '0.8'),
(44, 'Prawn and Harissa Spaghetti', 'Spaghetti tossed with prawns in spicy harissa tomato sauce.', '6.20', 580, 'dinner', 21, '2025-12-04 23:05:52', 'prawn and harissa spaghetti.png', 36, 72, 8, 14, 3, 5, '2.2'),
(45, 'Lasagne', 'Classic beef lasagne with béchamel, mozzarella, and rich tomato sauce.', '6.00', 680, 'dinner', 20, '2025-12-04 23:05:52', 'lasagne.png', 38, 58, 10, 30, 16, 4, '2.4'),
(46, 'Cinnamon Rolls', 'Soft pastry swirled with cinnamon sugar and topped with vanilla icing.', '3.00', 340, 'dessert', 20, '2025-12-04 23:05:52', 'cinnamon rolls.png', 5, 52, 28, 12, 6, 2, '0.5');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `total_price` decimal(8,2) NOT NULL,
  `total_calories` int NOT NULL,
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `total_calories`, `status`, `created_at`) VALUES
(28, 10, '7.50', 870, 'completed', '2025-12-05 05:26:43'),
(29, 10, '6.80', 680, 'cancelled', '2025-12-05 05:26:43'),
(30, 10, '8.90', 760, 'completed', '2025-12-05 05:26:43'),
(31, 10, '109.40', 10080, 'pending', '2025-12-05 10:16:34'),
(33, 10, '4.00', 420, 'pending', '2025-12-05 10:21:32'),
(34, 10, '3.20', 380, 'pending', '2025-12-05 10:23:41'),
(35, 11, '3.20', 380, 'pending', '2025-12-05 11:27:24'),
(36, 13, '10.50', 1350, 'pending', '2025-12-08 16:28:20'),
(37, 13, '10.20', 1280, 'pending', '2025-12-09 10:15:13'),
(38, 14, '168.40', 17230, 'cancelled', '2026-01-27 15:05:21'),
(39, 14, '3.50', 450, 'pending', '2026-01-27 15:06:16');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `meal_id` int NOT NULL,
  `quantity` int NOT NULL,
  `item_price` decimal(6,2) NOT NULL,
  `item_calories` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `meal_id`, `quantity`, `item_price`, `item_calories`) VALUES
(46, 28, 1, 1, '3.50', 450),
(47, 28, 2, 1, '4.00', 420),
(48, 29, 9, 1, '6.80', 680),
(49, 30, 10, 1, '6.40', 640),
(50, 30, 25, 1, '2.50', 120),
(51, 31, 1, 1, '3.50', 450),
(52, 31, 2, 2, '4.00', 420),
(53, 31, 3, 11, '4.50', 390),
(54, 31, 9, 1, '6.80', 680),
(55, 31, 30, 8, '3.80', 320),
(56, 31, 17, 1, '7.40', 740),
(57, 31, 5, 1, '3.80', 520),
(58, 33, 2, 1, '4.00', 420),
(59, 34, 4, 1, '3.20', 380),
(60, 35, 4, 1, '3.20', 380),
(61, 36, 1, 3, '3.50', 450),
(62, 37, 1, 2, '3.50', 450),
(63, 37, 4, 1, '3.20', 380),
(64, 38, 1, 1, '3.50', 450),
(65, 38, 4, 17, '3.20', 380),
(66, 38, 16, 15, '7.20', 680),
(67, 38, 25, 1, '2.50', 120),
(68, 39, 1, 1, '3.50', 450);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `student_verified` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `student_verified`) VALUES
(3, 'Khalid', 'khalid@aston.ac.uk', '$2y$10$1zKm7q.L30LJEkEvHqUo4utwbnOk11tHRwtGikIqdKeDFYdKdhtTm', 1),
(4, 'Mehek', '240146234@aston.ac.uk', '$2y$10$VkRozHXFBHDN3PY1i40K8e1ADgvDDyjSsbyQRGrJkGUHZLMyUQAkq', 1),
(5, 'Muzamal', '240121954@aston.ac.uk', '$2y$10$VzIQCZ7wVfakY0OClt5VvO/zKJAJ.ZCDx/8xAVn76JuzpwQkH6JMe', 1),
(6, 'Zunehra', '230115336@aston.ac.uk', '$2y$10$dzPUNJHWa6huTwySQvyuDeazGfUahKgErHD6MROYVHIHzzM2.FSwa', 1),
(7, 'Haaris', '240111450@aston.ac.uk', '$2y$10$2aokHgH2HlzZ4suVhKn9C.odGkH9upn7aJXWs6GRZvo6/KzAkqR3O', 1),
(8, 'Abdul', '240183871@aston.ac.uk', '$2y$10$9ROfv/VpOA56kHpxepcnWOTodUsAKBZueZl6afxC7ZVHLxewHxw3y', 1),
(9, 'Rufus', '240078430@aston.ac.uk', '$2y$10$VnHKPGiyZLj5PXNFIXjig.7I9P/KGBwMWot.lhmKw2O0g7ovsrMMG', 1),
(10, 'simran', '240364746@aston.ac.uk', '$2y$10$QTjd/bTnLWtUyeBZi.dWauB4BneirTL5Gv2sB7yJtIVrlVe8xOBim', 1),
(11, 'Zunehra', 'zunehra@aston.ac.uk', '$2y$10$J7RXh04rkSOZqU6LE8QBYuqD.T7PeDldAqNuBsdNRLaU3Ya9nEf1i', 1),
(12, 'User', 'User1@aston.ac.uk', '$2y$10$K2tNEv87J1YAilpCn7/qzuoQ0IkjHJTTGNpn0laZJxV4qxi6/R7Nq', 1),
(15, 'simran', 'simran@aston.ac.uk', '$2y$10$KYhe5Em4LGY35g7De3uymeyqMUJZ73W5zPBkLPceenp9YFUONcehC', 1),
(16, 'user', 'user100@aston.ac.uk', '$2y$10$BuIATgDjHETw/w8gcxkUPudNdWHRSLMojtAlnQXQZVamnZA6VPbR2', 1),
(17, 'khalid', '211111@aston.ac.uk', '$2y$10$8b5/HA44pKl6PXVKlpgrM.5YbCaUzqEVZQEjUy7iEYZiTxtLBwsWy', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`meal_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `meal_id` (`meal_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `contact_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `meal_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`meal_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
