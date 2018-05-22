USE khsci;

# DROP TABLE IF EXISTS `cron`;

# one branch one cron

CREATE TABLE IF NOT EXISTS `cron` (
  `id`       BIGINT AUTO_INCREMENT,
  `git_type` VARCHAR(100),
  `rid`      BIGINT UNSIGNED,
  `branch`   VARCHAR(200),
  `interval` VARCHAR(100),
  `always`   INT,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`git_type`, `rid`, `branch`)
);

