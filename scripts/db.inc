<?php
/**
*	Erstellung aller benötigten Tabellen in der Datenbank
*/

require_once(ABSPATH.'wp-admin/includes/upgrade.php');


/*
*	Erstellen der Tabelle für die Mitgliederdaten eines Unternehmens
*/
$sql = "CREATE TABLE IF NOT EXISTS `".$table_prefix."companion_map_mitglieder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unternehmen` text NOT NULL,
  `ansprechpartner` text NOT NULL,
  `strasse` text NOT NULL,
  `plz` varchar(255) NOT NULL,
  `ort` text NOT NULL,
  `telefon` varchar(255) NOT NULL,
  `fax` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `www` varchar(255) NOT NULL,
  `gps-lat` double NOT NULL,
  `gps-lon` double NOT NULL,
  `create` int(11) NOT NULL,
  `deleted` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=89 ;";

dbDelta($sql);


/*
*	Erstellen der Tabelle für die Einstellungen
*/
$sql = "CREATE TABLE IF NOT EXISTS `".$table_prefix."companion_map_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `wert` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;";

dbDelta($sql);

?>