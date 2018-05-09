USE khsci;

DROP TABLE IF EXISTS github_app_installation;

CREATE TABLE github_app_installation (
  `id`              BIGINT AUTO_INCREMENT,
  `installation_id` BIGINT UNIQUE,
  `repo`            JSON,
  `admin_user_id`   BIGINT,
  `access_token`    VARCHAR(255),
  `expires_time`    VARCHAR(255),
  KEY (`id`)
);
