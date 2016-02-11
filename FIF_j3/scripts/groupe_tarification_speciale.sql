CREATE TABLE `groupe_tarification_speciale` (
  `gts_id` tinyint NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `gts_nom` varchar(50) NOT NULL,
  `gts_date_creation` datetime NOT NULL,
  `gts_date_derniere_modification` datetime NOT NULL,
  `gts_tarif_hc` tinyint NOT NULL,
  `gts_description` text NULL
);

CREATE TABLE `client_tarification_speciale` (
  `cts_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cts_client_id` int NOT NULL,
  `cts_gts_id` tinyint NOT NULL,
  `cts_date_modification` datetime NOT NULL
);