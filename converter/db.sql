CREATE TABLE `city` (
  `id` char(36) CHARACTER SET ascii NOT NULL,
  `country_iso3` char(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `latitude` decimal(7,4) NOT NULL,
  `longitude` decimal(7,4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city_country_iso3_IDX` (`country_iso3`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `zones` (
    `city_id` char(36) CHARACTER SET ascii NOT NULL,
    `offset` integer NOT NULL,
    `utc_start` integer NOT NULL,
    `utc_end` integer,
    `local_start` datetime NOT NULL,
    `local_end` datetime,
    `dst` boolean default false,
    PRIMARY KEY (`city_id`, `utc_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
