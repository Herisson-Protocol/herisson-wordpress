
-- --------------------------------------------------------------------------
-- bookmarks
-- --------------------------------------------------------------------------

CREATE TABLE `#PREFIX#herisson_bookmarks`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`url` VARCHAR(1024),
	`hash` VARCHAR(255),
	`title` VARCHAR(255),
	`description` TEXT,
	`content` MEDIUMTEXT,
	`favicon_url` varchar(255),
	`favicon_image` TEXT,
	`is_public` TINYINT default 1,
	`is_binary` TINYINT default 0,
	`content_image` varchar(255),
	`content_type` varchar(50),
	`error` TINYINT default 0,
	expires_at` DATETIME default '2038-12-31',
	`created` DATETIME,
	`dirsize` int(10),
 `updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 type_id INTEGER(10),
	PRIMARY KEY (`id`),
	KEY `title`(`title`),
	KEY `description`(`description`(1000)),
	KEY `content`(`content`(1000)),
	KEY `url`(`url`),
	KEY `is_public`(`is_public`),
	KEY `is_binary`(`is_binary`),
	KEY `favicon_url`(`favicon_url`),
	KEY `content_image`(`content_image`),
	KEY `dirsize`(`dirsize`),
	KEY `created`(`created`),
	KEY `updated`(`updated`)
);

--  ---------------------------------------------------------------------------
--   tags
--  ---------------------------------------------------------------------------

CREATE TABLE `#PREFIX#herisson_tags`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255),
	`bookmark_id` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `name`(`name`),
	KEY `bookmark_id`(`bookmark_id`)
);

--  ---------------------------------------------------------------------------
--   bookmarks_tags
--  ---------------------------------------------------------------------------

-- CREATE TABLE `#PREFIX#herisson_bookmarks_tags`
-- (
-- 	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
-- 	`tag_id` INTEGER(10),
-- 	`bookmark_id` VARCHAR(255),
-- 	PRIMARY KEY (`id`),
-- 	KEY `tag_id`(`tag_id`),
-- 	KEY `bookmark_id`(`bookmark_id`)
-- );

--  ---------------------------------------------------------------------------
--   friends
--  ---------------------------------------------------------------------------

CREATE TABLE `#PREFIX#herisson_friends`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`url` VARCHAR(255),
	`alias` VARCHAR(255),
	`name` VARCHAR(255),
	`email` VARCHAR(255),
	`public_key` TEXT,
	`is_active` TINYINT default 0,
	`b_youwant` TINYINT default 0,
	`b_wantsyou` TINYINT default 0,
	PRIMARY KEY (`id`),
	KEY `name`(`name`),
	KEY `url`(`url`),
	KEY `email`(`email`),
	KEY `is_active`(`is_active`),
	KEY `b_youwant`(`b_youwant`),
	KEY `b_wantsyou`(`b_wantsyou`)
);

--  ---------------------------------------------------------------------------
--   types
--  ---------------------------------------------------------------------------

CREATE TABLE `#PREFIX#herisson_types`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `name`(`name`)
);

--  ---------------------------------------------------------------------------
--   screenshots
--  ---------------------------------------------------------------------------

CREATE TABLE `#PREFIX#herisson_screenshots`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255),
	`fonction` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `name`(`name`)
);

--  ---------------------------------------------------------------------------
--   screenshots
--  ---------------------------------------------------------------------------

CREATE TABLE `#PREFIX#herisson_backups`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`size` int(10),
	`nb` int(10),
	`creation` datetime,
	`friend_id` INTEGER(10) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `friend_id`(`friend_id`)
);
