/**

Build 表

 */

USE khsci;

DROP TABLE IF EXISTS `builds`;

CREATE TABLE `builds` (
  `id`             BIGINT AUTO_INCREMENT,
  `git_type`       VARCHAR(20),
  `event_type`     VARCHAR(20), /* push tag pr */
  `commit`         VARCHAR(200),
  `compare`        VARCHAR(200),
  `commit_message` VARCHAR(100),
  `branch`         VARCHAR(20),
  `tag_name`       VARCHAR(100),
  `username`       VARCHAR(20),
  `repo_full_name` VARCHAR(200),
  `create_time`    BIGINT UNSIGNED,
  `end_time`       BIGINT UNSIGNED,
  `build_activate` VARCHAR(20), /* canceled | passed | errored | failed */
  `open`           INT UNSIGNED, /* 是否开启构建 0-> 关闭 1 -> 开启 */
  KEY (`id`)
);
