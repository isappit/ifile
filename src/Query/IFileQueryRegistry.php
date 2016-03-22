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
 * Registro dei termini da ricercare 
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage query
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileQueryRegistry {
	
	/**
	 * Registro dei termini da ricercare
	 * @var array
	 */
	private $registry = array();
	
	public function __construct() {}
	
	/**
	 * Setta le proprieta' della query
	 * 
	 * @param string $term
	 * @param string $field [optional]
	 * @param mixed $match [optional]
	 * @param integer $position [optional]
	 * @param string $encoding [optional]
	 * @return void
	 */
	public function setQuery($term, $field = null, $match = null, $position = null, $encoding = '', $searchType = false) {
				
		$this->checkTerm($term);
		$this->checkField($field);
		$this->checkMatch($match);
		$this->checkPosition($position);
		$this->checkEncoding($encoding);
		$this->checkSearchType($searchType);
		
		$query = new IFileQuery();
		$query->setTerm($term);
		$query->setField($field);
		$query->setMatch($match);
		$query->setPosition($position);
		$query->setEncoding($encoding);
		$query->setSearchType($searchType);
		
		array_push($this->registry, $query);
	}
	
	/**
	 * Ritorna l'array delle query
	 * 
	 * @return array 
	 */
	public function getQuery() {return $this->registry;}
	
	/**
	 * Ritorna il termine con ID ricercato.
	 * 
	 * Se ID del termine non esiste allora ritorna null 
	 * 
	 * @return IFileQuery 
	 */
	public function getTerm($id) {
		
		if (isset($this->registry[$id])) { 
			return $this->registry[$id];
		}
		
		return null;
	}
	
	/**
	 * Ritorna il numero di query settate
	 * 
	 * @return integer 
	 */
	public function count() {return count($this->registry);}
	
	/**
	 * Controllo sul termine
	 * 
	 * Questo metodo potrebbe essere implementato estendendo la 
	 * classe IFileQueryRegistry per gestire eventuali controlli 
	 * sui termini di ricerca.
	 * Invoca una eccezione di tipo IFileQueryException in caso di errore
	 * 
	 * @param string $term
	 * @return void 
	 * @throws IFileQueryException 
	 */
	public function checkTerm($term) {}	 
	
	/**
	 * Controllo sul field
	 * 
	 * Questo metodo potrebbe essere implementato estendendo la 
	 * classe IFileQueryRegistry per gestire eventuali controlli 
	 * sui campi di ricerca
	 * Invoca una eccezione di tipo IFileQueryException in caso di errore
	 *  
	 * @param string $field
	 * @return void
	 * @throws IFileQueryException 
	 */
	public function checkField($field) {}

	/**
	 * Controllo sul match
	 * 
	 * @param string $match
	 * @return void
	 * @throws IFileQueryException 
	 */
	private function checkMatch($match) {
		
		$check = ($match === IFileQuery::MATCH_REQUIRED) | ($match === IFileQuery::MATCH_PROHIBITEN) | ($match === IFileQuery::MATCH_OPTIONAL);   
		
		if (!$check) {				
			throw new IFileQueryException('Query does not match correctly.');
		}
	}
	
	/**
	 * Controllo sulla posizione
	 * 
	 * Questo metodo potrebbe essere implementato estendendo la 
	 * classe IFileQueryRegistry per gestire eventuali controlli 
	 * sulla posizione del termine per le frasi
	 * Invoca una eccezione di tipo IFileQueryException in caso di errore
	 *  
	 * @param string $position
	 * @return void
	 * @throws IFileQueryException 
	 */
	public function checkPosition($position) {}
	
	/**
	 * Controllo sul tipo di Encoding
	 * 
	 * Questo metodo potrebbe essere implementato estendendo la 
	 * classe IFileQueryRegistry per gestire eventuali controlli 
	 * sul tipo di encoding permesso del termine per le frasi
	 * Invoca una eccezione di tipo IFileQueryException in caso di errore
	 *  
	 * @param string $encoding
	 * @return void
	 * @throws IFileQueryException 
	 */
	public function checkEncoding($encoding) {}
	
	/**
	 * Controllo sul tipo di ricerca
	 * 
	 * Questo metodo potrebbe essere implementato estendendo la 
	 * classe IFileQueryRegistry per gestire eventuali controlli 
	 * sul tipo di ricerca permesso del termine per le frasi
	 * Invoca una eccezione di tipo IFileQueryException in caso di errore
	 *  
	 * @param string $searchType
	 * @return void
	 * @throws IFileQueryException 
	 */
	private function checkSearchType($searchType) {
		$check = ($searchType === false) |
				 ($searchType === IFileQuery::QUERY_PHRASE) | 
				 ($searchType === IFileQuery::QUERY_WILDCARD) |
				 ($searchType === IFileQuery::QUERY_RANGE) |
				 ($searchType === IFileQuery::QUERY_MULTITERM) | 
				 ($searchType === IFileQuery::QUERY_FUZZY);   
		
		if (!$check) {				
			throw new IFileQueryException('Search type is not correct.');
		}
	}
}
?>