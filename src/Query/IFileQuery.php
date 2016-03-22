<?php
namespace Isappit\Ifile\Query;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage query
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Oggetto di definizione dei termini da ricercare all'interno del documento 
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage query
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileQuery {
	
	/**
	 * Metodo di match: Obbligatorio
	 */
	const MATCH_REQUIRED 	= true;
	/**
	 * Metodo di match: Diverso
	 */
	const MATCH_PROHIBITEN 	= false;
	/**
	 * Metodo di match: Facoltativo
	 */
	const MATCH_OPTIONAL  	= null;	
	/**
	 * Metodo di ricerca: Frase
	 */
	const QUERY_PHRASE = 'phrase';
	/**
	 * Metodo di ricerca: Multi Termine
	 */
	const QUERY_MULTITERM = 'multiterm';
	/**
	 * Metodo di ricerca: Range
	 */
	const QUERY_RANGE = 'range';
	/**
	 * Metodo di ricerca: Wildcard
	 */
	const QUERY_WILDCARD = 'wildcard';
	/**
	 * Metodo di ricerca: Fuzzy
	 */
	const QUERY_FUZZY = 'fuzzy';
		
	/**
	 * Termine
	 * 
	 * @var string
	 */
	private $term;
	/**
	 * Campo
	 * 
	 * @var string
	 */
	private $field = null;
	/**
	 * Matching mode
	 * 
	 * @var mixed
	 */
	private $match = null;
	/**
	 * Posizione del termine
	 * 
	 * @var integer
	 */
	private $position = null;
	/**
	 * Encoding del termine
	 *  
	 * @var string
	 */
	private $encoding = '';
	/**
	 * Type
	 *  
	 * @var string
	 */
	private $searchType = false;
		
	public function __construct() {}
	
	/**
	 * Setta il termione 
	 * 
	 * @param string $term
	 * @return void
	 */
	public function setTerm($term) {$this->term = $term;}
	/**
	 * Ritorna il termine
	 * 
	 * @return string 
	 */
	public function getTerm() {return $this->term;}
	/**
	 * Setta il campo
	 * 
	 * @param string $field
	 * @return void
	 */
	public function setField($field) {$this->field = $field;}
	/**
	 * Ritorna il campo
	 * 
	 * @return string
	 */
	public function getField() {return $this->field;}
	/**
	 * Setta il tipo di confronto
	 * 
	 * @param mixed $match
	 * @return void
	 */	
	public function setMatch($match) {$this->match = $match;}
	/**
	 * Ritorna il tipo di confronto
	 * 
	 * @return mixed
	 */
	public function getMatch() {return $this->match;}
	/**
	 * Setta la posizione del termine all'interno del documento
	 * 
	 * @param integer $position
	 * @return void
	 */
	public function setPosition($position) {$this->position = $position;}
	/**
	 * Ritorna la posizione del termine
	 * 
	 * @return integer 
	 */
	public function getPosition() {return $this->position;}
	/**
	 * Setta l'encoding del termine
	 * 
	 * @param string $encoding
	 * @return void
	 */
	public function setEncoding($encoding) {$this->encoding = $encoding;}
	/**
	 * Ritorna l'encoding del termine
	 * Default: UTF-8
	 * 
	 * @return string 
	 */
	public function getEncoding() {return $this->encoding;}
	/**
	 * Setta il tipo di ricerca
	 * Valori ammessi:
	 *  phrase
	 *  multiterm
	 *  range
	 *  wildcard
	 *  fuzzy
	 * 
	 * @param string $searchType
	 * @return void
	 */
	public function setSearchType($searchType) {$this->searchType = $searchType;}
	/**
	 * Ritorna il tipo di ricerca
	 * 
	 * @return string 
	 */
	public function getSearchType() {return $this->searchType;}
}
?>