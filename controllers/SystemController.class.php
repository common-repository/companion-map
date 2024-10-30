<?php
namespace m1\usv;
	class SystemController
	{
		
		public $controllerName;
		public $actionName;
		public $db;	
		public $view;
		 				
		public function __construct($controller = '', $action = '')
		{
						
			$this->controllerName = $controller;
			$this->actionName = $action; 
					
		}
		
		
		public function init()
		{
			
			global $wpdb;
			
			$this->db = new MySQLi_Database();
			
			$this->tbl_mtgl = $wpdb->prefix.'companion_map_mitglieder';
			$this->tbl_conf = $wpdb->prefix.'companion_map_config';
			
		} 
		
		
		/*
		*	Installiert das Plugin in der Wordpressinstanz
		*	legt das Verzeichnis ../data/companion-map an
		*/
		public function install()
		{
 
			include WP_PLUGIN_DIR.'/companion-map/scripts/db.inc';
			
			if(!file_exists(ABSPATH.'wp-content/data/companion-map'))
			{
				if(!mkdir(ABSPATH.'wp-content/data/companion-map', 0770, true))
				{
					die('Fehler: Das Verzeichnis '.ABSPATH.'wp-content/uploads/data konnte nicht erstellt werden');
				}

			}			
			
		} 
		
		/*
		 * Weiterleitung an Controller-Action
		*/
		public function redirect($controller, $action, $param = '')
		{
				
			header("Location:".SITECOOKIEPATH."wp-admin/admin.php?page=companion_map-".$controller."&action=".$action.$param);
			exit;
				
		}
		
		/*
		 * Weiterleitung an Template-phtml-Datei
		 */
		public function render($controller = '', $action = '')
		{
			
			if ($controller == false) $controller = $this->controllerName;
			if ($action == false) $action = $this->actionName;

			if (file_exists(WP_PLUGIN_DIR."/companion-map/views/".$controller."/".$action.".phtml"))
			{					
				include WP_PLUGIN_DIR."/companion-map/views/".$controller."/".$action.".phtml";
			}
			else
			{
				die(_('Templatedatei existiert nicht!'));
			}
			
		} 
		
		/*
		 * Benutzermeldung für erfolgreich ausgeführte Aktion setzen
		 */
		public function setPosMessage($message)
		{
			
			$_SESSION['PosMessage'][] = $message;
			
		}
		
		
		/*
		 * Benutzermeldung für fehlgeschlagene Aktion setzen
		 */
		public function setNegMessage($message)
		{
			
			$_SESSION['NegMessage'][] = $message;
			
		} 
		
		
		/*
		 * Ausgabe der hinterlegten Benutzerhinweise
		 */
		public function drawMessages()
		{				
			
			if (isSizedArray($_SESSION['PosMessage']))
			{
				echo '<ul class="posMessage">';
				foreach ($_SESSION['PosMessage'] as $m)
				{
					echo '<li>'.$m.'</li>';
				}
				echo '</ul>';
			}
			
			if (isSizedArray($_SESSION['NegMessage']))
			{
				echo '<ul class="negMessage">';
				foreach ($_SESSION['NegMessage'] as $m)
				{
					echo '<li>'.$m.'</li>';
				}
				echo '</ul>';
			}
			
			unset($_SESSION['NegMessage']);
			unset($_SESSION['PosMessage']);
			
		} 

	}
?>