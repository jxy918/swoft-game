
drop table if exists user;
drop table if exists count;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
  `age` int(11) NOT NULL DEFAULT ''0'',
  `password` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
  `user_desc` varchar(120) CHARACTER SET utf8mb4 NOT NULL DEFAULT "",
  `add` int(3) DEFAULT NULL,
  `hahh` int(11) DEFAULT ''1'',
  `test_json` json DEFAULT NULL,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `count` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `attributes` varchar(244) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
