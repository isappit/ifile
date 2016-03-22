<?php
namespace Isappit\Ifile\Tokenfilter\Stemming\Danish;

use ZendSearch\Lucene\Analysis\Token as Token;

/**
 * IFile Framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage TokenFilter/Stemming/en-GB
 * @author 	   Giampaolo Losito, Antonio Di Girolamo
 * @copyright  2011 isApp.it (www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Lucene Token Filter per lo stemming della lingua Danese utilizzando la libreria PECL "Stem" 
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage TokenFilter/Stemming/en-GB
 * @author 	   Giampaolo Losito, Antonio Di Girolamo
 * @copyright  2011 isApp.it (www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */

/** Zend_Search_Lucene_Analysis_TokenFilter */
// require_once 'Zend/Search/Lucene/Analysis/TokenFilter.php';
/** Zend_Search_Lucene_Exception */
// require_once 'Zend/Search/Lucene/Exception.php';


class DanishPECLStemmer implements ZendSearch\Lucene\Analysys\TokenFilter\TokenFilterInterface
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
			throw new Isappit\Ifile\Tokenfilter\Stemming\IFileStemException("PECL Stem library not supported.");
		}
		
		if (!function_exists('stem_danish')) {
			throw new Isappit\Ifile\Tokenfilter\Stemming\IFileStemException("Danish Stemmer not supported. Install and compile PECL Stem with Danish Stemmer.");
		}
    }

    /**
     * Normalize Token or remove it (if null is returned)
     *
     * @param ZendSearch\Lucene\Analysis\Token $srcToken
     * @return ZendSearch\Lucene\Analysis\Token
     */
    public function normalize(ZendSearch\Lucene\Analysis\Token $srcToken) {
		
		$newToken = new Token(
                              stem_danish( $srcToken->getTermText() ),
                              $srcToken->getStartOffset(),
                              $srcToken->getEndOffset());

        $newToken->setPositionIncrement($srcToken->getPositionIncrement());

        return $newToken;
    }
}

