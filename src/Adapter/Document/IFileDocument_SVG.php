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

/** Adapter_Search_Lucene_Document_PNG */
require_once 'Adapter_Search_Lucene_Document_Image.php';

/**
 * Adapter per il recupero del contenuto degli EXIF TAG dei file SVG
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_SVG extends Adapter_Search_Lucene_Document_Image 
{
	protected $tagType = 'svg';
	
	public function __construct() {
		parent::__construct();
	}
}
?> 