<?php

	class DbModel
	{

		function fetchOne($strQuery)
		{
			
			global $wpdb;
			
			$result = $wpdb->get_var($wpdb->prepare($strQuery));
			
			return $result; 
			
		} 
		
		
		function fetchRow($strQuery)
		{
			
			$result = mysql_query($strQuery);	
			$row = mysql_fetch_assoc($result);
			
			return $row;
			
		}
		
		function fetchAssoc($strQuery, $key = false)
		{
			
			global $wpdb;
			
			if ($strQuery == "") return array();
			
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
		
		function ImportQuery($table, $data)
		{
			
			$strQuery = "INSERT INTO `".$table."` SET ";
			
			foreach ($data as $k => $v)
			{
				
				if ($v != "NOW()" && $v != "NULL" && !is_array($v))
					$v = "'".q($v)."'";
				else if (is_array($v))
					$v = $v[0];
				
				$strQuery .= "`".$k."` = ".$v.", ";
			}
			  	
			mysql_query(substr($strQuery, 0, -2)) or debug($strQuery."\n\n".mysql_error()."\n\n".print_r(debug_backtrace(), 1));	
			
			return mysql_insert_id();
			
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
		 */
		function fetchAssocField($strQuery, $strKeyField = false, $strValueField = false)
		{
		
			$result = mysql_query($strQuery) or die(debug($strQuery."\n\n".mysql_error()."\n\n".print_r(debug_backtrace(), 1)));	
			
			$arReturn = array();
			while ($row = mysql_fetch_array($result))
			{
				if ($strKeyField != false && $strValueField != false)
					$arReturn[$row[$strKeyField]] = $row[$strValueField];
				else
					$arReturn[] = $row[0];
			}
			
			return $arReturn;
			
		}
		
		function Query($strQuery)
		{
			
			$result = mysql_query($strQuery) or debug($strQuery."\n\n".mysql_error()."\n\n".print_r(debug_backtrace(), 1));	
			
			return $result;
			
		}
		
	}

?>