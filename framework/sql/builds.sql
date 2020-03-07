# USE pcit;

# DROP TABLE IF EXISTS `builds`;

CREATE TABLE IF NOT EXISTS `builds`
(
  `id`                  BIGINT AUTO_INCREMENT,
  `git_type`            VARCHAR(20)  DEFAULT 'github',
  `rid`                 BIGINT UNSIGNED,
  `event_type`          VARCHAR(20)  DEFAULT 'push'
    COMMENT 'push | tag | pr',
  `build_status`        VARCHAR(20)  DEFAULT 'pending'
    COMMENT 'pending | queued | skip | inactive | GITHUB_CHECK_SUITE_CONCLUSION',
  `branch`              VARCHAR(100) DEFAULT 'master',
  `tag`                 VARCHAR(100),
  `pull_request_title`  VARCHAR(100),
  `pull_request_number` BIGINT,
  `pull_request_source` VARCHAR(200),
  `compare`             VARCHAR(200),
  `commit_id`           VARCHAR(200),
  `commit_message`      LONGTEXT,
  `committer_uid`       VARCHAR(100),
  `committer_name`      VARCHAR(100),
  `committer_username`  VARCHAR(100),
  `committer_email`     VARCHAR(100),
  `committer_pic`       VARCHAR(255),
  `author_uid`          VARCHAR(100),
  `author_name`         VARCHAR(100),
  `author_username`     VARCHAR(100),
  `author_email`        VARCHAR(100),
  `author_pic`          VARCHAR(255),
  `created_at`          BIGINT UNSIGNED,
  `finished_at`         BIGINT UNSIGNED,
  `deleted_at`          BIGINT UNSIGNED,
  `config`              TEXT,
  `action`              VARCHAR(100),
  `check_suites_id`     BIGINT,
  `internal`            INT UNSIGNED DEFAULT 1,
  `private`             BIGINT       DEFAULT 0,
  `unique_key`          BIGINT       DEFAULT 0,
  UNIQUE KEY (`git_type`, `rid`, `event_type`, `branch`, `commit_id`, `unique_key`),
  PRIMARY KEY (`id`)
);

ALTER TABLE builds
  MODIFY config TEXT;

ALTER TABLE `builds`
  ADD UNIQUE (`git_type`, `rid`, `event_type`, `branch`, `commit_id`, `unique_key`);

ALTER TABLE builds
  ADD `unique_key` BIGINT;

ALTER TABLE builds
  ADD `finished_at` BIGINT UNSIGNED AFTER `created_at`;
