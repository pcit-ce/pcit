# USE pcit;

# DROP TABLE IF EXISTS `user`;

CREATE TABLE IF NOT EXISTS `user` (
  `id`           BIGINT       AUTO_INCREMENT,
  `git_type`     VARCHAR(20),
  `uid`          BIGINT UNSIGNED,
  `name`         VARCHAR(100),
  `username`     VARCHAR(100),
  `email`        varchar(100),
  `pic`          varchar(200),
  `access_token` varchar(200),
  `org_admin`    JSON,
  `type`         VARCHAR(100) DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`git_type`, `uid`)
);

# ALTER TABLE user
#   CHANGE `admin` `org_admin` JSON;

ALTER TABLE user
  ADD COLUMN `installation_id` BIGINT DEFAULT 0;
