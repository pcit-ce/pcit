# USE khsci;

# DROP TABLE IF EXISTS `builds`;

CREATE TABLE IF NOT EXISTS `builds` (
  `id`                  BIGINT       AUTO_INCREMENT,
  `number`              FLOAT,
  `git_type`            VARCHAR(20)  DEFAULT 'github_app',
  `rid`                 BIGINT UNSIGNED,
  `event_type`          VARCHAR(20)  DEFAULT 'push'
  COMMENT 'push tag pr',
  `build_status`        VARCHAR(20)  DEFAULT 'pending'
  COMMENT 'pending | canceled | passed | errored | failed | skip | inactive',
  `branch`              VARCHAR(100) DEFAULT 'master',
  `tag`                 VARCHAR(100),
  `pull_request_title`  VARCHAR(100),
  `pull_request_number` BIGINT,
  `pull_request_source` VARCHAR(200),
  `compare`             VARCHAR(200),
  `commit_id`           VARCHAR(200),
  `commit_message`      LONGTEXT,
  `committer_name`      VARCHAR(100),
  `committer_email`     VARCHAR(100),
  `committer_username`  VARCHAR(100),
  `author_name`         VARCHAR(100),
  `author_email`        VARCHAR(100),
  `author_username`     VARCHAR(100),
  `created_at`          BIGINT UNSIGNED,
  `started_at`          BIGINT UNSIGNED,
  `finished_at`         BIGINT UNSIGNED,
  `deleted_at`          BIGINT UNSIGNED,
  `config`              JSON,
  `action`              VARCHAR(100),
  `check_suites_id`     BIGINT,
  `check_run_id`        BIGINT,
  `internal`            INT UNSIGNED DEFAULT 1,
  `private`             INT          DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`git_type`, `rid`, `event_type`, `branch`, `commit_id`)
);
