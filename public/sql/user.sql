/**

  用户表


 */

CREATE TABLE `user` (
  `id`   BIGINT AUTO_INCREMENT,
  `git`  varchar(20),
  `uid`  BIGINT UNSIGNED,
  `name` VARCHAR(100),
  `pic`  varchar(200),
  KEY (`id`)
);