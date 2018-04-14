/**
  仓库表
 */
CREATE TABLE `repo` (
  `id`   BIGINT AUTO_INCREMENT,
  `git`  VARCHAR(20),
  `rid`  BIGINT unique,
  `name` VARCHAR(100),
  KEY (`id`)
);