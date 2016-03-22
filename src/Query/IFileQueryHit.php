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
 * Gestione della hit dei risultati di ricerca. 
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage quary
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */

/** Zend_Search_Lucene_Search_QueryHit */
require_once 'Zend/Search/Lucene/Search/QueryHit.php';

/**
 * Oggetto per la gestione dei risultati.
 * 
 * Questo permette di ritornare un'oggetto con le stesse
 * caratteristiche che ha l'oggetto Zend_Search_Lucene_Search_QueryHit
 * che utilizza ZEND_SEARCH_LUCENE per i risultati delle query
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileQueryHit extends Zend_Search_Lucene_Search_QueryHit {
	
	/**
     * Costruttore - passa un oggettodi IFile_Indexing_Interface 
     *
     * @param IFile_Indexing_Interface $index
     */

    public function __construct(IFile_Indexing_Interface $index)
    {
        $this->_index = $index;
    }	
}
?>