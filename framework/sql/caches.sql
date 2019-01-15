# USE pcit;

# DROP TABLE IF EXISTS `caches`;

# one branch one caches;

CREATE TABLE IF NOT EXISTS `caches` (
  `id`         BIGINT AUTO_INCREMENT,
  `git_type`   VARCHAR(100),
  `rid`        BIGINT UNSIGNED,
  `branch`     VARCHAR(200),
  `filename`   VARCHAR(100),
  `updated_at` int,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`git_type`, `rid`, `branch`)
);
