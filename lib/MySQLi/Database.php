<?php
namespace m1\usv;

	class MySQLi_Database
	{
		
		/*
		 * Fehlerbehandlung
		 */
		function handleError()
		{
			
			global $wpdb;
			
			die("Query: ".$wpdb->last_query."\nFehler: ".$wpdb->last_error."\nBacktrace:\n".print_r(debug_backtrace()));
			
		} 
		

		/*
		 * Gibt eine einzelne Zelle aus der Datenbank zurück
		 */
		function fetchOne($strQuery)
		{
			
			global $wpdb;

			if ($wpdb->query($strQuery) === false)
			{
				
				$this->handleError();
				
			}else{
			
				$result = $wpdb->get_var($strQuery);
 
				return $result;
				
			} 
			
		}
		
		
		/*
		 * Gibt eine ganze Zeile als Ergebnis aus der Datenbank zurück
		 */
		function fetchRow($strQuery)
		{
			
			global $wpdb;
			 
			if ($wpdb->query($strQuery) === false)
			{
			
				$this->handleError();
			
			}else{
					
				$result = $wpdb->get_row($strQuery, ARRAY_A);
			 
				return $result;
			
			} 
			
		}
		
		
		/*
		 * Gibt mehrere Zeilen aus einer Tabelle als Array von Arrays zurück
		 */
		function fetchAssoc($strQuery, $key = false)
		{
			
			global $wpdb;
			
			if ($wpdb->query($strQuery) === false)
			{
					
				$this->handleError();
					
			}else{
			
				$arReturn1 = $wpdb->get_results($strQuery, ARRAY_A);
				
				if ($key != false)
				{
					
					$arReturn = array();
					
					foreach ($arReturn1 as $k => $v)
					{
						
						$arReturn[$v[$key]] = $v;
						
					}
					
					return $arReturn;
					
				}else{
					
					return $arReturn1;
					
				} 
				
			}
			
		}
		
		
		/*
		 * Importiert die Daten aus $data als neue Zeile in die Tabelle $table
		 * $data muss dabei aus einem Schlüssel/Wert Array bestehen
		 * Der Rückgabewert ist die ID des eingefügten Datensatzes
		 */
		function ImportQuery($table, $data, $checkCols = false)
		{
			
			global $wpdb;
			
			/*
			 * Nur Spalten importiert, wenn sie auch in der Zieltabelle existieren
			 */
			if ($checkCols === true)
			{
				
				$arFields = $this->fetchAssoc("SHOW COLUMNS FROM `".q($table)."` ");
				
				$arCols = array();				
				foreach ($arFields as $f) { $arCols[] = $f['Field']; }				
				foreach ($data as $k => $v) { if (!in_array($k, $arCols)) { unset($data[$k]); } }
				
			}
			
			if (!isSizedArray($data)) return false;
			
			// Query zusammenbauen
			$strQuery = "INSERT INTO `".q($table)."` SET ";
			
			foreach ($data as $k => $v)
			{
				
				if ($v != "NOW()" && $v != "NULL" && !is_array($v))
					$v = "'".$v."'";
				else if (is_array($v))
					$v = $v[0];
					
				$strQuery .= "`".$k."` = ".$v.", ";
				
			}
			
			$strQuery = substr($strQuery, 0, -2);
			
			$res = $wpdb->query($strQuery);

			if ($res === false)
			{
			
				$this->handleError();
			
			}else{
						
				return $wpdb->insert_id;
				
			}
			
		}
		
		function UpdateQuery($table, $data, $where)
		{
			
			global $wpdb;
			
			$strQuery = "UPDATE `".$table."` SET ";
			
			foreach ($data as $k => $v)
			{
				
				if ($v != "NOW()" && $v != "NULL" && !is_array($v))
					$v = "'".q($v)."'";
				else if (is_array($v))
					$v = $v[0];
					
				$strQuery .= "`".$k."` = ".$v.", ";
			}
			
			$wpdb->query(substr($strQuery, 0, -2)." WHERE ".$where);
			
		} 

		
		/*
		 * Liefert eine Spalte eines Querys als Array zurück
		 * Parameter strKeyField sollte eindeutig sein
		 */
		function fetchAssocField($strQuery, $strKeyField = false, $strValueField = false)
		{
		
			global $wpdb;
			
			if ($wpdb->query($strQuery) === false)
			{
					
				$this->handleError();
					
			}else{
			
				$db_rows = $wpdb->get_results($strQuery, ARRAY_A);
				$arReturn = array();			
				
				foreach ($db_rows as $row)
				{
					 
					if ($strKeyField != false && $strValueField != false)
						$arReturn[$row[$strKeyField]] = $row[$strValueField];
					else
						$arReturn[] = reset($row);
					
				} 
				
				return $arReturn;
				
			}
			
		} 
		
		/*
		 * Führt einen Query aus - zum Beispiel für Delete Querys
		 */
		function Query($strQuery)
		{
			
			global $wpdb;
			
			$res = $wpdb->query($strQuery);
			
			if ($res === false)
			{
				
				$this->handleError();
				
			}
			 			
		}
		
	} 

?>