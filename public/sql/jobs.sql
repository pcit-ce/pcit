# use khsci;

# DROP TABLE IF EXISTS `jobs`;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id`            BIGINT AUTO_INCREMENT KEY,
  `number`        FLOAT,
  `allow_failure` INT,
  `state`         VARCHAR(20),
  `started_at`    BIGINT,
  `finished_at`   BIGINT,
  `created_at`    BIGINT,
  `updated_at`    BIGINT,
  `deleted_at`    BIGINT UNSIGNED,
  `build`         BIGINT,
  `build_log`     LONGTEXT,
  `config`        TEXT,
  `private`       INT    DEFAULT 0
);
