# USE `pcit`;

# DROP TABLE IF EXISTS `settings`;

CREATE TABLE settings (
  `id`                              BIGINT AUTO_INCREMENT,
  `git_type`                        VARCHAR(20),
  `rid`                             BIGINT,
  `build_pushes`                    INT    DEFAULT 1,
  `build_pull_requests`             INT    DEFAULT 1,
  `maximum_number_of_builds`        INT    DEFAULT 1,
  `auto_cancel_branch_builds`       INT    DEFAULT 0,
  `auto_cancel_pull_request_builds` INT    DEFAULT 0,
  KEY (`id`)
);
