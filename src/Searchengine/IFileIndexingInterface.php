<?php
namespace Isappit\Ifile\Searchengine;

use Isappit\Ifile\Query\IFileQueryRegistry;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 IFile_Indexing_Interface.php 2011-01-24 20:13:58
 */

/**
 * Interfaccia pubblica per la gestione dell'indicizzazione dei file
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
interface IFileIndexingInterface {
	
	/**
	 * Tipo di field: Parola chiave
	 */
	const FIELD_TYPE_KEYWORD = 'Keyword';
	/**
	 * Tipo di field: Non indicizzato
	 */
	const FIELD_TYPE_UNINDEXED = 'UnIndexed';
	/**
	 * Tipo di field: Binario
	 */
	const FIELD_TYPE_BINARY = 'Binary';
	/**
	 * Tipo di field: Testo
	 */
	const FIELD_TYPE_TEXT = 'Text';
	/**
	 * Tipo di field: TesNon storicizzato
	 */
	const FIELD_TYPE_UNSTORED = 'UnStored';
	
	/**
	 * Aggiunge un documento ad un indice
	 *  
	 * @param Zend_Search_Lucene_Document $doc [optional]
	 * @return void 
	 */
	public function addDocument(Zend_Search_Lucene_Document $doc = null);
	
	/**
	 * Aggiunge un ordinamento alla query 
	 *
	 * @param string $field
	 * @param string $type [optional]
	 * @param string $order [optional]
	 * 
	 * @return void 
	 */
	public function setSort($field, $type = SORT_REGULAR, $order = SORT_ASC);
	
	/**
	 * Aggiunge un campo personalizzato al documento da indicizzare 
	 *  
	 * @param string $field
	 * @param string $term
	 * @param string $type
	 * 
	 * @return void 
	 */
	public function addCustomField($field, $term, $type);
	
	/**
	 * Ritorna un array dei campi personalizzati settati 
	 *  
	 * @return array 
	 */
	public function getCustomField();
	
	/**
	 * Esegue la query di ricerca per i termini
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed 
	 */
	public function query(IFileQueryRegistry $query);

	/**
	 * Esegue la query di ricerca per frasi
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed 
	 */
	public function queryPhrase(IFileQueryRegistry $query);
	
	/**
	 * Esegue la fuzzy query
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
	 */
	public function queryFuzzy(IFileQueryRegistry $query);
	
	/**
	 * Esegue una boolean query
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
	 */
	public function queryBoolean(IFileQueryRegistry $query);
		
	/**
	 * Esegue la query con caratteri Wildcard
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
	 */
	public function queryWildcard(IFileQueryRegistry $query);
	
	/**
	 * Esegue la query su un range di dati 
	 * 
	 * @param IFileQueryRegistry $query
	 * @return mixed
	 */
	public function queryRange(IFileQueryRegistry $query);
	
	/**
	 * Esegue una query parserizzando la stringa di ricerca
	 * 
	 * @param string $query
	 * @return mixed
	 */
	public function queryParser($query);
		
	/**
	 * Ottimizza l'indice
	 * 
	 * @return void
	 */
	public function optimize();
	
	/**
	 * Marca un documento come cancellato
	 * Ritorna un eccezione Zend_Search_Lucene_Exception se $id non e'
	 * presente nel range degli id dell'indice 
	 * @param integer $id
	 * @return void
	 * @throws Zend_Search_Lucene_Exception 
	 */
	public function delete($id);
	
	/**
	 * Setta il path del file da indicizzare
	 * @param stringa $indexFile
	 * @return void
	 */
	public function setIndexFile($indexFile);
	
	/**
	 * Ritorna un oggetto IFileInfoFile 
	 * @return IFileInfoFile
	 */
	public function getIndexFile();
		
	/**
	 * Setta il limite dei risultati da estrarre
	 * @param integer $limit
	 * @return void
	 */	
	public function setResultLimit($limit);
	
	/**
	 * Ritorna il limite dei risultati da estrarre
	 * @return integer
	 */	
	public function getResultLimit();
	
	/**
	 * Setta il field di default su cui ricercare i termini
	 * @param string $field
	 * @return void
	 */	
	public function setDefaultSearchField($field);
	
	/**
	 * Ritorna il field di default su cui ricercare i termini
	 * @return string
	 */	
	public function getDefaultSearchField();
		
	/**
	 * Ritorna il numero di documenti inseriti compresi quelli marcati come cancellati
	 * @return integer
	 */
	public function count();
	
	/**
	 * Ritorna il numero di documenti realmente presenti senza quelli marcati come cancellati
	 * @return integer
	 */
	public function numDocs();	
	
	/**
	 * Ritorna un array dei campi presenti nell'indice
	 * @param boolean $indexed se true torna solo quelli indicizzati
	 * @return array
	 */
	public function getFieldNames($indexed = false);
	
	/**
	 * Ritorna l'oggetto documento
	 * Ritorna un eccezione Zend_Search_Lucene_Exception se $id non e'
	 * presente nel range degli id dell'indice 
	 * @param integer $id
	 * @return Zend_Search_Lucene_Document
	 * @throws Zend_Search_Lucene_Exception 
	 */
	public function getDocument($id);
	
	/**
	 * Ritorna un array contenente tutti gli oggetti documento
	 * presenti nell'indice, senza i documenti marcati come cancellati. 
	 * Se settato il parametro $deleted = true allora ritorna anche 
	 * i documenti cancellati.
	 * 
	 * Ritorna NULL se non sono presenti documenti 
	 * 
	 * @param boolean $deleted [optional]
	 * @param integer $offset  [optional] offset di partenza
	 * @param integer $maxrow  [optional] numero massimo di documenti
	 * @return mixed 
	 */
	public function getAllDocument($deleted = false, $offset = null, $maxrow = null);
	
	/**
	 * Setta la gestire manualmente o in modo automatico del commit
	 * @param boolean $autocommit
	 * @return void
	 */
	public function autoCommit($autocommit);
	
	/**
	 * Committa l'indice
	 * @return void
	 */
	public function commit();
	
	/**
	 * Verifica se ci sono documenti calcellati
	 * @return boolean
	 */
	public function hasDeletions();
	
	/**
	 * Verifica se esiste il termine
	 * @param string $term
	 * @param string $field [0prional]
	 * @return boolean
	 */
	public function hasTerm($term, $field = null);
	
	/**
	 * Ritorna un array di oggetti "Zend_Search_Lucene_Index_Term", termini, presenti nell'indice
	 * @return array di Zend_Search_Lucene_Index_Term
	 */
	public function terms();
	
	/**
	 * Ritorna un array di oggetti "Zend_Search_Lucene_Index_Term", termini, presenti in un field (campo) 
	 * @param string $field 
	 * @return 
	 */
	public function getTermsForField($field);
	
	/**
	 * Verifica se un documento e' stato marcato come cancellato
	 * Ritorna un eccezione Zend_Search_Lucene_Exception se $id non e'
	 * presente nel range degli id dell'indice 
	 * @return boolean
	 * @throws Zend_Search_Lucene_Exception 
	 */
	public function isDeleted($id);
	
	/**
	 * Ripristina tutti i documenti marcati come cancellati
	 * Implementato in Zend_Search_Lucene dalla versione (x.x.x)
	 * @return void
	 */
	public function undeletedAll();
	
	/** 
	 * Cancella l'indice e ritorna il numero di documenti cancellati
	 * 
	 * Se viene passato TRUE cancella solo tutti i documenti dall'indice
	 * e ritorna il numero di documenti cancellati altrimenti elimina completamente l'indice
	 * 
	 * @param bool $doc [optional]
	 * @return integer
	 */
	public function deleteAll($doc = false);
}
?>