<?php
namespace Isappit\Ifile\Tokenfilter\Stemming\German;

use Isappit\Ifile\Exception\IFileStemException;
use Isappit\Ifile\Servercheck\LuceneServerCheck;
use ZendSearch\Lucene\Analysis\Token as Zend_Search_Lucene_Analysis_Token;
use ZendSearch\Lucene\Analysis\TokenFilter\TokenFilterInterface;
/**
 * IFile Framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage TokenFilter/Stemming/German
 * @author 	   Giampaolo Losito, Antonio Di Girolamo
 * @copyright  2011 isApp.it (www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Lucene Token Filter per lo stemming della lingua Tedesca utilizzando la libreria PECL "Stem" 
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage TokenFilter/Stemming/German
 * @author 	   Giampaolo Losito, Antonio Di Girolamo
 * @copyright  2011 isApp.it (www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */

class GermanPECLStemmer implements TokenFilterInterface
{
    
    /**
     * Construttore
     * 
     * @throws IFile_Stem_Exception
     */
    public function __construct(){
    	// Verifica la presenza della libreria PECL Stem 
		$serverCheck = LuceneServerCheck::getInstance();
		$serverCheck->serverCheck();
		$reportServerCheck = $serverCheck->getReportCheck();
		// check Stem 
		$reportCheckStem = $reportServerCheck['Extension']['stem'];
		
		if (!$reportCheckStem->getCheck()) {
			throw new IFileStemException("PECL Stem library not supported.");
		}
		
		if (!function_exists('stem_german')) {
			throw new IFileStemException("German Stemmer not supported. Install and compile PECL Stem with German Stemmer.");
		}
    }

    /**
     * Normalize Token or remove it (if null is returned)
     *
     * @param Zend_Search_Lucene_Analysis_Token $srcToken
     * @return Zend_Search_Lucene_Analysis_Token
     */
    public function normalize(Zend_Search_Lucene_Analysis_Token $srcToken) {
		
		$newToken = new Zend_Search_Lucene_Analysis_Token(
                                     stem_german( $srcToken->getTermText() ),
                                     $srcToken->getStartOffset(),
                                     $srcToken->getEndOffset());

        $newToken->setPositionIncrement($srcToken->getPositionIncrement());

        return $newToken;
    }
}

