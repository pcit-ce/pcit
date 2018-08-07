# USE khsci;

# DROP TABLE IF EXISTS `repo`;

CREATE TABLE IF NOT EXISTS `repo` (
  `id`                 BIGINT       AUTO_INCREMENT,
  `git_type`           VARCHAR(20) COMMENT 'github coding gitee',
  `rid`                BIGINT UNSIGNED,
  `repo_full_name`     VARCHAR(100),
  `webhooks_status`    INT UNSIGNED COMMENT 'Webhooks 是否开启  1 -> on    0 -> off',
  `build_activate`     INT UNSIGNED COMMENT '是否打开构建       1 -> on    0 -> off',
  `repo_admin`         JSON,
  `repo_collaborators` JSON,
  `default_branch`     VARCHAR(200) DEFAULT 'master',
  `last_sync`          BIGINT UNSIGNED COMMENT '最后同步时间',
  `secrets`            JSON COMMENT '仓库密钥列表',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unique_repo` (`git_type`, `repo_full_name`)
);
