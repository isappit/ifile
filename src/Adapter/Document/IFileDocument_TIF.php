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

use Isappit\Ifile\Adapter\IFileAdapterImage;

/**
 * Adapter per il recupero del contenuto degli EXIF TAG dei file TIF
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileDocument_TIF extends IFileAdapterImage 
{
	protected $tagType = 'tiff';
	
	public function __construct() {
		parent::__construct();
	}
}
?> 