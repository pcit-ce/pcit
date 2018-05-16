USE khsci;

# DROP TABLE IF EXISTS `builds`;

CREATE TABLE IF NOT EXISTS `builds` (
  `id`                 BIGINT AUTO_INCREMENT,
  `git_type`           VARCHAR(20),
  `rid`                BIGINT UNSIGNED,
  `event_type`         VARCHAR(20) COMMENT 'push tag pr',
  `ref`                VARCHAR(100),
  `branch`             VARCHAR(100),
  `tag_name`           VARCHAR(100),
  `pull_request_id`    BIGINT,
  `compare`            VARCHAR(200),
  `commit_id`          VARCHAR(200),
  `commit_message`     LONGTEXT,
  `committer_name`     VARCHAR(100),
  `committer_email`    VARCHAR(100),
  `committer_username` VARCHAR(100),
  `event_time`         BIGINT UNSIGNED,
  `started_at`         BIGINT UNSIGNED,
  `stopped_at`          BIGINT UNSIGNED,
  `build_status`       VARCHAR(20) COMMENT 'pending | canceled | passed | errored | failed | skip | inactive',
  `request_raw`        JSON,
  `config`             JSON,
  `build_log`          LONGTEXT,
  `action`             varchar(100),
  `stages`             VARCHAR(100),
  `matrix`             varchar(100),
  `check_suites_id`    BIGINT,
  `check_run_id`       BIGINT,
  PRIMARY KEY (`id`)
);
