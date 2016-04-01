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

namespace Isappit\Ifile\Adapter;

use Isappit\Ifile\Adapter\IFileAdapterAbstract;
use Isappit\Ifile\Adapter\Helpers\Multimedia2Txt;
use Isappit\Ifile\Config\IFileConfig;
use Isappit\Ifile\Exception\IFileAdapterException;
use ZendSearch\Lucene\Document as Zend_Search_Lucene_Document;
use ZendSearch\Lucene\Document\Field as Zend_Search_Lucene_Field;

/**
 * Adapter per il recupero del contenuto dei metadati dei file multimediali
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class IFileAdapterMultimedia extends IFileAdapterAbstract 
{
	/**
	 * Tipo di TAG
	 * @var string
	 */
	protected $tagType = "id3";
	
	public function __construct() {
		parent::__construct();				 
	}
	
	/**
	 * Ritorna un oggetto ZendSearch\Lucene\Document
	 *
	 * Implementa il metodo dell'interfaccia IFileAdapterInterface
	 * 
	 * @return ZendSearch\Lucene\Document
	 */
	public function loadParserFile()
    {
		return $this->parse();;
    }
	
	/**
	 * Recupera le informazioni del file multimediale
	 * 
	 * @return void
	 */
	protected function parse() {
		
		// Recupera i dati di configurazione 		
		$IfileConfig = IFileConfig::getInstance();
		
		// istanzia la classe per la parserizzazione dei file MP3
		$multimedia = new Multimedia2Txt($this->getFilename(), $IfileConfig->getConfig('encoding'));
    	//$result 	= $multimedia->parseTag($this->tagType); 
    	$multimedia->parseMultimediaTag(); 
		
		// recupera il corpo del file multimediale
		$body = $multimedia->getTextTag();
		
		if (empty($body)) {
			throw new IFileAdapterException('Multimedia Metatag not found');
		}
		
		// creazuione dell'oggetto ZendSearch\Lucene\Document 
		$doc = new Zend_Search_Lucene_Document();
		
		// Contenuto del Corpo
		$doc->addField(Zend_Search_Lucene_Field::UnStored('body', $body, $IfileConfig->getConfig('encoding')));
					
		// Inserisce i dati del file all'interno dell'indice come Field
		// Codificato da
		if ($multimedia->issetNotEmpty('encodedBy')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('encodedBy', $multimedia->getEncodedBy(), $IfileConfig->getConfig('encoding')));
		}
		// Traccia
		if ($multimedia->issetNotEmpty('track')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('track', $multimedia->getTrack(), $IfileConfig->getConfig('encoding')));
		}
		// Pubblicato
		if ($multimedia->issetNotEmpty('publisher')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('publisher', $multimedia->getPublisher(), $IfileConfig->getConfig('encoding')));
		}
		// Disco
		if ($multimedia->issetNotEmpty('partOfASet')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('partOfASet', $multimedia->getPartOfASet(), $IfileConfig->getConfig('encoding')));
		}
		// Battiti al minuto
		if ($multimedia->issetNotEmpty('bpm')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('bpm', $multimedia->getBpm(), $IfileConfig->getConfig('encoding')));
		}
		// originalArtist
		if ($multimedia->issetNotEmpty('originalArtist')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('originalArtist', $multimedia->getOriginalArtist(), $IfileConfig->getConfig('encoding')));
		}
		// Copyright
		if ($multimedia->issetNotEmpty('copyright')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('copyright', $multimedia->getCopyright(), $IfileConfig->getConfig('encoding')));
		}
		// Gruppo
		if ($multimedia->issetNotEmpty('band')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('band', $multimedia->getBand(), $IfileConfig->getConfig('encoding')));
		}
		// Genere
		if ($multimedia->issetNotEmpty('genre')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('genre', $multimedia->getGenre(), $IfileConfig->getConfig('encoding')));
		}
		// Compositore
		if ($multimedia->issetNotEmpty('composer')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('composer', $multimedia->getComposer(), $IfileConfig->getConfig('encoding')));
		}
		// Anno
		if ($multimedia->issetNotEmpty('year')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('year', $multimedia->getYear(), $IfileConfig->getConfig('encoding')));
		}
		// Titolo
		if ($multimedia->issetNotEmpty('title')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('title', $multimedia->getTitle(), $IfileConfig->getConfig('encoding')));
		}
		// Album
		if ($multimedia->issetNotEmpty('album')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('album', $multimedia->getAlbum(), $IfileConfig->getConfig('encoding')));
		}
		// Artista
		if ($multimedia->issetNotEmpty('artist')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('artist', $multimedia->getArtist(), $IfileConfig->getConfig('encoding')));
		}
		// Commento
		if ($multimedia->issetNotEmpty('comment')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('UserComment', $multimedia->getComment(), $IfileConfig->getConfig('encoding')));
		}
		// URL
		if ($multimedia->issetNotEmpty('urlUser')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('urlUser', $multimedia->getUrlUser(), $IfileConfig->getConfig('encoding')));
		}
		// Testo della canzone
		if ($multimedia->issetNotEmpty('unsynchronisedLyric')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('unsynchronisedLyric', $multimedia->getUnsynchronisedLyric(), $IfileConfig->getConfig('encoding')));
		}
		// Numero del disco 
		if ($multimedia->issetNotEmpty('discNumber')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('discNumber', $multimedia->getDiscNumber(), $IfileConfig->getConfig('encoding')));
		}
		// Compilation 
		if ($multimedia->issetNotEmpty('compilation')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('compilation', $multimedia->getCompilation(), $IfileConfig->getConfig('encoding')));
		}
		// Votazione 
		if ($multimedia->issetNotEmpty('rating')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('rating', $multimedia->getRating(), $IfileConfig->getConfig('encoding')));
		}
		// Stick 
		if ($multimedia->issetNotEmpty('stik')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('stik', $multimedia->getStik(), $IfileConfig->getConfig('encoding')));
		}
		// Encoder 
		if ($multimedia->issetNotEmpty('encoder')) {
			$doc->addField(Zend_Search_Lucene_Field::Text('encoder', $multimedia->getEncoder(), $IfileConfig->getConfig('encoding')));
		}
		// Tempo di esecuzione come stringa 
		if ($multimedia->issetNotEmpty('playTimeString')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('playTimeString', $multimedia->getPlayTimeString(), $IfileConfig->getConfig('encoding')));
		}
		// Tempo di esecuzione come secondi e millisecondi 
		if ($multimedia->issetNotEmpty('playTimeSecond')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('playTimeSecond', $multimedia->getPlayTimeSecond(), $IfileConfig->getConfig('encoding')));
		}
		// Bitrate
		if ($multimedia->issetNotEmpty('bitrate')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('bitrate', $multimedia->getBitrate(), $IfileConfig->getConfig('encoding')));
		}
		// Risoluzione Video X
		if ($multimedia->issetNotEmpty('resolutionX')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('XResolution', $multimedia->getResolutionX(), $IfileConfig->getConfig('encoding')));
		}
		// Risoluzione Video Y
		if ($multimedia->issetNotEmpty('resolutionY')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('YResolution', $multimedia->getResolutionY(), $IfileConfig->getConfig('encoding')));
		}
		// Formato Video
		if ($multimedia->issetNotEmpty('videoFormat')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('videoFormat', $multimedia->getVideoFormat(), $IfileConfig->getConfig('encoding')));
		}
		// Frame rate
		if ($multimedia->issetNotEmpty('frameRate')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('frameRate', $multimedia->getFrameRate(), $IfileConfig->getConfig('encoding')));
		}
		// Fourcc (four character code)
		if ($multimedia->issetNotEmpty('fourcc')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('fourcc', $multimedia->getFourcc(), $IfileConfig->getConfig('encoding')));
		}
		// Video Encoder
		if ($multimedia->issetNotEmpty('videoEncoder')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('videoEncoder', $multimedia->getVideoEncoder(), $IfileConfig->getConfig('encoding')));
		}
		// Formato Audio
		if ($multimedia->issetNotEmpty('audioFormat')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('audioFormat', $multimedia->getAudioFormat(), $IfileConfig->getConfig('encoding')));
		}
		// Codec Audio
		if ($multimedia->issetNotEmpty('codecAudio')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('codecAudio', $multimedia->getCodecAudio(), $IfileConfig->getConfig('encoding')));
		}
		// Tipo Canale Audio
		if ($multimedia->issetNotEmpty('audioChannelMode')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('audioChannelMode', $multimedia->getAudioChannelMode(), $IfileConfig->getConfig('encoding')));
		}
		// Canali Audio
		if ($multimedia->issetNotEmpty('audioChannels')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('audioChannels', $multimedia->getAudioChannels(), $IfileConfig->getConfig('encoding')));
		}
		// Frequenza di campionamento
		if ($multimedia->issetNotEmpty('sampleRate')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('sampleRate', $multimedia->getSampleRate(), $IfileConfig->getConfig('encoding')));
		}
		// Audio Encoder
		if ($multimedia->issetNotEmpty('audioEncoder')) {
			$doc->addField(Zend_Search_Lucene_Field::keyword('audioEncoder', $multimedia->getAudioEncoder(), $IfileConfig->getConfig('encoding')));
		}
				
		return $doc;
    }
}
?> 