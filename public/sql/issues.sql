USE khsci;

CREATE TABLE IF NOT EXISTS `issues` (
  `id`             BIGINT AUTO_INCREMENT,
  `rid`            BIGINT UNSIGNED,
  `issue_id`       BIGINT UNSIGNED,
  `issue_number`   BIGINT UNSIGNED,
  `action`         varchar(100) COMMENT '新建 issue opened 用户评论 created closed labeled assigned',
  `title`          varchar(200),
  `issue_username` VARCHAR(200),
  `issue_uid`      BIGINT UNSIGNED,
  `issue_pic`      VARCHAR(200),
  `labels`         VARCHAR(200),
  `state`          varchar(100),
  `locked`         INT UNSIGNED,
  `assigness`      VARCHAR(100),
  `created_time`   BIGINT UNSIGNED,
  `updated_time`   BIGINT UNSIGNED,
  `closed_time`    BIGINT UNSIGNED,
  PRIMARY KEY ('id')
);
