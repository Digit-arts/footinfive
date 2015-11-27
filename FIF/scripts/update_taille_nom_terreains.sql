ALTER TABLE `Terrain`
CHANGE `nom` `nom` varchar(10) COLLATE 'utf8_general_ci' NOT NULL AFTER `nom_long`;

UPDATE `Terrain` SET
`nom` = 'Loge VIP'
WHERE `id` = '5';