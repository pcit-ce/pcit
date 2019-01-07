# USE PCIT;

DROP TABLE IF EXISTS `default_services`;

CREATE TABLE `default_services`
(
  `service`    VARCHAR(200),
  `image`      VARCHAR(200),
  `env`        JSON,
  `entrypoint` JSON,
  `commands`   JSON,
  UNIQUE KEY (`service`)
);

INSERT INTO `default_services`
values ('mysql',
        'mysql:5.7.24',
        JSON_ARRAY('MYSQL_DATABASE=test', 'MYSQL_ROOT_PASSWORD=mytest'),
        JSON_ARRAY(),
        JSON_ARRAY('--character-set-server=utf8mb4', '--default-authentication-plugin=mysql_native_password'));

INSERT INTO `default_services`
values ('redis',
        'redis:5.0.3-alpine',
        JSON_ARRAY(),
        JSON_ARRAY(),
        JSON_ARRAY('--bind', '0.0.0.0'));
