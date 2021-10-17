CREATE DATABASE IF NOT EXISTS `quiz`;

USE `quiz`;

CREATE DATABASE IF NOT EXISTS `quiz`;

USE `quiz`;

CREATE TABLE IF NOT EXISTS `users` (
 `user_id`  INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `username` VARCHAR(40) NOT NULL,
 `email`    VARCHAR(128) NOT NULL,
 `password` VARCHAR(80) NOT NULL,
 `birthday` DATE NOT NULL,
 `gender`   ENUM('Female', 'Male', 'Other') NOT NULL,
 `photo`    VARCHAR(100) NOT NULL DEFAULT 'avatar.jpg',
 `role_id`  INT(11) UNSIGNED NOT NULL,
 `active`   TINYINT(1) NOT NULL DEFAULT '1',
 `views`    INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `created`  DATETIME NOT NULL,
 `modified` DATETIME NOT NULL,
 PRIMARY KEY (`user_id`),
 UNIQUE KEY `username` (`username`),
 UNIQUE KEY `email` (`email`),
 KEY `role_id` (`role_id`),
 KEY `active` (`active`) 
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles`(
 `role_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `role`    VARCHAR(25)      NOT NULL,
 PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `roles` (`role_id`, `role`) VALUES
(1, 'admin'),
(2, 'member'),
(3, 'guest');

CREATE TABLE IF NOT EXISTS `forgot` (
  `user_id` INT(11) UNSIGNED NOT NULL,
  `token`   VARCHAR(40) NOT NULL,
  `created` DATETIME    NOT NULL,
  PRIMARY KEY (`user_id`, `token`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `resources` (
  `resource_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module`      VARCHAR(20)      NOT NULL,
  `controller`  VARCHAR(20)      NOT NULL,
  `action`      VARCHAR(20)      NOT NULL,
  PRIMARY KEY (`resource_id`, `module`, `controller`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `privileges` (
  `resource_id` INT(11) UNSIGNED NOT NULL,
  `role_id`     INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`resource_id`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO  `resources` (`module`, `controller`, `action`) VALUES
('application', 'admin', 'index'),
('application', 'admin', 'delete'),
('application', 'help', 'contact'),
('application', 'help', 'privacy'),
('application', 'help', 'terms'),
('application', 'index', 'index'),
('application', 'quiz', 'answer'),
('application', 'quiz', 'create'),
('application', 'quiz', 'delete'),
('application', 'quiz', 'index'),
('application', 'quiz', 'view'),

('user', 'admin', 'index'),
('user', 'admin', 'delete'),
('user', 'auth', 'create'),
('user', 'login', 'index'),
('user', 'logout', 'index'),
('user', 'password', 'forgot'),
('user', 'password', 'reset'),
('user', 'profile', 'index'),
('user', 'setting', 'delete'),
('user', 'setting', 'email'),
('user', 'setting', 'index'),
('user', 'setting', 'password'),
('user', 'setting', 'upload'),
('user', 'setting', 'username'),

('application', 'comment', 'create'),
('application', 'comment', 'delete'),
('application', 'comment', 'edit');

INSERT INTO `privileges` (`resource_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(16, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),

(3, 2),
(4, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(16, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),

(3, 3),
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(11, 3),
(14, 3),
(15, 3),
(17, 3),
(18, 3),
(19, 3);

CREATE TABLE IF NOT EXISTS `quizzes`(
 `quiz_id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `user_id`     INT(11) UNSIGNED NOT NULL,
 `category_id` INT(11) UNSIGNED NOT NULL,
 `title`       VARCHAR(100)     NOT NULL,
 `question`    TEXT             NOT NULL,
 `status`      TINYINT(1)       NOT NULL DEFAULT '1',
 `views`       INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `total`       INT(11) UNSIGNED NOT NULL DEFAULT '0',
 `timeout`     DATETIME         NOT NULL,
 `created`     DATETIME         NOT NULL,
 PRIMARY KEY(`quiz_id`),
 KEY `user_id` (`user_id`),
 KEY `category_id` (`category_id`),
 KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `quizzes` ADD `comments` INT(11) UNSIGNED NOT NULL DEFAULT '0' AFTER `total`,
ADD INDEX `comments` (`comments`);

CREATE TABLE IF NOT EXISTS `answers`(
 `answer_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `quiz_id`   INT(11) UNSIGNED NOT NULL,
 `answer`    TEXT             NOT NULL,
 `tally`     INT(11) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY(`answer_id`),
  KEY `quiz_id` (`quiz_id`)
)ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quiz_id`    INT(11) UNSIGNED NOT NULL,
  `user_id`    INT(11) UNSIGNED NOT NULL,
  `comment`    TEXT             NOT NULL,
  `created`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comment_id`),
  KEY `quiz` (`quiz_id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tallies`(
 `quiz_id`   INT(11) UNSIGNED NOT NULL,
 `answer_id` INT(11) UNSIGNED NOT NULL,
 `user_id`   INT(11) UNSIGNED NOT NULL,
 `created`   DATETIME         NOT NULL,
 PRIMARY KEY(`quiz_id`, `user_id`)
)ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories`(
 `category_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `category`    VARCHAR(100)     NOT NULL,
 PRIMARY KEY(`category_id`),
 KEY `category` (`category`)
)ENGINE=InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT IGNORE INTO `categories` (`category_id`, `category`) VALUES
(1, 'Health'),
(2, 'Politics'),
(3, 'Science'),
(4, 'Sports'),
(5, 'World');
