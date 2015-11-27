-- Adminer 4.2.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `Mode_Reservation`;
CREATE TABLE `Mode_Reservation` (
  `mode_id` int(11) NOT NULL AUTO_INCREMENT,
  `mode_name` varchar(50) NOT NULL,
  PRIMARY KEY (`mode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `Mode_Reservation` (`mode_id`, `mode_name`) VALUES
(1,	'Avec acompte'),
(2,	'Avec caution'),
(3,	'Sans acompte ni caution');

-- 2015-10-17 15:05:46

ALTER TABLE `Reservation`
ADD `id_mode_reservation` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Mode de reservation';

ALTER TABLE `Reservation`
ADD `montant_sans_remise` float NOT NULL COMMENT 'Montant avant remise';