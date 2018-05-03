/**

Build 表

 */

USE khsci;

DROP TABLE IF EXISTS `builds`;

CREATE TABLE `builds` (
  `id`                 BIGINT AUTO_INCREMENT,
  `git_type`           VARCHAR(20),
  `event_type`         VARCHAR(20), /* push tag pr */
  `ref`                VARCHAR(100),
  `branch`             VARCHAR(100),
  `tag_name`           VARCHAR(100),
  `pull_id`            BIGINT,
  `compare`            VARCHAR(200),
  `commit_id`          VARCHAR(200),
  `commit_message`     LONGTEXT,
  `committer_name`     VARCHAR(100),
  `committer_email`    VARCHAR(100),
  `committer_username` VARCHAR(100),
  `username`           VARCHAR(100),
  `rid`                BIGINT UNSIGNED,
  `event_time`         BIGINT UNSIGNED,
  `create_time`        BIGINT UNSIGNED,
  `end_time`           BIGINT UNSIGNED,
  `build_status`       VARCHAR(20), /* pending | canceled | passed | errored | failed | skip | inactive */
  `request_raw`        JSON,
  `config`             JSON,
  `build_log`          TEXT,
  KEY (`id`)
);
