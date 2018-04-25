/**
  仓库表
 */

CREATE TABLE `repo` (
  `id`   BIGINT AUTO_INCREMENT,
  `git`  VARCHAR(20),             # github coding gitee
  `rid`  BIGINT UNIQUE,
  `name` VARCHAR(100),
  `build` VARCHAR(20),            # on off
  `last_sync` BIGINT,             # 最后同步时间
  KEY (`id`)
);
