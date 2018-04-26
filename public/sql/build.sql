/**

Build 表

 */

USE khsci;

DROP TABLE IF EXISTS `builds`;

CREATE TABLE `builds` (
  `id`             BIGINT AUTO_INCREMENT,
  `git_type`       VARCHAR(20),
  `event_type`     varchar(20), /* push tag pr */
  `commit`         VARCHAR(200),
  `compare`        varchar (200),
  `commit_message` varchar(100),
  `branch`         VARCHAR(20),
  `username`       VARCHAR(20),
  `create_time`    BIGINT,
  `end_time`       BIGINT,
  `status`         varchar(20), /* canceld passed errored failed */
  KEY (`id`)
);
