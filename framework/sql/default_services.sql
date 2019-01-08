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
VALUES ('mysql',
        'mysql:5.7.24',
        JSON_ARRAY('MYSQL_DATABASE=test', 'MYSQL_ROOT_PASSWORD=mytest'),
        JSON_ARRAY(),
        JSON_ARRAY('--character-set-server=utf8mb4', '--default-authentication-plugin=mysql_native_password'));

INSERT INTO `default_services`
VALUES ('redis',
        'redis:5.0.3-alpine',
        JSON_ARRAY(),
        JSON_ARRAY(),
        JSON_ARRAY('--bind', '0.0.0.0'));

INSERT INTO `default_services`
VALUES ('rabbitmq',
        'rabbitmq:3.7.8-management-alpine',
        JSON_ARRAY(),
        JSON_ARRAY(),
        JSON_ARRAY()),
       ('memcached',
        'memcached:1.5.12-alpine',
        JSON_ARRAY(),
        JSON_ARRAY(),
        JSON_ARRAY()),
       ('mariadb',
        'mariadb:10.4.1-bionic',
        JSON_ARRAY('MYSQL_DATABASE=test', 'MYSQL_ROOT_PASSWORD=test'),
        JSON_ARRAY(),
        JSON_ARRAY()),
       ('postgresql',
        'postgres:11.1-alpine',
        JSON_ARRAY('POSTGRES_PASSWORD=test', 'POSTGRES_USER=test', 'POSTGRES_DB=test'),
        JSON_ARRAY(),
        JSON_ARRAY()),
       ('couchdb',
        'couchdb:2.3.0',
        JSON_ARRAY(),
        JSON_ARRAY(),
        JSON_ARRAY()),
       ('riak',
        'riak',
        JSON_ARRAY(),
        JSON_ARRAY(),
        JSON_ARRAY()),
       ('neo4j',
        'neo4j:3.5.1',
        JSON_ARRAY(),
        JSON_ARRAY(),
        JSON_ARRAY()),
       ('elasticsearch',
        'elasticsearch:6.5.4',
        JSON_ARRAY('discovery.type=single-node'),
        JSON_ARRAY(),
        JSON_ARRAY()),
       ('rethinkdb',
        'rethinkdb:2.3.6',
        JSON_ARRAY(),
        JSON_ARRAY(),
        JSON_ARRAY());
