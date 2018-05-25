# use khsci;

# DROP TABLE IF EXISTS `api_token`;

CREATE TABLE if NOT EXISTS `api_token` (
  `id`         BIGINT AUTO_INCREMENT,
  `api_token`  VARCHAR(200),
  `git_type`   VARCHAR(20),
  `uid`        BIGINT UNSIGNED,
  `created_at` BIGINT,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`api_token`, `git_type`)
);
