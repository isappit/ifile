<?php
namespace Isappit\Ifile\Adapter\Beans;

use Isappit\Ifile\Config\IFileConfig;
use Isappit\Ifile\Helpers\IFileHelper;
use ZendSearch\Lucene\Document as Zend_Search_Lucene_Document;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/beans
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Oggetto contenitore delle proprieta' di un documento
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/beans
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class LuceneDataIndexBean {
	
	/**
	 * Titolo del documento
	 * 
	 * @var string
	 */
	private $title;
	/**
	 * Oggetto del documento
	 * 
	 * @var string
	 */
	private $subject;
	/**
	 * Descrizione del documento
	 * 
	 * @var string
	 */
	private $description;
	/**
	 * Creatore del documento
	 * 
	 * @var string
	 */
	private $creator;
	/**
	 * Parole chiave del documento, separate da virgole (,)
	 * 
	 * @var string
	 */
	private $keywords;
	/**
	 * Data di creazione del documento
	 * 
	 * @var string
	 */
	private $created;
	/**
	 * Data di ultima modifica del documento
	 * 
	 * @var string
	 */
	private $modified;
	/**
	 * Numero di pagine
	 * 
	 * @var string
	 */
	private $pages;
	/**
	 * Contenuto del documetno
	 * 
	 * @var string
	 */
	private $body;
	
	public function __construct() {}
	
	/** 
	 * Setta il contenuto (corpo) 
	 * 
	 * @param string $body
	 * @return void
	 */
	public function setBody($body) {$this->body = trim($body);}
	/**  
	 * Restituisce il contenuto di un documento
	 * @return string
	 */
	public function getBody() {return $this->body;}
	/** 
	 * Setta il numero di pagine
	 * 
	 * @param string $modified
	 * @return void
	 */
	public function setPages($pages) {$this->pages = trim($pages);}
	/** 
	 * Ritorna il numero di pagine
	 * 
	 * @return string
	 */
	public function getPages() {return $this->pages;}
	/** 
	 * Setta la data di modifica 
	 * 
	 * @param string $modified
	 * @return void
	 */
	public function setModified($modified) {$this->modified = trim($modified);}
	/** 
	 * Ritorna la data di modifica 
	 * 
	 * @return string
	 */
	public function getModified() {return $this->modified;}
	/** 
	 * Setta la descrizione 
	 * 
	 * @param string $description
	 * @return void 
	 */
	public function setDescription($description) {$this->description = trim($description);}
	/** 
	 * Ritorna la descrizione 
	 * 
	 * @return string 
	 */
	public function getDescription() {return $this->description;}
	/** 
	 * Setta la data di creazione 
	 * 
	 * @param string $created
	 * @return void
	 */
	public function setCreated($created) {$this->created = trim($created);}
	/** 
	 * Ritorna la data di creazione 
	 * 
	 * @return string 
	 */
	public function getCreated() {return $this->created;}
	/** 
	 * Setta le parole chiave 
	 * 
	 * @param string $keywords
	 * @return void
	 */
	public function setKeywords($keywords) {$this->keywords = trim($keywords);}
	/** 
	 * Ritorna le parole chiave 
	 * 
	 * @return string 
	 */ 
	public function getKeywords() {return $this->keywords;}
	/** 
	 * Setta l'autore 
	 * 
	 * @param string $creator
	 * @return void
	 */
	public function setCreator($creator) {$this->creator = trim($creator);}
	/** 
	 * Ritorna l'autore 
	 * 
	 * @return string 
	 */
	public function getCreator() {return $this->creator;}
	/** 
	 * Setta l'oggetto 
	 * 
	 * @param string $subject
	 * @return void
	 */
	public function setSubject($subject) {$this->subject = trim($subject);}
	/** 
	 * Ritorna l'oggetto 
	 * 
	 * @return string 
	 */ 
	public function getSubject() {return $this->subject;}
	/** 
	 * Setta il titolo 
	 * 
	 * @param string $title
	 * @return vloid 
	 */
	public function setTitle($title) {$this->title = trim($title);}
	/** 
	 * Ritorna il titolo 
	 * 
	 * @return string 
	 */
	public function getTitle() {return $this->title;}
	
	/**
	 * Verifia se la proprieta' non e' vuota
	 * 
	 * @param string $property
	 * @return bool
	 */
	public function issetNotEmpty($property) {
		
		if (isset($this->$property) && trim($this->$property) !== '') return true;
		
		return false;
	}
	
	/**
	 * Ritorna un Zend_Search_Lucene_Document valorizzato con i dati del bean
	 * 
	 * La struttura del Document comprende esclusivamente i seguenti campi (field) 
	 * cosi' descritti => nomeField:Tipo (descrizione)
	 *  
	 * - body:UnStored (contenuto del documento)
	 * - title:Text (titolo del documento)
	 * - subject:Text (oggetto del documento)
	 * - creator:UnIndexed (creatore del documento)
	 * - keywords:Keyword (parole chiave separato da spazi)
	 * - created:UnIndexed (data di creazione del documento)
	 * - modified:UnIndexed (data di modifica del documento)
	 * 
	 * @return Zend_Search_Lucene_Document 
	 */
	public function getLuceneDocument() {
		
		// Recupera i dati di configurazione 		
		$IfileConfig = IFileConfig::getInstance();
		
		// creazuione dell'oggetto Zend_Search_Lucene_Document 
		$doc = new Zend_Search_Lucene_Document();
		
		// Inserisce i dati del file all'interno dell'indice come Field
		// Contenuto
		if ($this->issetNotEmpty('body')) {
			IFileHelper::setFieldType($doc, 'body', $this->getBody());
		} 
		// Titolo
		if ($this->issetNotEmpty('title')) {
			IFileHelper::setFieldType($doc, 'title', $this->getTitle());
		}
		// Oggetto
		if ($this->issetNotEmpty('subject')) {
			IFileHelper::setFieldType($doc, 'subject', $this->getSubject());
		}
		// Descrizione
		if ($this->issetNotEmpty('description')) {
			IFileHelper::setFieldType($doc, 'description', $this->getDescription());			
		}	
		// Autore (Creatore)
		if ($this->issetNotEmpty('creator')) {			
			IFileHelper::setFieldType($doc, 'creator', $this->getCreator());
		}		
		// Parole chiavi
		if ($this->issetNotEmpty('keywords')) {
			IFileHelper::setFieldType($doc, 'keywords', $this->getKeywords());
		}
		// Data di creazione
		if ($this->issetNotEmpty('created')) {
			IFileHelper::setFieldType($doc, 'created', $this->getCreated());
		}
		// Data di modifica
		if ($this->issetNotEmpty('modified')) {
			IFileHelper::setFieldType($doc, 'modified', $this->getModified());
		}
		// Nuemro di pagine
		if ($this->issetNotEmpty('pages')) {
			IFileHelper::setFieldType($doc, 'pages', $this->getPages());
		}
		
		return $doc;
	} 
}
?>