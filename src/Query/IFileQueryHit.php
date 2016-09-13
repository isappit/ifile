<?php
/**
 * IFile framework
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage query
 * @link       https://github.com/isappit/ifile for the canonical source repository
 * @copyright  Copyright (c) 2011-2016 isApp.it (http://www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */

namespace Isappit\Ifile\Query;

use Isappit\Ifile\Searchengine\IFileIndexingInterface;
use ZendSearch\Lucene\Search\QueryHit as Zend_Search_Lucene_Search_QueryHit;

/**
 * Oggetto per la gestione dei risultati.
 * 
 * Questo permette di ritornare un'oggetto con le stesse
 * caratteristiche che ha l'oggetto Zend_Search_Lucene_Search_QueryHit
 * che utilizza ZEND_SEARCH_LUCENE per i risultati delle query
 */
class IFileQueryHit extends Zend_Search_Lucene_Search_QueryHit {
	
	/**
     * Costruttore - passa un oggetto di IFileIndexingInterface
     *
     * @param IFile_Indexing_Interface $index
     */

    public function __construct(IFileIndexingInterface $index)
    {
        $this->_index = $index;
    }	
}
?>