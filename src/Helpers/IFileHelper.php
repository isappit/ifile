<?php
namespace Isappit\Ifile\Helpers;

use Isappit\Ifile\Exception\IFileException;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Classe di utility
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileHelper {
	
	/**
	 * Setta il il tipo del field e il tipo di encoding dalla configurazione.
	 * 
	 * Type:
	 * 
	 * Keyword 
	 * UnIndexed 
	 * Binary 
	 * Text 
	 * UnStored 
	 * 
	 *  Field Type 	Stored 	Indexed 	Tokenized 	Binary
	 *	Keyword 	Yes 	Yes 		No 			No
	 *	UnIndexed 	Yes 	No 			No 			No
	 *	Binary 		Yes 	No 			No 			Yes
	 *	Text 		Yes 	Yes 		Yes 		No
	 *	UnStored 	No 		Yes 		Yes 		No
	 *  
	 * @param Zend_Search_Lucene_Document $doc
	 * @param string $fieldsName
	 * @param mixed $value
	 * @return void
	 */
	static function setFieldType(Zend_Search_Lucene_Document $doc, $fieldName, $fieldValue) {
		
		// recupero dati di configurazione 	
		$iFileConfig = IFileConfig::getInstance();
		// recupero dei parametrio di configurazione del Field		
		$field = $iFileConfig->getDocumentField($fieldName);
		
		if (!$field) {
			throw new IFileException('Field not exist');
		}
		
		// Verifico che l'encoding sia settato oppure non vuoto
		$encoding = (!isset($field['encoding']) || empty($field['encoding'])) ? $iFileConfig->getConfig('encoding') : $field['encoding'];
		
		switch ($field['type']) {
			case 'Keyword':
				$doc->addField(Zend_Search_Lucene_Field::Keyword($fieldName, $fieldValue, $encoding));
				break;
			case 'UnIndexed':
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed($fieldName, $fieldValue, $encoding));
				break;
			case 'Binary':
				$doc->addField(Zend_Search_Lucene_Field::Binary($fieldName, $fieldValue, $encoding));
				break;
			case 'Text':
				$doc->addField(Zend_Search_Lucene_Field::Text($fieldName, $fieldValue, $encoding));
				break;
			case 'UnStored':
				$doc->addField(Zend_Search_Lucene_Field::UnStored($fieldName, $fieldValue, $encoding));
				break;
			default:
				throw new IFileException('Field Type not defined or not correct');
				break;
		}
	}
	
	/**
	 * Ritorna il nome di un file, dato il percorso
	 *
	 * @param string $file 
	 * @return string
	 */
	static function getName($file) {
		$slash = strrpos($file, DIRECTORY_SEPARATOR) + 1;
		return substr($file, $slash);
	}
	
	/**
	 * Ritorna l'estensione di un file
	 * 
	 * @param string $file
	 * @return string
	 */
	static function getExt($file) {
		$dot = strrpos($file, '.') + 1;
		return substr($file, $dot);
	}
	
	/**
	 * Elimina l'ultimo "directory separetor" dal percorso di un file
	 * 
	 * @param string $path
	 * @return string
	 */
	static function deleteLastSlash($path) {
		$path = realpath($path);		
		
		if($path{strlen($path)-1} == DIRECTORY_SEPARATOR) {
			return substr($path,0,-1);			
		}
		
		return $path;
	}
	
	/**
	 * Ritorna il checksum in MD5 del contenuto di un file
	 * 
	 * @param string $file
	 * @return string
	 */
	static function checksumFromFile($file) {
		return md5_file($file);
	} 
	
	/**
	 * Ritorna una stringa formattata senza l'ultima parola tagliata
	 * 
	 * @param string $text
	 * @return string
	 */
	static function introText($text) {
		$introText = "";

		$arrayText =  preg_split("/[\s]+/", $text);
		$countWord = count($arrayText)-1;
		
		unset($arrayText[$countWord]);
		
		$introText = implode(" ", $arrayText);	
		
		return $introText;
	}
	
	/**
	 * Ritorna una stringa nel formato INI dato un oggetto
	 * 
	 * Non permette di avere valori innestati superiori a due livelli di un oggetto
	 *
	 * @param object $object
	 * @return string 
	 */
	static function objectToString(&$object)
	{

		// Initialize variables
		$retval = '';
		$prepend = '';

		// First handle groups (or first level key/value pairs)
		foreach (get_object_vars( $object ) as $key => $level1)
		{
			if (is_object($level1))
			{
				// This field is an object, so we treat it as a section
				$retval .= "[".$key."]\n";
				foreach (get_object_vars($level1) as $key => $level2)
				{
					if (!is_object($level2) && !is_array($level2))
					{
						// Join lines
						$level2		= str_replace('|', '\|', $level2);
						$level2		= str_replace(array("\r\n", "\n"), '\\n', $level2);
						$retval		.= $key."=".$level2."\n";
					}
				}
				$retval .= "\n";
			}
			elseif (is_array($level1))
			{
				foreach ($level1 as $k1 => $v1)
				{
					// Escape any pipe characters before storing
					$level1[$k1]	= str_replace('|', '\|', $v1);
					$level1[$k1]	= str_replace(array("\r\n", "\n"), '\\n', $v1);
				}

				// Implode the array to store
				$prepend	.= $key."=".implode('|', $level1)."\n";
			}
			else
			{
				// Join lines
				$level1		= str_replace('|', '\|', $level1);
				$level1		= str_replace(array("\r\n", "\n"), '\\n', $level1);
				$prepend	.= $key."=".$level1."\n";
			}
		}

		return $prepend."\n".$retval;
	}

	
	/**
	 * Parserizza una stringa dal formato INI in un oggetto
	 * 
	 * Il parser e' basato su phpDocumentor phpDocumentor_parse_ini_file function
	 *
	 * @param mixed $data una stringa INI o un array di linee
	 * @param boolean $process_sections aggiunge un indice associativo per ogni sezione
	 * @return object
	 */
	static function stringToObject($data, $process_sections = false )
	{
		static $inistocache;

		if (!isset( $inistocache )) {
			$inistocache = array();
		}

		if (is_string($data))
		{
			$lines = explode("\n", $data);
			$hash = md5($data);
		}
		else
		{
			if (is_array($data)) {
				$lines = $data;
			} else {
				$lines = array ();
			}
			$hash = md5(implode("\n",$lines));
		}

		if(array_key_exists($hash, $inistocache)) {
			return $inistocache[$hash];
		}

		$obj = new stdClass();

		$sec_name = '';
		$unparsed = 0;
		if (!$lines) {
			return $obj;
		}

		foreach ($lines as $line)
		{
			// ignore comments
			if ($line && $line{0} == ';') {
				continue;
			}

			$line = trim($line);

			if ($line == '') {
				continue;
			}

			$lineLen = strlen($line);
			if ($line && $line{0} == '[' && $line{$lineLen-1} == ']')
			{
				$sec_name = substr($line, 1, $lineLen - 2);
				if ($process_sections) {
					$obj-> $sec_name = new stdClass();
				}
			}
			else
			{
				if ($pos = strpos($line, '='))
				{
					$property = trim(substr($line, 0, $pos));

					// property is assumed to be ascii
					if ($property && $property{0} == '"')
					{
						$propLen = strlen( $property );
						if ($property{$propLen-1} == '"') {
							$property = stripcslashes(substr($property, 1, $propLen - 2));
						}
					}
					// AJE: 2006-11-06 Fixes problem where you want leading spaces
					// for some parameters, eg, class suffix
					// $value = trim(substr($line, $pos +1));
					$value = substr($line, $pos +1);

					if (strpos($value, '|') !== false && preg_match('#(?<!\\\)\|#', $value))
					{
						$newlines = explode('\n', $value);
						$values = array();
						foreach($newlines as $newlinekey=>$newline) {

							// Explode the value if it is serialized as an arry of value1|value2|value3
							$parts	= preg_split('/(?<!\\\)\|/', $newline);
							$array	= (strcmp($parts[0], $newline) === 0) ? false : true;
							$parts	= str_replace('\|', '|', $parts);

							foreach ($parts as $key => $value)
							{
								if ($value == 'false') {
									$value = false;
								}
								else if ($value == 'true') {
									$value = true;
								}
								else if ($value && $value{0} == '"')
								{
									$valueLen = strlen( $value );
									if ($value{$valueLen-1} == '"') {
										$value = stripcslashes(substr($value, 1, $valueLen - 2));
									}
								}
								if(!isset($values[$newlinekey])) $values[$newlinekey] = array();
								$values[$newlinekey][] = str_replace('\n', "\n", $value);
							}

							if (!$array) {
								$values[$newlinekey] = $values[$newlinekey][0];
							}
						}

						if ($process_sections)
						{
							if ($sec_name != '') {
								$obj->$sec_name->$property = $values[$newlinekey];
							} else {
								$obj->$property = $values[$newlinekey];
							}
						}
						else
						{
							$obj->$property = $values[$newlinekey];
						}
					}
					else
					{
						//unescape the \|
						$value = str_replace('\|', '|', $value);

						if ($value == 'false') {
							$value = false;
						}
						else if ($value == 'true') {
							$value = true;
						}
						else if ($value && $value{0} == '"')
						{
							$valueLen = strlen( $value );
							if ($value{$valueLen-1} == '"') {
								$value = stripcslashes(substr($value, 1, $valueLen - 2));
							}
						}

						if ($process_sections)
						{
							$value = str_replace('\n', "\n", $value);
							if ($sec_name != '') {
								$obj->$sec_name->$property = $value;
							} else {
								$obj->$property = $value;
							}
						}
						else
						{
							$obj->$property = str_replace('\n', "\n", $value);
						}
					}
				}
				else
				{
					if ($line && $line{0} == ';') {
						continue;
					}
					if ($process_sections)
					{
						$property = '__invalid'.$unparsed ++.'__';
						if ($process_sections)
						{
							if ($sec_name != '') {
								$obj->$sec_name->$property = trim($line);
							} else {
								$obj->$property = trim($line);
							}
						}
						else
						{
							$obj->$property = trim($line);
						}
					}
				}
			}
		}

		$inistocache[$hash] = clone($obj);
		return $obj;
	}
}
?>