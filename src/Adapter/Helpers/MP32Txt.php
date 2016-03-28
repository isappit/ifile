<?php
namespace Isappit\Ifile\Adapter\Helpers;

/**
 * IFile Framework
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright 
 * @license	   GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

//require_once 'simplehtmldom/simple_html_dom.php';

/**
 * Recupera le informazioni di un file MP3 
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
class MP32Txt {
	
	/**
	 * Versione dell'ID3
	 * 
	 * @var string
	 */
	private $id3Version = '0.1';
	/**
	 * Nome dell'estensione ID3
	 * 
	 * @var string
	 */
	private $id3Extension = 'id3';		
	/**
	 * Tutto il contenuto del TAG
	 * 
	 * @var string
	 */
	private $textTag;
	/**
	 * Artista della canzone
	 * 
	 * @var string
	 */
	private $artist;
	/**
	 * Titolo della canzone
	 * 
	 * @var string
	 */
	private $title;
	/**
	 * Album della canzone
	 * @var string 
	 */
	private $album;
	/**
	 * Anno della canzone
	 * @var integer 
	 */
	private $year;
	/**
	 * Compositore della canzone
	 * @var string 
	 */
	private $composer;
	/**
	 * Genere della canzone
	 * @var integer 
	 */
	private $genre;
	/**
	 * Gruppo 
	 * @var string 
	 */
	private $band;
	/**
	 * Copyright
	 * @var string 
	 */
	private $copyright;
	/**
	 * Artista originale
	 * @var string 
	 */
	private $originalArtist;
	/**
	 * Battiti al minuto
	 * @var integer
	 */
	private $bpm;
	/**
	 * Disco
	 * @var string
	 */
	private $partOfASet;
	/**
	 * Editore
	 * @var string
	 */
	private $publisher;
	/**
	 * Traccia
	 * @var integer
	 */
	private $track;
	/**
	 * Codificato da
	 * @var string
	 */
	private $encodedBy;
	
	
	/** 
	 * Setta il nome dell'artista della canzone 
	 * 
	 * @param string $artist
	 * @return void
	 */
	public function setArtist($artist) { $this->artist = $artist; }
	/** 
	 * Setta il titolo della canzone 
	 * 
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) { $this->title = $title; }
	/** 
	 * Setta il nome dell'album della canzone 
	 * 
	 * @param string $album
	 * @return void
	 */
	public function setAlbum($album) { $this->album = $album; }
	/** 
	 * Setta da chi e' stato codificato 
	 * 
	 * @param string $encodedBy
	 * @return void
	 */
	public function setEncodedBy($encodedBy) { $this->encodedBy = $encodedBy; }
	/** 
	 * Setta la traccia della canzone 
	 * 
	 * @param integer $track
	 * @return void
	 */
	public function setTrack($track) { $this->track = $track; }
	/** 
	 * Setta l'editore della canzone 
	 * 
	 * @param string $publisher
	 * @return void
	 */
	public function setPublisher($publisher) { $this->publisher = $publisher; }
	/** 
	 * Setta il disco della canzone 
	 * 
	 * @param string $partOfASet
	 * @return void
	 */
	public function setPartOfASet($partOfASet) { $this->partOfASet = $partOfASet; }
	/** 
	 * Setta i battiti al minuto della canzone 
	 * 
	 * @param string $originalArtist
	 * @return void
	 */
	public function setBpm($bpm) { $this->bpm = $bpm; }
	/** 
	 * Setta l'artista originale della canzone 
	 * 
	 * @param string $originalArtist
	 * @return void
	 */
	public function setOriginalArtist($originalArtist) { $this->originalArtist = $originalArtist; }
	/** 
	 * Setta il copyright della canzone 
	 * 
	 * @param string $copyright
	 * @return void
	 */
	public function setCopyright($copyright) { $this->copyright = $copyright; }
	/** 
	 * Setta il nome della band (gruppo) della canzone 
	 * 
	 * @param string $band
	 * @return void
	 */
	public function setBand($band) { $this->band = $band; }
	/** 
	 * Setta l'anno della canzone 
	 * 
	 * @param string $composer
	 * @return void
	 */
	public function setComposer($composer) { $this->composer = $composer; }
	/** 
	 * Setta il genere della canzone 
	 * 
	 * @param integer $genre
	 * @return void
	 */
	public function setGenre($genre) { $this->genre = $genre; }
	/** 
	 * Setta il commento della canzone 
	 * 
	 * @param integer $year
	 * @return void
	 */
	public function setYear($year) { $this->year = $year; }
	/** 
	 * Setta il contenuto del TAG in formato testuale 
	 * 
	 * @param integer $textTag
	 * @return void
	 */
	public function setTextTag($textTag) { $this->textTag = $textTag; }
	
	/** 
	 * Ritorna il nome dell'artista della canzone 
	 * 
	 * @return string 
	 */
	public function getArtist() { return $this->artist; }
	/** 
	 * Ritorna il titolo della canzone 
	 * 
	 * @return string 
	 */
	public function getTitle() { return $this->title; }
	/** 
	 * Ritorna il nome dell'album della canzone 
	 * 
	 * @return string 
	 */
	public function getAlbum() { return $this->album; }
	/** 
	 * Setta da chi e' stato codificato 
	 * 
	 * @return string
	 */
	public function getEncodedBy() { return $this->encodedBy; }
	/** 
	 * Ritorna la traccia della canzone 
	 * 
	 * @return integer
	 */
	public function getTrack() { return $this->track; }
	/** 
	 * Ritorna l'editore della canzone 
	 * 
	 * @return string
	 */
	public function getPublisher() { return $this->publisher; }
	/** 
	 * Ritorna il disco della canzone 
	 * 
	 * @return string
	 */
	public function getPartOfASet() { return $this->partOfASet; }
	/** 
	 * Ritorna i battiti al minuto della canzone 
	 * 
	 * @return string
	 */
	public function getBpm() { return $this->bpm; }
	/** 
	 * Ritorna l'artista originale della canzone 
	 * 
	 * @return string
	 */
	public function getOriginalArtist() { return $this->originalArtist; }
	/** 
	 * Ritorna il copyright della canzone 
	 * 
	 * @return string
	 */
	public function getCopyright() { return $this->copyright; }
	/** 
	 * Ritorna il nome della band (gruppo) della canzone 
	 * 
	 * @return string
	 */
	public function getBand() { return $this->band; }
	/** 
	 * Ritorna l'anno della canzone 
	 * 
	 * @return string
	 */
	public function getComposer() { return $this->composer; }
	/** 
	 * Ritorna il genere della canzone 
	 * 
	 * @return string
	 */
	public function getGenre() {
		$genre = @id3_get_genre_name($this->genre);
		return  $genre;
 	}
	/** 
	 * Ritorna l'anno della canzone 
	 * 
	 * @return integer
	 */
	public function getYear() { return $this->year;}
	/** 
	 * Ritorna il contenuto del TAG in formato testuale 
	 * 
	 * @return string
	 */
	public function getTextTag() { return implode(' ' ,$this->textTag); }
	
	
	/**
	 * Ritorna True se riesce a parserizzare il file 
	 * 
	 * Recupera dal file MP3 le informazioni ID3 se questi sono presenti.
	 *  
	 * @param string $filename
	 * @return bool
	 */
	public function parseTagID3($filename){
		// array dei tag recuperai dal file MP3
		$tags = array();
		// versione del TAG
		$version = id3_get_version($filename);
		
		// verifica se la versione del ID3 e' la 2.1
		if ($version & ID3_V2_1) {
		    $tags = @id3_get_tag( $filename, ID3_V2_1);
		}
		// se non ritorna nulla allora cerca di recuperare  
		// i TAG per la versione 1.1
		if (empty($tags) && ($version & ID3_V1_1)) {
			$tags = @id3_get_tag( $filename, ID3_V1_1);
		}
		// se non ritorna nulla allora cerca di recuperare  
		// i TAG per la versione 1.0
		if (empty($tags) && ($version & ID3_V1_0)) {
			$tags = @id3_get_tag( $filename, ID3_V1_0);
		}
		
		// Il recupero dei TAG con la versione ID3_V2_2  
		// e' messa alla fine perche' ho notato che se 
		// parte per primo va in FATAL ERROR in molti casi in cui il TAG e' 
		// di tipo ID3_V1_x.
		// Pertanto cerco di recuperare il valore dei TAG con questa versione 
		// solo se realmente non riesce a recuperare con le altre versioni
		
		// verifica se la versione del ID3 e' la 2.2
		if (empty($tags) && ($version & ID3_V2_2)) {
		    $tags = @id3_get_tag( $filename, ID3_V2_2);
		}
		
		if (empty($tags)) {return false;}
				
		// definsco in array
		$this->textTag = array();
		
		foreach ($tags as $key => $tag) {
			switch ($key) {
				// Gruppo
				case 'band':
					$this->setBand( $this->clear($tag) );
					$this->textTag[$key] = $this->getBand();
					break;
				// copyright
				case 'copyright':
					$this->setCopyright( $this->clear($tag) );
					$this->textTag[$key] = $this->getCopyright();
					break;
				// artista originale
				case 'originalArtist':
					$this->setOriginalArtist( $this->clear($tag) );
					$this->textTag[$key] = $this->getOriginalArtist();
					break;
				case 'bpm':
					$this->setBpm( $this->clear($tag, true) );
					$this->textTag[$key] = $this->getBpm();
					break;
				// disco
				case 'partOfASet':
					$this->setPartOfASet( $this->clear($tag) );
					$this->textTag[$key] = $this->getPartOfASet();
					break;
				// Editore
				case 'publisher':
					$this->setPublisher( $this->clear($tag) );
					$this->textTag[$key] = $this->getPublisher();
					break;
				// Compositore
				case 'composer':
					$this->setComposer( $this->clear($tag) );
					$this->textTag[$key] = $this->getComposer();
					break;
				// Album
				case 'album':
					$this->setAlbum ( $this->clear($tag) );
					$this->textTag[$key] = $this->getAlbum();
					break;
				// Artista
				case 'artist':
					$this->setArtist( $this->clear($tag) );
					$this->textTag[$key] = $this->getArtist();
					break;
				// Titolo
				case 'title':
					$this->setTitle ( $this->clear($tag) );
					$this->textTag[$key] = $this->getTitle();
					break;
				// Anno
				case 'year':
					$this->setYear  ( $this->clear($tag, true) );
					$this->textTag[$key] = $this->getYear();
					break;
				// Genere
				case 'genre':
					$this->setGenre  ( $this->clear($tag, true) );
					$this->textTag[$key] = $this->getGenre();
					break;
				// Traccia
				case 'track':
					$this->setTrack  ( $this->clear($tag, true) );
					$this->textTag[$key] = $this->getTrack();
					break;
				// Codificato da
				case 'encodedBy':
					$this->setEncodedBy  ( $this->clear($tag) );
					$this->textTag[$key] = $this->getEncodedBy();
					break;
			}			
		}
		
		return true;
	}
	
	/**
	 * Verifia se la proprieta' non e' vuota
	 * 
	 * @param string $property
	 * @return bool
	 */
	public function issetNotEmpty($property) {
		$isNotEmpty = false;
		
		if (isset($this->$property)) {
			switch ($property) {
				// per il genre verifica se e' presente nella lista dei generi di default
				case 'genre':
					if (trim($this->getGenre()) !== '') $isNotEmpty = true;
					break;
				default:
				if (trim($this->$property) !== '') $isNotEmpty = true;
			}	
		}	
		
		return $isNotEmpty;
	}
	
	/**
	 * Ritorna una stringa recuperata dal TAG del file MP3
	 * 
	 * @param stringa $str
	 * @return string
	 */
	private function clear($str, $number = false) {
		
		$alphanum = "/[^A-Za-z0-9 ]/";
		
		if ($number) {
			$alphanum = "/[^0-9]/";	
		}
		
		$result = preg_replace($alphanum, '', $str);
		
		return rtrim($result);
	}
}
?>