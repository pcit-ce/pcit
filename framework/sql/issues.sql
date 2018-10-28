# USE pcit;

# DROP TABLE IF EXISTS `issues`;

CREATE TABLE IF NOT EXISTS `issues` (
  `id`           BIGINT AUTO_INCREMENT,
  `git_type`     VARCHAR(50),
  `rid`          BIGINT UNSIGNED,
  `issue_id`     BIGINT UNSIGNED,
  `comment_id`   BIGINT UNSIGNED,
  `issue_number` BIGINT UNSIGNED,
  `action`       varchar(100) COMMENT '新建 issue opened 用户评论 created closed labeled assigned',
  `title`        varchar(200),
  `body`         LONGTEXT,
  `sender_uid`   BIGINT UNSIGNED,
  `state`        varchar(100),
  `locked`       INT UNSIGNED,
  `assignees`    JSON,
  `labels`       JSON,
  `created_at`   BIGINT UNSIGNED,
  `updated_at`   BIGINT UNSIGNED,
  `closed_at`    BIGINT UNSIGNED,
  `deleted_at`   BIGINT UNSIGNED,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`git_type`, `rid`, `issue_id`, `comment_id`)
);
