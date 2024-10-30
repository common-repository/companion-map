<?php
namespace m1\usv;
	/*
	 * Formatierte Ausgabe der Debugparameter
	 */
	function debug($value)
	{

		if (is_array($value))
		{

			echo '<pre class="posMessage">';
			print_r($value);
			echo '</pre>';
		
		}else{
			
			echo '<pre class="negMessage">'.$value.'</pre>';
		
		}
 
	}
	
	
	/*
	 * Substitution der Mysql-Escape Funktion
	 */
	function q($value)
	{
		
		return addslashes($value);	
	
	}
	
	
	/*
	 * Prüfung ob befülltes Array vorhanden ist
	 */
	function isSizedArray(&$array)
	{
		
		if(isset($array) && is_array($array) && sizeof($array) > 0)
		{

			return true;
		
		}
		
		return false;
	}
	
	
	/*
	 * Prüfung ob String mit Länge vorliegt
	 */
	function isSizedString($string)
	{
		if(is_string($string) && $string != '' && strlen($string) > 0)
		{
			
			return true;
		
		}
		
		return false;
	}
	
	/*
	 * Wandelt Sonderzeichen in HTML-Code um => Formulare 
	 */
	function hspc($string) 
	{
		
		if (isset($string)) return htmlspecialchars($string); 
		else return '';
		
	}	
?>