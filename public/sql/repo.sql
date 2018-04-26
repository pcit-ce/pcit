/**
  仓库表
 */

USE khsci;

DROP TABLE IF EXISTS repo;

CREATE TABLE `repo` (
  `id`   BIGINT AUTO_INCREMENT,
  `git_type`  VARCHAR(20),                 /* github coding gitee */
  `rid`  BIGINT,
  `username` varchar(100),
  `repo_prefix` VARCHAR(100),
  `repo_name` VARCHAR(100),
  `repo_full_name` VARCHAR(100),
  `webhooks_status` VARCHAR(20),           /* on->1 off->0 */
  `last_sync` BIGINT,                      /* 最后同步时间 */
  KEY (`id`)
);
