USE khsci;

DROP TABLE IF EXISTS repo;

CREATE TABLE `repo` (
  `id`              BIGINT       AUTO_INCREMENT,
  `git_type`        VARCHAR(20) COMMENT 'github coding gitee',
  `rid`             BIGINT UNSIGNED,
  `username`        varchar(100),
  `repo_prefix`     VARCHAR(100),
  `repo_name`       VARCHAR(100),
  `repo_full_name`  VARCHAR(100),
  `webhooks_status` INT UNSIGNED COMMENT 'Webhooks 是否开启  1 -> on    0 -> off',
  `build_activate`  INT UNSIGNED COMMENT '是否打开构建       1 -> on    0 -> off',
  `star`            INT UNSIGNED DEFAULT 0 COMMENT '是否收藏 1 -> star  0 -> unstar',
  `last_sync`       BIGINT UNSIGNED COMMENT '最后同步时间',
  KEY (`id`)
);
