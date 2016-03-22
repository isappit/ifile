<?php
namespace Isappit\Ifile\Config\Helpers;
/**
 * IFile framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Classe di utiliti per la configurazione della XPDF
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileXpdfConfig {
	
	/**
	 * Ritorna la password criptata.
	 * Questo metodo permetterebbe di definire un processo 
	 * di cripting in caso si voglia creare un processo automatico per l'inserimento 
	 * della password nel file XML di configurazione 
	 * Il metodo deve essere implementato manualmente a secoda dei casi.
	 * 
	 * @param string $opw
	 * @return 
	 */
	static function encodeOpw($opw) {
		return $opw;
	}
	
	/**
	 * Ritorna la password decriptata.
	 * Questo metodo permetterebbe di definire un processo 
	 * di encripting in caso si sia inserita nel XML una passoword criptata
	 * IMPORTANTE:::::
	 * Il metodo deve essere implementato manualmente a secoda dei casi.
	 * 
	 * @param string $opw
	 * @return 
	 */
	static function decodeOpw($opw) {
		return $opw;
	}
}

