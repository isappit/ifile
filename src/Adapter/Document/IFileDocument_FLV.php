<?php
namespace Isappit\Ifile\Adapter\Document;

/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/** Adatpter_Search_Lucene_Document_Abstract */
require_once 'Adapter_Search_Lucene_Document_Multimedia.php';

/**
 * Adapter per il recupero del contenuto degli ID3 TAG dei file FLV
 * Flash Video
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_FLV extends Adapter_Search_Lucene_Document_Multimedia 
{
	public function __construct() {
		parent::__construct();					 
	}
}
?> 