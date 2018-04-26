/**
  用户表
 */

USE khsci;

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id`           BIGINT AUTO_INCREMENT,
  `git_type`     VARCHAR(20),
  `uid`          BIGINT UNSIGNED,
  `username`     VARCHAR(100),
  `email`        varchar(100),
  `pic`          varchar(200),
  `access_token` varchar(200),
  KEY (`id`)
);
