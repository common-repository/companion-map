<?php
namespace m1\usv;

	class MitgliederController extends SystemController
	{
		/*
		 *	zeigt ein Formular zum Erfassen eines neuen Datensatzes
		*/
		public function addAction()
		{
				
			$this->render('Mitglieder','add');
				
		}
		
		/*
		 *	Löscht den angegebenen Datensatz
		*/
		public function deleteAction()
		{
			// Deletefunktion für mehrere Datens�tze
			if(isSizedArray($_REQUEST['mitgliedcheck']))
			{
		
				foreach($_REQUEST['mitgliedcheck'] as $value)
				{
					$this->current_id = $this->db->UpdateQuery($this->tbl_mtgl,array('deleted'=>'1'),"`id`='".$value."'");
				}
		
				$this->setPosMessage('Datensatz erfolgreich gel�scht');
					
			}
				
				
			// Delete-Funktion für einzelnen Datensatz
			if(isset($_REQUEST['del_id']) && is_numeric($_REQUEST['del_id']))
			{
		
				$delete = q($_REQUEST['del_id']);
		
				$this->current_id = $this->db->UpdateQuery($this->tbl_mtgl,array('deleted'=>'1'),"`id`='".$delete."'");
		
				$this->setPosMessage('Datensatz erfolgreich gel�scht');
		
			}
				
			$this->redirect('Mitglieder','index');
		}
		
		/*
		 *	zeigt ein Formular zum Bearbeiten der Einträge
		*/
		public function editAction($id = false)
		{
			if(isset($_REQUEST['id']))
			{
		
				$this->edit_id = q($_REQUEST['id']);
		
			}else{
		
				$this->edit_id = $id;
					
			}
				
			$this->arData = $this->db->fetchRow("
					SELECT
						*
					FROM
						`".$this->tbl_mtgl."`
					WHERE
						`id` = ".$this->edit_id."
				");
				
			$this->render('Mitglieder','edit');
		
		}
		
		/*
		 *	zeigt die Mitglieder des Unternehmens im Frontend an
		*/
		public function show_FE_Page()
		{
			require_once CONTROLLER_PATH.'/ConfigController.class.php';
		
			$CFC = new ConfigController();
			$CFC->init();
		
			$this->configArray = $CFC->initConfig();
				
			$this->arData = $this->db->fetchAssoc("
				SELECT
					*
				FROM
					`".$this->tbl_mtgl."`
				WHERE
					deleted = '0'
				");
				
				
			/* Was wurde als Darstellung eingetragen? */
			$show = $this->db->fetchOne("SELECT `wert` FROM `".$this->tbl_conf."` WHERE `name` = 'darstellung' ");
		
			$this->view['show'] = $show;
		
			$this->render('Mitglieder','userview');
		
		}
		
		/*
		*	zeigt eine Übersicht aller Datensätze
		*/
		public function indexAction()
		{

			if(isset($_REQUEST['orderby']) && $_REQUEST['orderby'] != '')
			{
				$this->orderby = htmlspecialchars($_REQUEST['orderby']);
			
			}else{
				
				$this->orderby = 'unternehmen';
			
			}
			
			
			$strQueryWHERE = "";
			
			/*
			* Suchfunktion 
			*/
			if (isset($_REQUEST['search']) && $_REQUEST['search'] != '') 
			{
				
				$strQueryWHERE .= " 
					AND (
						`unternehmen` LIKE '%".q($_REQUEST['search'])."%' OR 
						`ansprechpartner` LIKE '%".q($_REQUEST['search'])."%' OR
						`strasse` LIKE '%".q($_REQUEST['search'])."%' OR
						`plz` LIKE '%".q($_REQUEST['search'])."%' OR
						`ort` LIKE '%".q($_REQUEST['search'])."%'
					)
				";
				
			}	
			
			if(isset($_REQUEST['order']) && $_REQUEST['order'] != '')
			{
				$this->order = htmlspecialchars($_REQUEST['order']);
			
			}else{
				
				$this->order = 'DESC';
			
			}			
			
			$this->arData = $this->db->fetchAssoc("
				SELECT
					*
				FROM
					`".$this->tbl_mtgl."`
				WHERE
					deleted = '0'
					".$strQueryWHERE."
				ORDER BY
					".$this->orderby."
					".$this->order."

			");

			$this->render('Mitglieder','index');
		}
		
		/*
		 * Verarbeitung des Ajax Request für die GPS-Koordinaten
		*/
		public function get_gps_dataAction($address = false)
		{
				
			//Funktionsteil für den AJAX Request
			if($_REQUEST['strasse'] != '' && $_REQUEST['plz'] != '' && $_REQUEST['ort'] != '' && $address == false)
			{
				$address = q($_REQUEST['strasse']).', ';
				$address .= q($_REQUEST['plz']).' ';
				$address .= q($_REQUEST['ort']).' ';
		
				//Curl Optionen setzen und Aufruf starten
				$str  		= array('Accept-Language: '.$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
				$curl_req 	= curl_init();
		
				curl_setopt($curl_req, CURLOPT_URL, 'http://maps.googleapis.com/maps/api/geocode/json?address='.rawurlencode($address));
		
				curl_setopt($curl_req, CURLOPT_HTTPHEADER, $str);
				curl_setopt($curl_req, CURLOPT_CONNECTTIMEOUT, 4);
				curl_setopt($curl_req, CURLOPT_RETURNTRANSFER, TRUE);
				$curl_res = json_decode(curl_exec($curl_req), true);
		
				foreach ($curl_res as $res)
				{
					$arResult[]= $res;
				}
				$geo['lat'] = $arResult[0][0]['geometry']['location']['lat'];
				$geo['lng'] = $arResult[0][0]['geometry']['location']['lng'];
				$data = implode(',', $geo);
		
				die($data);
		
			}
				
			//Curl Optionen setzen und Aufruf starten
			$str  		= array('Accept-Language: '.$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			$curl_req 	= curl_init();
		
			curl_setopt($curl_req, CURLOPT_URL, 'http://maps.googleapis.com/maps/api/geocode/json?address='.rawurlencode($address));
			curl_setopt($curl_req, CURLOPT_HTTPHEADER, $str);
			curl_setopt($curl_req, CURLOPT_CONNECTTIMEOUT, 4);
			curl_setopt($curl_req, CURLOPT_RETURNTRANSFER, TRUE);
			$curl_res = json_decode(curl_exec($curl_req), true);
				
			foreach ($curl_res as $res)
			{
				$arResult[]= $res;
			}
			$geo['lat'] = $arResult[0][0]['geometry']['location']['lat'];
			$geo['lng'] = $arResult[0][0]['geometry']['location']['lng'];
				
			return $geo;
		}	
		
		/*
		*	speichert die erfassten Daten in die Datenbank
		*/
		public function saveAction()
		{

			if(isset($_REQUEST['new-mtgl']) && $_REQUEST['unternehmen'] != '' && $_REQUEST['ansprechpartner'] != '' && $_REQUEST['telefon'] != '')
			{
				$arData = array(
					'unternehmen' 		=> q($_REQUEST['unternehmen']),
					'ansprechpartner' 	=> q($_REQUEST['ansprechpartner']),
					'strasse' 			=> q($_REQUEST['strasse']),
					'plz' 				=> q($_REQUEST['plz']),
					'ort' 				=> q($_REQUEST['ort']),
					'telefon' 			=> q($_REQUEST['telefon']),
					'fax' 				=> q($_REQUEST['fax']),
					'email' 			=> q($_REQUEST['email']),
					'www'				=> q($_REQUEST['www']),
					'gps-lat'			=> q($_REQUEST['gps-lat']),
					'gps-lon' 			=> q($_REQUEST['gps-lon']),
					'create' 			=> time()			
				);
				
				$this->current_id = $this->db->ImportQuery( $this->tbl_mtgl , $arData );
				
				$this->setPosMessage('Datensatz erfolgreich hinzugefügt.');
				
				$this->indexAction();
				
			}else{
				
				$this->setNegMessage('Hinzufügen fehlgeschlagen, bitte prüfen Sie Ihre Angaben');

				$this->render( 'Mitglieder' , 'add' );
					
			}
			
		}

		/*
		*	übernimmt die geänderten Angaben 
		*/
		public function updateAction()
		{
			
			if(isset($_REQUEST['actiontop']) && $_REQUEST['actiontop'] || (@$_REQUEST['actionbottom']) && @$_REQUEST['actionbottom'] == 'delete')
			{
				
				$this->deleteAction();
				$this->redirect('Mitglieder', 'index');
			
			}
			
			if(isset($_REQUEST['update-mtgl']))
			{
				$where = "`id` = '".q($_REQUEST['mtgl_id'])."'";
				
				$arData = array(
					'unternehmen' 		=> q($_REQUEST['unternehmen']),
					'ansprechpartner' 	=> q($_REQUEST['ansprechpartner']),
					'strasse' 			=> q($_REQUEST['strasse']),
					'plz' 				=> q($_REQUEST['plz']),
					'ort' 				=> q($_REQUEST['ort']),
					'telefon' 			=> q($_REQUEST['telefon']),
					'fax' 				=> q($_REQUEST['fax']),
					'email' 			=> q($_REQUEST['email']),
					'www' 				=> q($_REQUEST['www']),
					'gps-lat'			=> q($_REQUEST['gps-lat']),
					'gps-lon' 			=> q($_REQUEST['gps-lon']),
					'create' 			=> time()			
				);
				
				$this->current_id = $this->db->UpdateQuery( $this->tbl_mtgl , $arData , $where);
				
				$this->setPosMessage('Änderungen wurden erfolgreich übernommen');
				
				$this->editAction(q($_REQUEST['mtgl_id']));
				
			}else{
				
				$this->setNegMessage('Änderungen wurden nicht übernommen');
				$this->redirect('Mitglieder','index');
			
			}
			
		} 

	}
?>