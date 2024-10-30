<?php
namespace m1\usv;

class ConfigController extends SystemController
{
	
	/*
	* zeigt eine Übersicht aller Datensätze
	*/	
	public function indexAction()
	{	
		$this->arConf = $this->db->fetchAssoc(
											"SELECT
												*
											FROM
												`".$this->tbl_conf."`
											");
		
		foreach ($this->arConf as $key => $value)
		{
			foreach ($value as $k =>$v)
			{
				$this->arData[$value['name']] = $v;
			}
		}
		
		$this->render('Config', 'index');		
	}
	
	/*
	* Speichern eines bearbeiteten oder neu angelegten Datensätzen
	*/	
	public function saveAction()
	{
		//if (!isset($_REQUEST['strasse']) or !isset($_REQUEST['plz']) or !isset($_REQUEST['ort']) or !isset($_REQUEST['googlekey']) or !isset($_REQUEST['veranstaltung']))
		{
		
			if($_REQUEST['config']['strasse'] != '' && $_REQUEST['config']['plz'] != '' && $_REQUEST['config']['ort'] != '' && $_REQUEST['config']['googlekey'] !=''  && $_REQUEST['config']['darstellung'] !='' )
			{
					
				$this->arData = $_REQUEST['config'];
				
				$this->current_id = '0';
				
				foreach ($this->arData as $key => $value)
				{
					$arInsert = array(
						'name' => q($key),
						'wert' => q($value)
					);
					
					$this->varConf = $this->db->fetchRow("SELECT * FROM `".$this->tbl_conf."` WHERE `name` = '".$arInsert['name']."'");
					
					if(isSizedArray($this->varConf))
					{
						
						$this->db->UpdateQuery($this->tbl_conf, $arInsert, "`name` = '".q($key)."'");
					
					}else{
						
						$this->current_id = $this->db->ImportQuery( $this->tbl_conf , $arInsert );
					
					}
				}	
	
				$this->setPosMessage('Einstellungen erfolgreich gespeichert.');
				
			}else{
					
				$this->setNegMessage('Einstellungen konnten nicht übernommen werden, bitte prüfen Sie Ihre Angaben.');
						
			}
			
			$this->indexAction();

		}
	}
	
	public function initConfig()
	{
		$sqlStr = "SELECT * FROM `".$this->tbl_conf."`";
			
		$this->arConf = $this->db->fetchAssoc($sqlStr);
		
		foreach ($this->arConf as $key => $value)
		{
			foreach ($value as $k =>$v)
			{
				$this->configArray[$value['name']] = $v;
			}
		}
		
		return $this->configArray;
		
	}
}	