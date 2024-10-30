<?php
namespace m1\usv;

	/**
	*	Feature: csv-Datei mit hinterlegten Standorten hochladbar
	*/

	define('csv_file','csv_file');
	define('name','name');
	define('DATA_PATH','DATA_PATH');

	class ImportController extends SystemController
	{
		public function indexAction()
		{
			if(!isset($_REQUEST['csv-import']))
			{
				$this->uploadAction();	
			}
		}
		
		
		/**
		* speichert die upload Datei und öffnet das Formular zum Zuordnen der Felder
		*/
		public function formAction()
		{
			
			if($_FILES[csv_file][name] == '' )
			{
				
				$this->setNegMessage('Es wurden keine zu Importierenden Daten gefunden');
				$this->uploadAction();
			
			}else{

				$this->tmp_csv_file 	= $_FILES['csv_file']['tmp_name'];
				$this->tmp_file_name 	= $_FILES['csv_file']['name'];
				$this->csv_separator 	= ';';
				
				if(!move_uploaded_file($this->tmp_csv_file, DATA_PATH.$this->tmp_file_name))
				{  
					
					$this->setNegMessage(_("Bitte überprüfen Sie Ihre Pfadangabe (Pfad existiert?) und die Rechte darauf!<br />Das momentane Upload-Verzeichnis liegt hier: ".DATA_PATH));
					$this->uploadAction();
					
				}else{
					
					$this->path = DATA_PATH.$this->tmp_file_name;
					
					if(isSizedString($this->path))
					{
						$row = 0;
	
						$handle = fopen($this->path, "r");
					
						/* Umwandlung der importierten Datei in UTF8-Format */
						function toUtf8($data)
						{
								
							if (is_array($data))
							{
						
								foreach ($data as $k => $v)
						
								{
						
									$data[$k]=toUtf8($v);
						
								}
									
								return $data;
									
							}else{
									
								return utf8_encode($data);
									
							}
								
						}
						
						/* Importiert die csv-Datei im ISO-Format */ 
						while ($row < 1 && ($data = fgetcsv($handle, 1024, $this->csv_separator)) !== FALSE) 
						{
	    					$num = count($data);
	    					$row++;
	    					$content[] = toUtf8($data);
						}
																		
						fclose($handle);
					}
					
					foreach($content as $key=>$value) {$this->arFelder = $value;}
					
			 		$this->setPosMessage('Daten wurden erfolgreich eingelesen.');
			 		
			 		$this->render('Import','form');	
				}
					
			}
					
		}
		
		/*
		*	Übergibt die Daten an die Datenbank
		*/		
		public function saveAction()
		{
			
			$this->path = DATA_PATH.q($_REQUEST['csv_file_name']);
			
			$this->csv_separator = q($_REQUEST['csv_seperator']);
			
			$MCR = new MitgliederController();
			$MCR->init();
			
			if(isSizedString($this->path))
			{
				$row = 0;
	
				$handle = fopen($this->path, "r");
			
				while (($data = fgetcsv($handle, 1024, $this->csv_separator)) !== FALSE) 
				{
	    			$num = count($data);
	    			$row++;
	    			$content[] = $data;
				}
			
					fclose($handle);
			}
			
			$this->arContent = $content;
			
			$i = '0';
			$tablehead = '0';
			
			foreach($this->arContent as $key=>$value)
			{
				if($_REQUEST['first-line'] == '1' && $i == '0')
				{
					
					$i++;
					$tablehead++;
					
				}else{
					
					$arData = array(
						'unternehmen'		=> q($value[$_REQUEST['unternehmen']]),
						'ansprechpartner'	=> q($value[$_REQUEST['ansprechpartner']]),
						'strasse'			=> q($value[$_REQUEST['strasse']]),
						'plz'				=> q($value[$_REQUEST['plz']]),
						'ort'				=> q($value[$_REQUEST['ort']]),
						'telefon'			=> q($value[$_REQUEST['telefon']]),
						'fax'				=> q($value[$_REQUEST['fax']]),
						'email'				=> q($value[$_REQUEST['email']]),
						'www'				=> q($value[$_REQUEST['www']]),
						'gps-lat'			=> q($value[$_REQUEST['gps-lat']]),
						'gps-lon'			=> q($value[$_REQUEST['gps-lon']]),
						'create'			=> time()
						);
						
						if($value[$_REQUEST['gps-lat']] == '' || $value[$_REQUEST['gps-lon']] == '')
						{
							$address = $value[$_REQUEST['strasse']].', ';
							$address .= $value[$_REQUEST['plz']].' ';
							$address .= $value[$_REQUEST['ort']].' ';
							
							$position = $MCR->get_gps_dataAction($address);
							
							usleep(2000);
							
							if($position[0] == 200)
							{

								$arData['gps-lat'] = $position[2];
								$arData['gps-lon'] = $position[3];
							
							}else{
								
								$arData['gps-lat'] = 'Adresse nicht aufl�sbar';
								$arData['gps-lat'] = 'Adresse nicht aufl�sbar';
							
							}
							
						}
					
					/* Pflichtfelder */	
					if(isSizedArray($arData))
					{
						if($_REQUEST['unternehmen'] != '-1' && $_REQUEST['ansprechpartner'] != '-1'  && $_REQUEST['telefon'] != '-1')
						{
						
							$this->lastQuery = $this->db->ImportQuery( $this->tbl_mtgl , $arData );
							
							$i++;
						}
					}
				
				}
				
			}
			
			$inserts = $i-$tablehead;
			
			if($inserts < 1)
			{

				$this->setNegMessage('Es konnten keine Daten importiert werden, bitte prüfen Sie Ihre CSV Daten');
			
			}else{
				
				$this->setPosMessage($inserts.' Datensätze wurden erfolgreich übernommen.');
			
			}
			
			$MCR->indexAction();
			
		}

		

		/*
		 *	zeigt das upload Formular an
		*/
		public function uploadAction()
		{
			$this->render('Import' , 'upload');
		}
	}

?>