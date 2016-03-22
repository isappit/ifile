<?php
namespace Isappit\Ifile\Tokenfilter\Stemming\English;
/**
 * IFile Framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage TokenFilter/Stemming/en-GB
 * @author 	   Giampaolo Losito, Antonio Di Girolamo
 * @copyright  2011 isApp.it (www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0
 */

/**
 * Lucene Token Filter per lo stemming della lingua inglese utilizzando la PorterStemmer
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage TokenFilter/Stemming/en-GB
 * @author 	   Giampaolo Losito, Antonio Di Girolamo
 * @copyright  2011 isApp.it (www.isapp.it)
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
/** PorterStemmer */
require_once 'EnglishStemmer/PorterStemmer.php';
/** Zend_Search_Lucene_Analysis_TokenFilter */
require_once 'Zend/Search/Lucene/Analysis/TokenFilter.php';
/** Zend_Search_Lucene_Exception */
require_once 'Zend/Search/Lucene/Exception.php';


class EnglishPorterStemmer extends Zend_Search_Lucene_Analysis_TokenFilter
{
    
	/**
     * Constructs
     */
    public function __construct(){}
	
	/**
     * Normalize Token or remove it (if null is returned)
     *
     * @param Zend_Search_Lucene_Analysis_Token $srcToken
     * @return Zend_Search_Lucene_Analysis_Token
     */
    public function normalize(Zend_Search_Lucene_Analysis_Token $srcToken) {
		
		echo "Prima: ".$srcToken->getTermText()."<br />";
		
		$newToken = new Zend_Search_Lucene_Analysis_Token(
                                     PorterStemmer::stem( $srcToken->getTermText() ),
                                     $srcToken->getStartOffset(),
                                     $srcToken->getEndOffset());

		echo "Dopo: ".$newToken->getTermText()."<br />";
		echo "--------------------------------<br />";

        $newToken->setPositionIncrement($srcToken->getPositionIncrement());

        return $newToken;
    }
}

