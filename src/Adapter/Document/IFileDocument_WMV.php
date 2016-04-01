<?php
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

namespace Isappit\Ifile\Adapter\Document;

use Isappit\Ifile\Adapter\IFileAdapterMultimedia;

/**
 * Adapter per il recupero del contenuto dei METATAG dei file WMV
 * Windows Media Video
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_WMV extends IFileAdapterMultimedia 
{
	public function __construct() {		
		parent::__construct();	
		// definisco il tipo di TAG da indicizzare 
		$this->tagType = 'none';			 
	}	
}
?> 