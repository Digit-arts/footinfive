CREATE TABLE `groupe_tarification_speciale` (
  `gts_id` tinyint NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `gts_nom` varchar(50) NOT NULL,
  `gts_date_creation` date NOT NULL,
  `gts_date_derniere_modification` date NOT NULL,
  `gts_tarif_hc` tinyint NOT NULL,
  `gts_description` text NULL
);

CREATE TABLE `client_tarification_speciale` (
  `cts_client_id` int NOT NULL,
  `cts_gts_id` tinyint NOT NULL,
  `cts_date_modification` date NOT NULL;
);

ALTER TABLE `client_tarification_speciale`
ADD PRIMARY KEY `cts_client_id_cts_gts_id` (`cts_client_id`, `cts_gts_id`);