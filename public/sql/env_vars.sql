# USE pcit;

# DROP TABLE IF EXISTS env_vars;

CREATE TABLE IF NOT EXISTS `env_vars` (
  `id`       BIGINT AUTO_INCREMENT,
  `git_type` VARCHAR(100),
  `rid`      BIGINT UNSIGNED,
  `name`     VARCHAR(255),
  `value`    TEXT,
  `public`   INT UNSIGNED,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`git_type`, `rid`, `name`)
);
