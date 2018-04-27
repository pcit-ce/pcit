/**
  仓库表
 */

USE khsci;

DROP TABLE IF EXISTS repo;

CREATE TABLE `repo` (
  `id`              BIGINT       AUTO_INCREMENT,
  `git_type`        VARCHAR(20), /* github coding gitee */
  `rid`             BIGINT UNSIGNED,
  `username`        varchar(100),
  `repo_prefix`     VARCHAR(100),
  `repo_name`       VARCHAR(100),
  `repo_full_name`  VARCHAR(100),
  `webhooks_status` INT UNSIGNED, /* Webhooks 是否开启  1 -> on    0 -> off */
  `build_activate`  INT UNSIGNED, /* 是否打开构建       1 -> on    0 -> off */
  `star`            INT UNSIGNED DEFAULT 0, /* 用户是否收藏       1 -> star  0 -> unstar */
  `last_sync`       BIGINT UNSIGNED, /* 最后同步时间 */
  KEY (`id`)
);
