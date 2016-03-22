<?php
namespace Isappit\Ifile\Servercheck;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage servercheck
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Oggetto contenitore dei dati del check server
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage servercheck
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class ReportCheck {
	
	/**
	 * Valore di controllo
	 * 
	 * @var bool
	 */
	private $check;
	/**
	 * Etichetta
	 * 
	 * @var string
	 */
	private $label;
	/**
	 * Messaggio
	 * 
	 * @var string
	 */
	private $message;
	/**
	 * Stringa dei requisiti minimi
	 * 
	 * @var string
	 */
	private $require;
	/**
	 * Stringa di informazioni aggiuntive
	 * 
	 * @var string
	 */
	private $info;
	/**
	 * Stringa di informazioni sull'utilita' della libreria
	 * 
	 * @var string
	 */
	private $infoUse;
	/**
	 * Indirizzo web 
	 * 
	 * @var string
	 */
	private $site;
	
	
	/**
	 * Costruttore
	 *  
	 * @param bool $check [optional]
	 * @param string $label [optional]
	 * @param string $message [optional]
	 * @param string $require [optional]
	 * @param string $info [optional]
	 * @param string $site [optional]
	 * @return void 
	 */	
	public function __construct($check = false, $label = 'empty', $message = 'empty', $require = 'empty', $info = 'empty', $site = 'empty', $infoUse = 'Not defined') {
		$this->setCheck($check);
		$this->setLabel($label);
		$this->setMessage($message);
		$this->setRequire($require);
		$this->setInfo($info);
		$this->setSite($site);
		$this->setInfoUse($infoUse);
	}
	/**
	 * Setta il valore di controllo
	 * 
	 * @param bool $check
	 * @return void
	 */
	public function setCheck($check) {$this->check = $check;}
	/**
	 * Ritorna il valore di controllo
	 * 
	 * @return boolo
	 */	
	public function getCheck() {return $this->check;}
	/**
	 * Setta il messaggio del controllo
	 *  
	 * @param string $message
	 * @return void 
	 */
	public function setMessage($message) {$this->message = $message;}
	/**
	 * Ritorna il messaggio del controllo
	 * 
	 * @return string
	 */
	public function getMessage() {return $this->message;}
	/**
	 * Setta l'etichetta del controllo
	 * 
	 * @param string $label
	 * @return void
	 */
	public function setLabel($label) {$this->label = $label;}
	/** 
	 * Ritorna l'etichetta del controllo
	 * 
	 * @return string 
	 */
	public function getLabel() {return $this->label;}
	/**
	 * Setta il testo dei requisiti minimi
	 * 
	 * @param string $require
	 * @return void
	 */
	public function setRequire($require) {$this->require = $require;}
	/**
	 * Ritorna la stringa dei requisiti minimi
	 * 
	 * @return string 
	 */
	public function getRequire() {return $this->require;}
	/**
	 * Setta la stringa delle informazioni
	 * 
	 * @param string $info
	 * @return void
	 */
	public function setInfo($info) {$this->info = $info;}
	/**
	 * Setta la stringa delle informazioni sull'uso della componente
	 * 
	 * @param string $infoUse
	 * @return void
	 */
	public function setInfoUse($infoUse) {$this->infoUse = $infoUse;}
	/**
	 * Ritorna la stringa delle informazioni sull'uso
	 * 
	 * @return string 
	 */
	public function getInfoUse() {return $this->infoUse;}
	/**
	 * Ritorna la stringa delle informazioni
	 * 
	 * @return string 
	 */
	public function getInfo() {return $this->info;}
	/**
	 * Setta l'indirizzo web di riferimento
	 *   
	 * @param string $site
	 * @return void
	 */	
	public function setSite($site) {$this->site = $site;}
	/**
	 * Ritorna l'indirizzo web di riferimento
	 * 
	 * @return string 
	 */
	public function getSite() {return $this->site;}
}
?>