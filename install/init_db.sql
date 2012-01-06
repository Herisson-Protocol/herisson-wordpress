
-------------------------------------------------------------------------------
---- bookmarks
-------------------------------------------------------------------------------

DROP TABLE IF EXISTS `#PREFIX#herisson_bookmarks`;


CREATE TABLE `#PREFIX#herisson_bookmarks`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`url` VARCHAR(1024),
	`hash` VARCHAR(255),
	`title` VARCHAR(255),
	`description` TEXT,
	`is_public` TINYINT default 1,
	`is_activated` TINYINT default 0,
	`expires_at` DATETIME default '0',
	`updated` DATETIME default '0',
	PRIMARY KEY (`id`),
	KEY `title`(`title`),
	KEY `description`(`description`),
	KEY `url`(`url`)
);

-------------------------------------------------------------------------------
---- tags
-------------------------------------------------------------------------------

DROP TABLE IF EXISTS `#PREFIX#herisson_tags`;


CREATE TABLE `#PREFIX#herisson_tags`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `name`(`name`)
);

-------------------------------------------------------------------------------
---- bookmarks_tags
-------------------------------------------------------------------------------

DROP TABLE IF EXISTS `#PREFIX#herisson_bookmarks_tags`;


CREATE TABLE `#PREFIX#herisson_bookmarks_tags`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`tag_id` INTEGER(10),
	`bookmark_id` VARCHAR(255),
	PRIMARY KEY (`id`),
	KEY `tag_id`(`tag_id`),
	KEY `bookmark_id`(`bookmark_id`),
);

-------------------------------------------------------------------------------
---- friends
-------------------------------------------------------------------------------

DROP TABLE IF EXISTS `#PREFIX#herisson_friends`;


CREATE TABLE `#PREFIX#herisson_friends`
(
	`id` INTEGER(10)  NOT NULL AUTO_INCREMENT,
	`url` VARCHAR(255),
	`name` VARCHAR(255),
	`email` VARCHAR(255),
	`public_key` TEXT,
	`token` VARCHAR(255),
	`is_active` TINYINT default 0,
	PRIMARY KEY (`id`),
	KEY `name`(`name`),
	KEY `url`(`url`),
	KEY `email`(`email`)
);
