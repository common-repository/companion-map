=== Companion-Map ===
Contributors: maennchen1.de
Donate link:
Tags: Adressverwaltung, Google Maps, Kartendarstellung, Maps, Datenschutzrichtlinien, Deutschland, deutsch
Requires at least: 4.3
Tested up to: 6.0.0
Stable tag: 2.0.3


Mit Hilfe dieses Plugins ist es möglich, Adressen innerhalb eines Kartenausschnitts von Google Maps darzustellen.

== Description ==

Mit Hilfe des Plugins Companion-Map ist es möglich, Adressdaten im Backend zu verwalten und 
sie mit Hilfe von Google Maps und dessen Marker im Frontend anzuzeigen. 
Neben der Google Maps Darstellung wird auch eine tabellarische Auflistung unterstützt. Damit hat der Nutzer die Wahl, 
die Adressdaten als Tabelle darzustellen, oder die Standorte in einer Karte anzuzeigen. Natürlich ist eine kombinierte 
Anzeigemöglichkeit ebenfalls wählbar. Hierbei wird die Karte mit den Standorten angezeigt, während die Tabelle mit den 
jeweiligen dazugehörigen Standortdaten darunter gelistet wird.  

== Installation ==

Zur Installation beachten Sie bitte folgende Schritte:

1. companion-map.zip entpacken und in den Ordner "wp-content/plugins" laden, oder über das Wordpress Backend: Plugins >
Installieren > Hochladen, die ZIP-Datei hochladen, oder direkt über die Suchfunktion des Backends
2. Das Plugin im Plugin Menü von Wordpress aktivieren
3. Unter Einstellungen "Eigener Standort" eine Adresse Angeben, welche zur Erzeugeung des Kartenausschnitts benötigt wird
4. Unter "Mitglieder" die Adressen einpflegen oder alternativ vorhandene Adressdaten als CSV Importieren
   (bei gültiger Adresse werden die Kartendaten automatisch ermittelt und eingetragen)

5. Mit dem shortcode [companion-map] kann das Plugin auf einer Seite oder im Post eingebunden werden

== Screenshots ==

1. Plugin Backend new

2. Plugin Frontend Overview 

3. CSV-Import upload form

== Changelog ==

= 2.0.3 = 
* bugfix: using with wordpress 5.0

= 2.0.2 =
* bugfix: remove notices

= 2.0.1 =
* bugfix: path variables

= 2.0 =
* Adapt to Google API
* Function adjustment for current WordPress

= 1.0 =
* Initial Release