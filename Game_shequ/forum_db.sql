/*
 Navicat Premium Dump SQL

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 90200 (9.2.0)
 Source Host           : localhost:3306
 Source Schema         : forum_db

 Target Server Type    : MySQL
 Target Server Version : 90200 (9.2.0)
 File Encoding         : 65001

 Date: 07/03/2025 22:01:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for bookmarks
-- ----------------------------
DROP TABLE IF EXISTS `bookmarks`;
CREATE TABLE `bookmarks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  CONSTRAINT `bookmarks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of bookmarks
-- ----------------------------
BEGIN;
INSERT INTO `bookmarks` (`id`, `post_id`, `user_id`, `created_at`) VALUES (2, 5, 3, '2024-12-26 15:35:04');
COMMIT;

-- ----------------------------
-- Table structure for comments
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of comments
-- ----------------------------
BEGIN;
INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES (1, 3, 1, '不错', '2024-12-26 12:28:11');
INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES (2, 3, 1, '怎么说', '2024-12-26 12:52:12');
INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES (3, 5, 2, '不错', '2024-12-26 13:31:54');
INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES (4, 3, 2, '有点东西', '2024-12-26 13:32:09');
INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES (5, 5, 3, '怎么说', '2024-12-26 15:35:09');
INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`) VALUES (6, 7, 1, 'good', '2025-03-04 15:45:59');
COMMIT;

-- ----------------------------
-- Table structure for likes
-- ----------------------------
DROP TABLE IF EXISTS `likes`;
CREATE TABLE `likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of likes
-- ----------------------------
BEGIN;
INSERT INTO `likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES (2, 5, 1, '2024-12-26 13:04:08');
INSERT INTO `likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES (3, 5, 2, '2024-12-26 13:31:48');
INSERT INTO `likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES (4, 4, 1, '2024-12-26 15:21:50');
INSERT INTO `likes` (`id`, `post_id`, `user_id`, `created_at`) VALUES (5, 5, 3, '2024-12-26 15:35:03');
COMMIT;

-- ----------------------------
-- Table structure for posts
-- ----------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of posts
-- ----------------------------
BEGIN;
INSERT INTO `posts` (`id`, `user_id`, `title`, `content`, `created_at`, `image`) VALUES (4, 1, '明日方舟', '你觉得如何', '2024-12-26 12:21:06', './uploads/post_676cd9b23684c8.59226155.jpg');
INSERT INTO `posts` (`id`, `user_id`, `title`, `content`, `created_at`, `image`) VALUES (5, 1, '你还在犹豫什么', '99999999999999999999999999999999999999999999999999adhdahdhahdahdasdhadala', '2024-12-26 13:03:51', NULL);
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `username`, `password`, `phone`, `address`) VALUES (1, 'admin', '$2y$12$8/bUUz6535Aq8p8iG07qCuG.r1x7dght0I1p2TpEOf1u0Fr/eNO1C', '123456789', '1134');
INSERT INTO `users` (`id`, `username`, `password`, `phone`, `address`) VALUES (2, 'test', '$2y$12$nPzsUDEg9HCJB7Ep1G/6w.SKaWTQiXdWlteatY6UquHwp753QQ0Em', '123', '1111');
INSERT INTO `users` (`id`, `username`, `password`, `phone`, `address`) VALUES (3, 'tom', '$2y$12$HrlIbfFP5BtHszE1WmWkBOcoGkX220EyCsqXvyvceKDTbDSMHsA1K', '12345678', '222');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
