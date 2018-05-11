USE khsci;

DROP TABLE IF EXISTS repo;

CREATE TABLE `repo` (
  `id`                 BIGINT AUTO_INCREMENT,
  `git_type`           VARCHAR(20) COMMENT 'github github_app coding gitee',
  `rid`                BIGINT UNSIGNED,
  `repo_prefix`        VARCHAR(100),
  `repo_name`          VARCHAR(100),
  `repo_full_name`     VARCHAR(100),
  `webhooks_status`    INT UNSIGNED COMMENT 'Webhooks 是否开启  1 -> on    0 -> off',
  `build_activate`     INT UNSIGNED COMMENT '是否打开构建       1 -> on    0 -> off',
  `repo_admin`         JSON,
  `repo_collaborators` JSON,
  `default_branch`     VARCHAR(200) DEFAULT 'master',
  `installation_id`    BIGINT COMMENT 'github app only',
  `last_sync`          BIGINT UNSIGNED COMMENT '最后同步时间',
  `secrets`            JSON COMMENT '仓库密钥列表',
  KEY (`id`)
);
