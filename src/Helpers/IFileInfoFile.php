<?php
namespace Isappit\Ifile\Helpers;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage helpers
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Oggetto contenitore dei dati del file da indicizzare 
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileInfoFile {
	
	/**
	 * Path del file da indicizzare
	 * 
	 * @var string
	 */
	private $file = null;
	/**
	 * Checksum del file
	 * 
	 * @var string
	 */
	private $keyFile = null;
	/**
	 * Nome del file senza path
	 * 
	 * @var string
	 */
	private $nameFile = null;
	/**
	 * Estensione del file
	 * 
	 * @var string
	 */
	private $extFile = null;
	/**
	 * Path relativo del file a partire dalla root-appication
	 * 
	 * @var string
	 */
	private $relativePathFile = null;
	
		
	public function __construct() {}
	
	/**
	 * Setta il path assoluto del file
	 *  
	 * @param string $file
	 * @return void
	 */
	public function setFile($file) {
		$this->file = $file;
	}
	
	/**
	 * Ritorna il path assoluto del file
	 *  
	 * @return string 
	 */
	public function getfile() {
		return $this->file;
	}
	
	/**
	 * Setta la chiave univoca del file
	 * 
	 * @param string $key
	 * @return void
	 */
	public function setKeyFile($key) {
		$this->keyFile = $key;
	}
	
	/**
	 * Ritorna la chiave univoca del file
	 * 
	 * @return string 
	 */
	public function getKeyFile() {
		return $this->keyFile;
	}
	
	/**
	 * Setta il nome del file 
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setNameFile($name) {
		$this->nameFile = $name;
	}
	
	/**
	 * Ritorna il nome del file
	 * 
	 * @return string 
	 */
	public function getNameFile() {
		return $this->nameFile;
	}
	
	/**
	 * Setta l'estensione del file 
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setExtFile($ext) {
		$this->extFile = $ext;
	}
	
	/**
	 * Ritorna l'estensione del file
	 * 
	 * @return string 
	 */
	public function getExtFile() {
		return $this->extFile;
	}
	
	/**
	 * Setta l'estensione del file 
	 * 
	 * @param string $name
	 * @return void
	 */
	public function setRelativePathFile($path) {
		$this->relativePathFile = $path;
	}
	
	/**
	 * Ritorna l'estensione del file
	 * 
	 * @return string 
	 */
	public function getRelativePathFile() {
		return $this->relativePathFile;
	}
}
?>