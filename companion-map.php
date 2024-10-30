<?php
	/*
	Plugin Name: Companion-Map
	Description: Darstellung von hinterlegten Adressen unterschiedlicher Filialstandorte in einer GoogleMaps-Karte und/oder in Tabellenform
	Author: maennchen1.de
	Version: 2.0.2
	Author: http://maennchen1.de
	*/
	
	define('PLUGIN_NAME'		, 	'companion-map');
	
	// PFADE
	define('PLUGIN_PATH'		, 	ABSPATH.'wp-content/plugins/'.PLUGIN_NAME);
	define('CONTROLLER_PATH'	, 	PLUGIN_PATH.'/controllers');
	define('SCRIPT_PATH'		, 	PLUGIN_PATH.'/scripts');
	define('LIBRARY_PATH'		, 	PLUGIN_PATH.'/lib');
	define('MYSQLI_PATH' 		,	PLUGIN_PATH.'/lib/MySQLi');
	define('VIEWS_PATH' 		,	PLUGIN_PATH.'/views/Mitglieder');
	
	require_once(SCRIPT_PATH.'/db.inc');
	require_once(SCRIPT_PATH.'/function.php');
	require_once(MYSQLI_PATH.'/Database.php');
	require_once(CONTROLLER_PATH.'/SystemController.class.php');
	require_once(CONTROLLER_PATH.'/MitgliederController.class.php');
		
	/*
	*	Auto-Load-Funktion
	*/	
	function __autoload($class)
	{
		
		$classPathLib = LIBRARY_PATH.'/'.preg_replace('/\_/', '/', $class).'.php';
		
		try
		{
			if(file_exists($classPathLib))
			{
				require_once $classPathLib;
				return true;
			}	
	
		}
		catch(Exception $e)
		{
			return false;
		}
		
	}
		
		
	/*
	*	RUN-Funktion
	*/		
	function cm_run()
	{
		// 14 = Anzahl der Buchstaben vor dem eigentlichen Controllername (companion-map-)
		$controller = substr($_REQUEST['page'], 14);		
		$controller_class = $controller.'Controller';
		
		if (!file_exists(CONTROLLER_PATH.'/'.$controller_class.'.class.php')) die(_('Wrong Controller ('.CONTROLLER_PATH.'/'.$controller_class.'.class.php'.') !'));
		require_once CONTROLLER_PATH.'/'.$controller_class.'.class.php';
	
		$controller_class = 'm1\\usv\\'.$controller.'Controller';
				
		if (!isset($_REQUEST['action'])) $action = 'index'; 
		else $action = $_REQUEST['action'];	
 
		$CTR = new $controller_class();		
		$CTR->init();
		
		if (!is_subclass_of($CTR, 'm1\usv\SystemController'))
		{
			die(_('Controller could not be loaded !'));
		}
		
		$action_method = $action.'Action';
		
		if (!in_array($action_method, get_class_methods(get_class($CTR))))
		{
			die(_('The requested Action doesnt exist !'));
		}	 
		
		$CTR->$action_method();
		
	}
	
	
	/*
	 * Fügt einen Menuepunkt im Backendseitenmenue ein
	 */
	function companion_mapAddMenu()
	{
		
		add_menu_page('Companion Map', 'Companion Map', 'companion_map', 'companion_map-System', 'cm_run', SITECOOKIEPATH.'wp-content/plugins/companion-map/gfx/logo.png', 9);
		add_submenu_page('companion_map-System', 'Uebersicht', 'Übersicht', 'administrator', 'companion_map-Mitglieder', 'cm_run');
		add_submenu_page('companion_map-System', 'Hinzufuegen', 'Neue Firma hinzufügen', 'administrator', 'companion_map-Mitglieder&action=add', 'cm_run');
		add_submenu_page('companion_map-System', 'Import', 'csv-Datei importieren', 'administrator', 'companion_map-Import', 'cm_run');
		add_submenu_page('companion_map-System', 'Settings', 'Konfiguration', 'administrator', 'companion_map-Config', 'cm_run');
		
	}
	
	add_action('admin_menu','companion_mapAddMenu');
	
	/*
	 * Registriert die Installationsmethode im Wordpress
	*/
	function companion_map_install()
	{
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	
		$SC = new m1\usv\SystemController("", "", false);
		$SC->init();
		$SC->install();
	
	}
	register_activation_hook(__FILE__, 'companion_map_install');
	
	/*
	 * Einfügen der Inhalte per shortcode
	 */
	function companion_map_shortcode($atts, $content = '')
	{
		require_once CONTROLLER_PATH.'/MitgliederController.class.php';
		
		$MC = new m1\usv\MitgliederController();
		$MC->init();
		
		return $MC->show_FE_Page();
	}
	
	
	/*
	 * Definiert den Shortcode [companion-map]
	 */
	add_shortcode('companion-map', 'companion_map_shortcode');
	
	
	/*
	 * Google API einbinden
	 */
	function hook_ajax_script()
	{ 
		
		global $wpdb;

		/* Daten aus der Config-Table mit Wert: Googlekey holen*/
		$db = new m1\usv\MySQLi_Database();
		$key = $db->fetchOne("SELECT `wert` FROM `".$wpdb->prefix.'companion_map_config'."` WHERE `name` = 'googlekey'");
		
		wp_enqueue_script('google_maps_script', 'http://maps.google.com/maps/api/js?key='.$key);
		
		/* Einbinden der style.css */
		wp_enqueue_style('usv-admin-style', SITECOOKIEPATH.'wp-content/plugins/companion-map/css/style.css');
	
	}
	
	add_action( 'admin_enqueue_scripts', 'hook_ajax_script' );
	add_action( 'wp_enqueue_scripts', 'hook_ajax_script' );
	
?>