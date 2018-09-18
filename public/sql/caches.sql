# USE khsci;

# DROP TABLE IF EXISTS `caches`;

# one branch one caches;

CREATE TABLE IF NOT EXISTS `cron` (
  `id`         BIGINT AUTO_INCREMENT,
  `git_type`   VARCHAR(100),
  `rid`        BIGINT UNSIGNED,
  `branch`     VARCHAR(200),
  `file_name`  VARCHAR(100),
  `build_path` VARCHAR(255),
  PRIMARY KEY (`id`),
  UNIQUE KEY (`git_type`, `rid`, `branch`, `build_path`)
);
