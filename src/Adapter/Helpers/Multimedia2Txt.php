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
 * @license
 * @version    1.0 class.multimedia2txt.php 2011-01-12 12:19:34
 */

/** @see getID3 */
require_once ("getid3/getid3.php");
/**
 * Recupera le informazioni di un file multimediali utilizzando la libreria getID3 
 * 
 * @category   IndexingFile
 * @package    ifile
 * @subpackage adapter/helpers
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright  
 * @license
 * @link http://www.getid3.org
 * 
 */
class Multimedia2Txt {
			
	/**
	 * Tutto il contenuto del TAG
	 * 
	 * @var array
	 */
	private $textTag = array();
	/**
	 * Encoding del file multimediale
	 * 
	 * @var string
	 */
	private $encoding;
	/**
	 * File multimediale da analizzare
	 * 
	 * @var string
	 */
	private $filename;
	/**
	 * Metadati del file multimediale
	 * 
	 * @var array
	 */
	private $metadata;
	/**
	 * Istanza classe getID3
	 * 
	 * @var getID3
	 */
	private $getID3;
	
	/** MULTIMEDIA TAGS */
	
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
	 * Commento 
	 * @var integer 
	 */
	private $comment;
	/**
	 * URL
	 * @var string
	 */
	private $urlUser;
	/**
	 * Testo della canzone
	 * @var string
	 */
	private $unsynchronisedLyric;
	/**
	 * Numero del disco
	 * @var string
	 */
	private $discNumber;
	/**
	 * Numero della compilation
	 * @var integer
	 */
	private $compilation;
	/**
	 * Valutazione
	 * @var string
	 */
	private $rating;
	/**
	 * Stick
	 * @var string
	 */
	private $stik;
	/**
	 * Encoder
	 * @var string
	 */
	private $encoder;
	/**
	 * Tempo di esecuzione in formato stringa
	 * @var string
	 */
	private $playTimeString;
	/**
	 * Tempo di esecuzione in secondi e millisecondi
	 * @var float
	 */
	private $playTimeSecond;
	/**
	 * Bitrate
	 * @var float
	 */
	private $bitrate;
	/**
	 * Risoluzione Video X
	 * @var integer
	 */
	private $XResolution;
	/**
	 * Risoluzione Video Y
	 * @var integer
	 */
	private $YResolution;
	/**
	 * Formato Video
	 * @var integer
	 */
	private $videoFormat;
	/**
	 * Frame rate
	 * @var float
	 */
	private $frameRate;
	/**
	 * Fourcc
	 * @var string
	 */
	private $fourcc;
	/**
	 * Video Encoder
	 * @var string
	 */
	private $videoEncoder;
	/**
	 * Formato Audio
	 * @var string
	 */
	private $audioFormat;
	/**
	 * Codec Audio
	 * @var string
	 */
	private $codecAudio;
	/**
	 * Canale Audio
	 * @var string
	 */
	private $audioChannelMode;
	/**
	 * Canali Audio
	 * @var float
	 */
	private $audioChannels;
	/**
	 * Frequenza di campionamento
	 * @var integer
	 */
	private $sampleRate;
	/**
	 * Audio Encoder
	 * @var string
	 */
	private $audioEncoder;
	/**
	 * Opzioni Audio Encoder
	 * @var string
	 */
	private $audioEncoderOptions;
	
	/** IMAGES TAGS */
	
	/**
	 * Dimensione dfel file in byte
	 *
	 * @var integer
	 */
	private $fileSize;
	/**
	 * Altezza in pixel dell'immagine
	 *
	 * @var integer
	 */
	private $height;
	/**
	 * Larghezza in pixel dell'immagine
	 *
	 * @var integer
	 */
	private $width;
	/**
	 * Immagini a colori
	 *
	 * @var integer
	 */
	private $isColor;
	/**
	 * Apertura dell'obiettivo
	 *
	 * @var string
	 */
	private $apertureFNumber;	
	/**
	 * Commenti
	 *
	 * @var string
	 */
	private $userComment;
	/**
	 * Descrizione dell'immagine
	 *
	 * @var string
	 */
	private $imageDescription;
	/**
	 * Orientamento dell'immagine
	 *
	 * @var integer
	 */
	private $orientation;
	
	/** MULTIMEDIA TAGS */
	
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
	 * Setta l'anno della canzone 
	 * 
	 * @param integer $year
	 * @return void
	 */
	public function setYear($year) { $this->year = $year; }
	/** 
	 * Setta il commento
	 * 
	 * @param integer $comment
	 * @return void
	 */
	public function setComment($comment) { $this->comment = $comment; }
	/** 
	 * Setta URL 
	 * 
	 * @param string $unsynchronisedLyric
	 * @return void
	 */
	public function setUrlUser($urlUser) { $this->urlUser = $urlUser; }
	/** 
	 * Setta Testo della canzone 
	 * 
	 * @param string $unsynchronisedLyric
	 * @return void
	 */
	public function setUnsynchronisedLyric($unsynchronisedLyric) { $this->unsynchronisedLyric = $unsynchronisedLyric; }
	/** 
	 * Setta il numero del disco 
	 * 
	 * @param string $discNumber
	 * @return void
	 */
	public function setDiscNumber($discNumber) { $this->discNumber = $discNumber; }
	/** 
	 * Setta il numero della compilation 
	 * 
	 * @param integer $compilation
	 * @return void
	 */
	public function setCompilation($compilation) { $this->compilation = $compilation; }
	/** 
	 * Setta la valutazione 
	 * 
	 * @param string $rating
	 * @return void
	 */
	public function setRating($rating) { $this->rating = $rating; }
	/** 
	 * Setta lo stick
	 * 
	 * @param string $stik
	 * @return void
	 */
	public function setStik($stik) { $this->stik = $stik; }
	/** 
	 * Setta l'encoder
	 * 
	 * @param string $encoder
	 * @return void
	 */
	public function setEncoder($encoder) { $this->encoder = $encoder; }
	
	/* Informazioni generali del file multimediale */
	/** 
	 * Setta il tempo di esecuzione come stringa 
	 * 
	 * @param string $playTimeString
	 * @return void
	 */
	public function setPlayTimeString($playTimeString) { $this->playTimeString = $playTimeString; }
	/** 
	 * Setta il tempo di esecuzione in secondi e microsecondi 
	 * 
	 * @param float $playTimeSecond
	 * @return void
	 */
	public function setPlayTimeSecond($playTimeSecond) { $this->playTimeSecond = $playTimeSecond; }
	/** 
	 * Setta il bitrate 
	 * 
	 * @param float $playTimeSecond
	 * @return void
	 */
	public function setBitrate($bitrate) { $this->bitrate = $bitrate; }
	/** 
	 * Setta il Risoluzione Video X 
	 * 
	 * @param integer $Xresolution
	 * @return void
	 */
	public function setXResolution($Xresolution) { $this->XResolution = $Xresolution; }
	/** 
	 * Setta il Risoluzione Video Y 
	 * 
	 * @param integer $Yresolution
	 * @return void
	 */
	public function setYResolution($Yresolution) { $this->YResolution = $Yresolution; }
	/** 
	 * Setta il Formato Video 
	 * 
	 * @param integer $videoFormat
	 * @return void
	 */
	public function setVideoFormat($videoFormat) { $this->videoFormat = $videoFormat; }
	/** 
	 * Setta il Frame rate 
	 * 
	 * @param float $frameRate
	 * @return void
	 */
	public function setFrameRate($frameRate) { $this->frameRate = $frameRate; }
	/** 
	 * Setta il Fourcc 
	 * 
	 * @param string $fourcc
	 * @return void
	 */
	public function setFourcc($fourcc) { $this->fourcc = $fourcc; }
	/** 
	 * Setta il Video Encoder 
	 * 
	 * @param string $videoEncoder
	 * @return void
	 */
	public function setVideoEncoder($videoEncoder) { $this->videoEncoder = $videoEncoder; }
	/** 
	 * Setta il Formato Audio 
	 * 
	 * @param string $audioFormat
	 * @return void
	 */
	public function setAudioFormat($audioFormat) { $this->audioFormat = $audioFormat; }
	/** 
	 * Setta il Codec Audio 
	 * 
	 * @param string $codecAudio
	 * @return void
	 */
	public function setCodecAudio($codecAudio) { $this->codecAudio = $codecAudio; }
	/** 
	 * Setta il tipo di Canale Audio 
	 * 
	 * @param string $audioChannelMode
	 * @return void
	 */
	public function setAudioChannelMode($audioChannelMode) { $this->audioChannelMode = $audioChannelMode; }
	/** 
	 * Setta i Canali Audio 
	 * 
	 * @param float $audioChannels
	 * @return void
	 */
	public function setAudioChannels($audioChannels) { $this->audioChannels = $audioChannels; }
	/** 
	 * Setta i Frequenza di campionamento 
	 * 
	 * @param float $sampleRate
	 * @return void
	 */
	public function setSampleRate($sampleRate) { $this->sampleRate = $sampleRate; }
	/** 
	 * Setta l'audio Encoder 
	 * 
	 * @param string $audioEncoder
	 * @return void
	 */
	public function setAudioEncoder($audioEncoder) { $this->audioEncoder = $audioEncoder; }
	/** 
	 * Setta le opzioni dell'encoder audio  
	 * 
	 * @param string $audioEncoderOptions
	 * @return void
	 */
	public function setAudioEncoderOptions($audioEncoderOptions) { $this->audioEncoderOptions = $audioEncoderOptions; }
	
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
	public function getGenre() { return  $this->genre; }
	/** 
	 * Ritorna l'anno della canzone 
	 * 
	 * @return integer
	 */
	public function getYear() { return $this->year; }
	/** 
	 * Ritorna il commento 
	 * 
	 * @return string
	 */
	public function getComment() { return $this->comment; }
	/** 
	 * Ritorna URL 
	 * 
	 * @return string
	 */
	public function getUrlUser() { return $this->urlUser; }
	/** 
	 * Ritorna il testo della canzone 
	 * 
	 * @return string
	 */
	public function getUnsynchronisedLyric() { return $this->unsynchronisedLyric; }
	/** 
	 * Ritorna numero del disco 
	 * 
	 * @return integer
	 */
	public function getDiscNumber() { return $this->discNumber; }
	/** 
	 * Ritorna il numero della compilation 
	 * 
	 * @return integer
	 */
	public function getCompilation() { return $this->compilation; }
	/** 
	 * Ritorna la valutazione 
	 * 
	 * @return string
	 */
	public function getRating() { return $this->rating; }
	/** 
	 * Ritorna lo stick 
	 * 
	 * @return string
	 */
	public function getStik() { return $this->stik; }
	/** 
	 * Ritorna l'encoder 
	 * 
	 * @return string
	 */
	public function getEncoder() { return $this->encoder; }
	
	/* Informazioni generali del file multimediale */
	/** 
	 * Ritorna il tempo di esecuzione come stringa 
	 * 
	 * @return string
	 */
	public function getPlayTimeString() { return $this->playTimeString; }
	/** 
	 * Ritorna il tempo di esecuzione in secondi e millisecondi 
	 * 
	 * @return float
	 */
	public function getPlayTimeSecond() { return $this->playTimeSecond; }
	/** 
	 * Ritorna il bitrate 
	 * 
	 * @return float
	 */
	public function getBitrate() { return $this->bitrate; }	
	/** 
	 * Ritorna il Risoluzione Video X 
	 * 
	 * @return integer
	 */
	public function getXResolution() { return $this->XResolution; }
	/** 
	 * Ritorna il Risoluzione Video Y 
	 * 
	 * @return integer
	 */
	public function getYResolution() { return $this->YResolution; }
	/** 
	 * Ritorna il Formato Video 
	 * 
	 * @return string
	 */
	public function getVideoFormat() { return $this->videoFormat; }
	/** 
	 * Ritorna il Frame Rate 
	 * 
	 * @return float
	 */
	public function getFrameRate() { return $this->frameRate; }
	/** 
	 * Ritorna il four character code (fourcc) 
	 * 
	 * @return string
	 */
	public function getFourcc() { return $this->fourcc; }
	/** 
	 * Ritorna il Video Encoder 
	 * 
	 * @return string
	 */
	public function getVideoEncoder() { return $this->videoEncoder; }
	/** 
	 * Ritorna il formato Audio 
	 * 
	 * @return string
	 */
	public function getAudioFormat() { return $this->audioFormat; }
	/** 
	 * Ritorna il Codec Audio 
	 * 
	 * @return string
	 */
	public function getCodecAudio() { return $this->codecAudio; }
	/** 
	 * Ritorna il tipo di Canale Audio 
	 * 
	 * @return string
	 */
	public function getAudioChannelMode() { return $this->audioChannelMode; }
	/** 
	 * Ritorna Canali Audio 
	 * 
	 * @return float
	 */
	public function getAudioChannels() { return $this->audioChannels; }
	/** 
	 * Ritorna Frequenza campionamento 
	 * 
	 * @return integer
	 */
	public function getSampleRate() { return $this->sampleRate; }
	/** 
	 * Ritorna Decodifica Audio 
	 * 
	 * @return string
	 */
	public function getAudioEncoder() { return $this->audioEncoder; }
	/** 
	 * Ritorna le opzioni di Decodifica Audio 
	 * 
	 * @return string
	 */
	public function getAudioEncoderOptions() { return $this->audioEncoderOptions; }
	
	/** IMAGES TAGS */
	
	/**
	 * Setta la dimensione del file in byte
	 *
	 * @param integer $fileSize
	 * @return void
	 */
	public function setFileSize($filesize) { $this->fileSize = $filesize; }
	
	/**
	 * Setta l'altezza dell'immagine
	 *
	 * @param integer $height
	 * @return void
	 */
	public function setHeight($height) { $this->height = $height; }
	
	/**
	 * Setta la larghezza dell'immagine
	 *
	 * @param integer $width
	 * @return void
	 */
	public function setWidth($width) { $this->width = $width; }
	
	/**
	 * Setta se l'immagine e' a colori
	 *
	 * @param mixed $isColor
	 * @return void
	 */
	public function setIsColor($isColor) { $this->isColor = $isColor; }
	
	/**
	 * Setta l'apertura dell'obiettivo
	 *
	 * @param string $apertureFNumber
	 * @return void
	 */
	public function setApertureFNumber($apertureFNumber) { $this->apertureFNumber = $apertureFNumber; }
	
	/**
	 * Setta il commento dell'utente
	 *
	 * @param string $userComment
	 * @return void
	 */
	public function setUserComment($userComment) { $this->userComment = $userComment; }
	
	/**
	 * Setta la descrizione dell'immagine
	 *
	 * @param string $imageDescription
	 * @return void
	 */
	public function setImageDescription($imageDescription) { $this->imageDescription = $imageDescription; }
	
	/**
	 * Setta l'orientamento della pagina
	 *
	 * @param integer $orientation
	 * @return void
	 */
	public function setOrientation($orientation) { $this->orientation = $orientation; }
	
	/**
	 * Setta la macchina utilizzata per la foto
	 *
	 * @param string $make
	 * @return void
	 */
	public function setMake($make) { $this->make = $make; }
	
	/**
	 * Setta il modello della macchina utilizzata per l'immagine
	 *
	 * @param string $model
	 * @return void
	 */
	public function setModel($model) { $this->model = $model; }
	
	/**
	 * Setta il software che ha creato l'immagine
	 *
	 * @param string $software
	 * @return void
	 */
	public function setSoftware($software) { $this->software = $software; }
	
	/**
	 * Setta la data di creazione dell'immagine nel formato yyyy:mm:gg hh:mm:ss
	 *
	 * @param string $dataTime
	 * @return void
	 */
	public function setDateTime($dateTime) { $this->dateTime = $dateTime; }
	
	/**
	 * Setta la modalita' di esposizione
	 *
	 * @param string $exposureMode
	 * @return void
	 */
	public function setExposureMode($exposureMode) { $this->exposureMode = $exposureMode; }
	
	/**
	 * Setta il tempo di esposizione
	 *
	 * @param string $exposureTime
	 * @return void
	 */
	public function setExposureTime($exposureTime) { $this->exposureTime = $exposureTime; }
	
	
	
	
	/**
	 * Setta la risorsa di luce
	 *
	 * @param string $lightSource
	 * @return void
	 */
	public function setLightSource($lightSource) { $this->lightSource = $lightSource; }
	/**
	 * Setta la scena di cattura
	 *
	 * @param string $sceneCaptureType
	 * @return void
	 */
	public function setSceneCaptureType($sceneCaptureType) { $this->sceneCaptureType = $sceneCaptureType; }
	
	/**
	 * Setta il punto cardinale della longitudine
	 *
	 * @param string $GPSLatitudeRef
	 * @return void
	 */
	public function setGPSLongitudeRef($GPSLongitudeRef) { $this->GPSLongitudeRef = $GPSLongitudeRef; }
	
	/**
	 * Setta le coordinate della longitudine
	 *
	 * @param string $GPSLatitude
	 * @return void
	 */
	public function setGPSLongitude($GPSLongitude) { $this->GPSLongitude = $GPSLongitude; }
	
	/**
	 * Setta il punto cardinale della latitudine
	 *
	 * @param string $GPSLatitudeRef
	 * @return void
	 */
	public function setGPSLatitudeRef($GPSLatitudeRef) { $this->GPSLatitudeRef = $GPSLatitudeRef; }
	
	/**
	 * Setta le coordinate della latitudine
	 *
	 * @param string $GPSLatitude
	 * @return void
	 */
	public function setGPSLatitude($GPSLatitude) { $this->GPSLatitude = $GPSLatitude; }
	
	/**
	 * Ritorna la dimensione del file in byte
	 *
	 * @return integer
	 */
	public function getFileSize() { return $this->fileSize;}
	
	/**
	 * Ritorna l'altezza dell'immagine
	 *
	 * @return integer
	 */
	public function getHeight() { return $this->height; }
	
	/**
	 * Ritorna la larghezza dell'immagine
	 *
	 * @return integer
	 */
	public function getWidth() { return $this->width; }
	
	/**
	 * Ritorna se l'immagine e' a colori
	 *
	 * @return integer
	 */
	public function getIsColor() {
		if ($this->isColor == '1') {
			return '1';
		}
	
		return '0';
	}
	
	/**
	 * Ritorna l'apertura dell'obiettivo
	 *
	 * @return string
	 */
	public function getApertureFNumber() { return $this->apertureFNumber; }

	/**
	 * Ritorna il commento dell'utente
	 *
	 * @return string
	 */
	public function getUserComment() { return $this->userComment; }
	
	/**
	 * Ritorna la descrizione dell'immagine
	 *
	 * @return string
	 */
	public function getImageDescription() { return $this->imageDescription; }
		
	/**
	 * Ritorna l'orientamento dell'immagine
	 *
	 * @return string
	 */
	public function getOrientation() { return $this->orientation; }
	
	/**
	 * Ritorna la decodifica testuale dell'orientamento dell'immagine
	 *
	 * @return string
	 */
	public function getOrientationDescription() {
		$orientation = '';
		
		
		switch ($this->orientation) {
			case '1':
				$orientation = 'horizontal';
				break;
			case '2':
				$orientation = 'mirror horizontal';
				break;
			case '3':
				$orientation = 'rotate 180';
				break;
			case '4':
				$orientation = 'mirror horizontal and rotate 270 cw';
				break;
			case '5':
				$orientation = 'rotate 90 cw';
				break;
			case '6':
				$orientation = 'mirror horizontal and rotate 90 cw';
				break;
			case '7':
				$orientation = 'rotate 270 cw';
				break;
			case '8':
				$orientation = 'left side bottom';
				break;
			default:
				$orientation = 'reserved';
				break;
		}
	
		return $orientation;
	}
	
	/**
	 * Ritorna la macchina utilizzata per la foto
	 *
	 * @return string
	 */
	public function getMake() { return $this->make; }
	
	/**
	 * Ritorna il modello della macchina utilizzata per l'immagine
	 *
	 * @return string
	 */
	public function getModel() { return $this->model; }
	
	/**
	 * Ritorna il software che ha creato l'immagine
	 *
	 * @return string
	 */
	public function getSoftware() { return $this->software; }
	
	/**
	 * Ritorna la data di creazione dell'immagine nel formato yyyy:mm:gg hh:mm:ss
	 *
	 * @return string
	 */
	public function getDateTime() { return $this->dateTime; }
	
	/**
	 * Riotrna la modalita' di esposizione
	 *
	 * @return string
	 */
	public function getExposureMode() { return $this->exposureMode; }
	
	/**
	 * Riotrna la descrizione della modalita' di esposizione
	 *
	 * @return string
	 */
	public function getExposureModeDescription() {
		$exposureMode = '';
	
		switch ($this->exposureMode) {
			case '0':
				$exposureMode = 'Auto';
				break;
			case '1':
				$exposureMode = 'Manual';
				break;
			case '2':
				$exposureMode = 'Auto bracket';
				break;
			case 'none':
				$exposureMode = 'None';
				break;
			default:
				$exposureMode = 'reserved';
				break;
		}
	
		return $exposureMode;
	}
	
	/**
	 * Ritorna il tempo di esposizione
	 *
	 * @return string
	 */
	public function getExposureTime() { return $this->exposureTime; }
	
	/**
	 * Ritorna la scena di cattura
	 *
	 * @return string
	 */
	public function getSceneCaptureType() { return $this->sceneCaptureType; }
	
	/**
	 * Ritorna la scena di cattura
	 *
	 * @return string
	 */
	public function getSceneCaptureTypeDescription() {
		$sceneCaptureType = '';
	
		switch ($this->sceneCaptureType) {
			case '0':
				$sceneCaptureType = 'Standard';
				break;
			case '1':
				$sceneCaptureType = 'Landscape';
				break;
			case '2':
				$sceneCaptureType = 'Portrait';
				break;
			case '3':
				$sceneCaptureType = 'Night scene';
				break;
			default:
				$sceneCaptureType = 'reserved';
				break;
		}
	
		return $sceneCaptureType;
	}
	
	/**
	 * Ritorna la risorsa di luce
	 *
	 * @return string
	 */
	public function getLightSource() { return $this->lightSource; }
	
	/**
	 * Ritorna la descizione della risorsa di luce
	 *
	 * @return string
	 */
	public function getLightSourceDescription() {
		$lightSource = '';
	
		switch ($this->lightSource) {
			case '0':
				$lightSource = 'unknown';
				break;
			case '1':
				$lightSource = 'Daylight';
				break;
			case '2':
				$lightSource = 'Fluorescent';
				break;
			case '3':
				$lightSource = 'Tungsten (incandescent light)';
				break;
			case '4':
				$lightSource = 'Flash';
				break;
			case '9':
				$lightSource = 'Fine weather';
				break;
			case '10':
				$lightSource = 'Cloudy weather';
				break;
			case '11':
				$lightSource = 'Shade';
				break;
			case '12':
				$lightSource = 'Daylight fluorescent (D 5700 – 7100K)';
				break;
			case '13':
				$lightSource = 'Day white fluorescent (N 4600 – 5400K)';
				break;
			case '14':
				$lightSource = 'Cool white fluorescent (W 3900 – 4500K)';
				break;
			case '15':
				$lightSource = 'White fluorescent (WW 3200 – 3700K)';
				break;
			case '17':
				$lightSource = 'Standard light A';
				break;
			case '18':
				$lightSource = 'Standard light B';
				break;
			case '19':
				$lightSource = 'Standard light C';
				break;
			case '20':
				$lightSource = 'D55';
				break;
			case '21':
				$lightSource = 'D65';
				break;
			case '22':
				$lightSource = 'D75';
				break;
			case '23':
				$lightSource = 'D50';
				break;
			case '24':
				$lightSource = 'ISO studio tungsten';
				break;
			case '255':
				$lightSource = 'Other light source';
				break;
			default:
				$lightSource = 'reserved';
				break;
		}
	
		return $lightSource;
	}
	
	/**
	 * Ritorna il punto cardinale della longitudine
	 *
	 * @return string
	 */
	public function getGPSLongitudeRef() { return $this->GPSLongitudeRef;	}
	
	/**
	 * Ritorna la longitudine
	 *
	 * @param boolean $toString
	 * @return string
	 */
	public function getGPSLongitude($toString = true) {
		if ($toString) {
			return implode('@@', $this->GPSLongitude);
		} else {
			return $this->GPSLongitude;
		}
	}
	
	/**
	 * Ritorna la latitudine nel formato di googlemap
	 *
	 * @return string
	 */
	public function getGPSLongitudeGoogle() { return $this->getGps($this->getGPSLongitude(false), $this->getGPSLongitudeRef()); }
	
	/**
	 * Ritorna il punto cardinale della latitudine
	 *
	 * @return string
	 */
	public function getGPSLatitudeRef() { return $this->GPSLatitudeRef;	}
	
	/**
	 * Ritorna la latitudine
	 *
	 * @param boolean $toString
	 * @return string
	 */
	public function getGPSLatitude($toString = true) {
		if ($toString) {
			return implode('@@', $this->GPSLatitude);
		} else {
			return $this->GPSLatitude;
		}
			
	}
	
	/**
	 * Ritorna la latitudine nel formato di googlemap
	 *
	 * @return string
	 */
	public function getGPSLatitudeGoogle() { return $this->getGps($this->getGPSLatitude(false), $this->getGPSLatitudeRef()); }
	
	/** COMMON TAGS */
	
	/**
	 * Setta il copyright della canzone
	 *
	 * @param string $copyright
	 * @return void
	 */
	public function setCopyright($copyright) { $this->copyright = $copyright; }
	
	/**
	 * Setta il contenuto del TAG in formato testuale
	 *
	 * @param integer $textTag
	 * @return void
	 */
	public function setTextTag($textTag) { $this->textTag = $textTag; }
	
	/**
	 * Ritorna il copyright della canzone
	 *
	 * @return string
	 */
	public function getCopyright() { return $this->copyright; }
	
	/** 
	 * Ritorna il contenuto del TAG in formato testuale 
	 * 
	 * @return string
	 */
	public function getTextTag() {
		$text = "";
		foreach ($this->textTag as $key => $value) {
			$text .= $key.": ".$value."\n"; 
		}
		
		return $text;
	}
	
	/**
	 * Definisce l'encoding del file multimediale
	 * 
	 * @param string $filename 
	 * @param string $encoding [optional]
	 * @return void
	 */
	public function __construct($filename, $encoding = 'UTF-8') {
		
		// @TODO
		// dopo aver implementato il controllo dell'esistenza della libreria
		// getID3, devo fare il controllo in questo punto recuperandola 
		// dalla checkReport (vedi come x PDF)
		
		$this->filename = $filename;
		$this->encoding = (empty($encoding)) ? 'UTF-8' : $encoding;	
		// istanzia la getID3
		$this->getID3 = new getID3();
		// setta l'encoding
		$this->getID3->setOption(array('encoding' => $this->encoding));	
		// analizza il documento
		$this->metadata = $this->getID3->analyze($this->filename);	
		// setta alcune informazioni sui metadati
		$this->setAllInfo();	 
	}
	
	/**
	 * Analizza il documento multimediale in funzione del tipo di TAG
	 * 
	 * @param string $type
	 * @return void
	 */
	public function parseTag($type) {
		
		$type = strtolower($type);
		
		switch ($type) {
			case "id3":
				$result = $this->parseTagID3();
				break;
			case "quicktime":
				$result = $this->parseTagQuickTime();
				break;
			case "asf":
				$result = $this->parseTagAsf();
				break;			
			case "vorbis":
				$result = $this->parseTagVorbisComment();
				break;		
			case "riff":
				$result = $this->parseTagRiff();
				break;
			case "midi":
				$result = $this->parseTagMidi();
				break;			
			case "jpg":
				$result = $this->parseTagJpg();
				break;			
			case "tiff":
			case "png":
			case "svg":
				$result = $this->parseTagDefault($type);
				break;			
			default:
				require_once 'Helper_Exception.php';
				throw new Helper_Exception("Type of metadata not correct");
		}
		
		return $result;
	}
	
	/**
	 * Analizza il documento Multimediale cercando nei possibili TAGS
	 *  
	 * @return boolean
	 */
	public function parseMultimediaTag() {
		// TAG ID3
		$result = $this->parseTagID3();
		// TAG QuickTime 
		if (!$result) {$result = $this->parseTagQuickTime();}
		// TAG ASF
		if (!$result) {$result = $this->parseTagAsf();} 
		// TAG VorbisComment
		if (!$result) {$result = $this->parseTagVorbisComment();} 
		// TAG RIFF
		if (!$result) {$result = $this->parseTagRiff();} 
		// TAG Midi
		if (!$result) {$result = $this->parseTagMidi();} 
		
		return $result;
	}
	
	/**
	 * Ritorna True se riesce a parserizzare il file
	 *
	 * Recupera dal file le informazioni Midi se queste sono presenti.
	 * Questo metodo utilizza la libreria getID3.
	 * http://www.getid3.org
	 *
	 * @return bool
	 */
	private function parseTagDefault($type){
	
		$tags = array();
		// setta i tags
		$this->setAllInfo($tags);
			
		return true;
	}
	
	/**
	 * Ritorna True se riesce a parserizzare il file
	 *
	 * Recupera dal file le informazioni Midi se queste sono presenti.
	 * Questo metodo utilizza la libreria getID3.
	 * http://www.getid3.org
	 *
	 * @return bool
	 */
	private function parseTagJpg(){
		
		$tags = array();
		// recupera i tag riff
		if (isset($this->metadata['jpg']['exif'])) {
			$tags['type'] = 'jpg';
			$tags['value'] =& $this->metadata['jpg']['exif'];
		}
		// se non presenta informazioni esce
		if (empty($tags)) {return false;}
		
		// setta i tags
		$this->setImageAllTags($tags['value']);
			
		return true;
	}
	
	/**
	 * Setta i tag per l'indicizzazione.
	 * Questo metodo è indipendente dal tipo di file multimediale
	 *
	 * @param array $tags
	 * @return
	 */
	private function setImageAllTags ($tags) {
	
		foreach ($tags as $key => $tag) {
			// @TODO sembra che non vada bene per tutte le tipologie di immagini
			switch ($key) {
				// FILE
				case 'FILE':					 
					$this->setIamgeFileTags( $tag );
					break;
				// COMPUTED
				case 'COMPUTED':					 
					$this->setImageComputedTags( $tag );
					break;
				// IFDO
				case 'IFD0':					 
					$this->setImageIFD0Tags( $tag );
					break;
				// EXIF
				case 'EXIF':					 
					$this->setImageExifTags( $tag );
					break;
				// GPS
				case 'GPS':					 
					$this->setImageGPSTags( $tag );
					break;
				// default 
				default:					 
					$this->setImageDefautlTags( $tag );
					break;
			}
		}
	}
	
	/**
	 * Setta i metadati per il TAG EXIF.
	 * Ritorna false se il tag e' vuoto
	 *
	 * @param array $tags
	 * @return bool
	 */
	private function setImageGPSTags($tags) {
	
		foreach ($tags as $key => $tag) {
			switch ($key) {
				// GPSLatitude
				case 'GPSLatitude':
					$this->setGPSLatitude( $tag );
					$this->textTag[$key] = $this->getGPSLatitude();
					break;
				// GPSLatitudeRef
				case 'GPSLatitudeRef':
					$this->setGPSLatitudeRef( $tag );
					$this->textTag[$key] = $this->getGPSLatitudeRef();
					break;
				// GPSLongitude
				case 'GPSLongitude':
					$this->setGPSLongitude( $tag );
					$this->textTag[$key] = $this->getGPSLongitude();
					break;
				// GPSLongitudeRef
				case 'GPSLongitudeRef':
					$this->setGPSLongitudeRef( $tag );
					$this->textTag[$key] = $this->getGPSLongitudeRef();
					break;
			}
		}
	
		return true;
	}
	
	/**
	 * Setta i metadati per il TAG EXIF.
	 * Ritorna false se il tag e' vuoto
	 *
	 * @param array $tags
	 * @return bool
	 */
	private function setImageExifTags($tags) {
	
		foreach ($tags as $key => $tag) {
			switch ($key) {
				// ExposureMode
				case 'ExposureMode':
					$this->setExposureMode( $tag );
					$this->textTag[$key] = $this->getExposureModeDescription();
					break;					
				// ExposureTime
				case 'ExposureTime':
					$this->setExposureTime( $tag );
					$this->textTag[$key] = $this->getExposureTime();
					break;					
				// SceneCaptureType
				case 'SceneCaptureType':
					$this->setSceneCaptureType( $tag );
					$this->textTag[$key] = $this->getSceneCaptureTypeDescription();
					break;					
				// SceneCaptureType
				case 'LightSource':
					$this->setLightSource( $tag );
					$this->textTag[$key] = $this->getLightSourceDescription();
					break;					
			}
		}
	
		return true;
	}
	
	/**
	 * Setta i metadati per il TAG IFDO.
	 * Ritorna false se il tag e' vuoto
	 * 
	 * @param array $tags
	 * @return bool
	 */
	private function setImageIFD0Tags($tags) {
		
		foreach ($tags as $key => $tag) {
			switch ($key) {
				// Orientation
				case 'Orientation':
					$this->setOrientation( $tag );
					$this->textTag[$key] = $this->getOrientationDescription();
					break;			
				// Make
				case 'Make':
					$this->setMake( $tag );
					$this->textTag[$key] = $this->getMake();
					break;			
				// Model
				case 'Model':
					$this->setModel( $tag );
					$this->textTag[$key] = $this->getModel();
					break;			
				// Software
				case 'Software':
					$this->setSoftware( $tag );
					$this->textTag[$key] = $this->getSoftware();
					break;
				// DateTime
				case 'DateTime':
					$this->setDateTime( $tag );
					$this->textTag[$key] = $this->getDateTime();
					break;
				// ImageDescription
				case 'ImageDescription':
					$this->setImageDescription( $tag );
					$this->textTag[$key] = $this->getImageDescription();
					break;
			}
		}
		
		return true;
	}
	
	/**
	 * Setta i metadati per il TAG FILE.
	 * Ritorna false se il tag e' vuoto
	 * 
	 * @param array $tags
	 * @return bool
	 */
	private function setIamgeFileTags($tags) {
		
		foreach ($tags as $key => $tag) {
			switch ($key) {
				// FileSize
				case 'FileSize':
					$this->setFileSize( $tag );
					$this->textTag[$key] = $this->getFileSize();
					break;			
			}
		}
		
		return true;
	}
	
	/**
	 * Setta i metadati per il TAG COMPUTED.
	 * Ritorna false se il tag e' vuoto
	 * 
	 * @param array $tags
	 * @return bool
	 */
	private function setImageComputedTags($tags) {

		foreach ($tags as $key => $tag) {
			switch ($key) {
				// Height
				case 'Height':
					$this->setHeight( $tag );
					$this->textTag[$key] = $this->getHeight();
					break;
				// Width
				case 'Width':
					$this->setWidth( $tag );
					$this->textTag[$key] = $this->getWidth();
					break;
				// IsColor
				case 'IsColor':
					$this->setIsColor( $tag );
					$this->textTag[$key] = $this->getIsColor();
					break;
				// ApertureFNumber
				case 'ApertureFNumber':
					$this->setApertureFNumber( $tag );
					$this->textTag[$key] = $this->getApertureFNumber();
					break;
				// UserComment
				case 'UserComment':
					$this->setUserComment( $tag );
					$this->textTag[$key] = $this->getUserComment();
					break;
				// Copyright
				case 'Copyright':
					$this->setCopyright( $tag );
					$this->textTag[$key] = $this->getCopyright();
					break;
			}
		}
		
		return true;
	}
	
	/**
	 * Ritorna True se riesce a parserizzare il file 
	 * 
	 * Recupera dal file le informazioni Midi se queste sono presenti.
	 * Questo metodo utilizza la libreria getID3.
	 * http://www.getid3.org
	 *  
	 * @return bool
	 */
	private function parseTagRiff(){
		$tags = array();
		// recupera i tag riff
		if (isset($this->metadata['tags']['riff'])) {		
			$tags['type'] = 'riff'; 
			$tags['value'] =& $this->metadata['tags']['riff'];						 	
		} 	
		// se non presenta informazioni esce
		if (empty($tags)) {return false;}		
		
		// setta i tags
		$this->setMultimediaAllTags($tags['value']);
			
		return true;
	}
	
	/**
	 * Ritorna True se riesce a parserizzare il file 
	 * 
	 * Recupera dal file le informazioni Midi se queste sono presenti.
	 * Questo metodo utilizza la libreria getID3.
	 * http://www.getid3.org
	 *  
	 * @return bool
	 */
	private function parseTagMidi(){
		$tags = array();
		// recupera i tag Midi
		if (isset($this->metadata['tags']['midi'])) {		
			$tags['type'] = 'midi'; 
			$tags['value'] =& $this->metadata['tags']['midi'];						 	
		} 	
		// se non presenta informazioni esce
		if (empty($tags)) {return false;}		
		
		// setta i tags
		$this->setMultimediaAllTags($tags['value']);
			
		return true;
	}
	
	/**
	 * Ritorna True se riesce a parserizzare il file 
	 * 
	 * Recupera dal file le informazioni Vorbis Comment se queste sono presenti.
	 * Questo metodo utilizza la libreria getID3.
	 * http://www.getid3.org
	 *  
	 * @return bool
	 */
	private function parseTagVorbisComment(){
		$tags = array();
		// recupera i tag Vorbis
		if (isset($this->metadata['tags']['vorbiscomment'])) {		
			$tags['type'] = 'vorbiscomment'; 
			$tags['value'] =& $this->metadata['tags']['vorbiscomment'];						 	
		} 	
		// se non presenta informazioni esce
		if (empty($tags)) {return false;}		
		
		// setta i tags
		$this->setMultimediaAllTags($tags['value']);
			
		return true;
	}
	
	/**
	 * Ritorna True se riesce a parserizzare il file 
	 * 
	 * Recupera dal file le informazioni ASF se queste sono presenti.
	 * Questo metodo utilizza la libreria getID3.
	 * http://www.getid3.org
	 *  
	 * @return bool
	 */
	private function parseTagAsf(){
		$tags = array();
		// recupera i tag Asf
		if (isset($this->metadata['tags']['asf'])) {		
			$tags['type'] = 'asf'; 
			$tags['value'] =& $this->metadata['tags']['asf'];						 	
		} elseif (isset($this->metadata['asf']['comments'])) {
			$tags['type'] = 'asf'; 
			$tags['value'] =& $this->metadata['asf']['comments'];
		}		
		// se non presenta informazioni esce
		if (empty($tags)) {return false;}		
		
		// setta i tags
		$this->setMultimediaAllTags($tags['value']);
			
		return true;
	}
	
	/**
	 * Ritorna True se riesce a parserizzare il file 
	 * 
	 * Recupera dal file le informazioni QuickTime se queste sono presenti.
	 * Questo metodo utilizza la libreria getID3.
	 * http://www.getid3.org
	 *  
	 * @return bool
	 */
	private function parseTagQuickTime(){
		$tags = array();
		// recupera i tag quicktime
		if (isset($this->metadata['tags']['quicktime'])) {		
			$tags['type'] = 'quicktime'; 
			$tags['value'] =& $this->metadata['tags']['quicktime'];						 	
		} elseif (isset($this->metadata['quicktime'])) {
			$tags['type'] = 'quicktime'; 
			$tags['value'] =& $this->metadata['quicktime'];
		}		
		// se non presenta informazioni esce
		if (empty($tags)) {return false;}		
		
		// setta i tags
		$this->setMultimediaAllTags($tags['value']);
			
		return true;
	}
	
	
	/**
	 * Ritorna True se riesce a parserizzare il file 
	 * 
	 * Recupera dal file multimediale le informazioni ID3 se queste sono presenti.
	 * Questo metodo utilizza la libreria getID3.
	 * http://www.getid3.org
	 *  
	 * @return bool
	 */
	private function parseTagID3(){
		
		$tags = array();
		// definisce il tipo di ID3 da indicizzare
		if (isset($this->metadata['tags'])) {		
			// se presente ID3V2	
			if (isset($this->metadata['tags']['id3v2'])) {
				$tags['type'] = 'id3v2'; 
				$tags['value'] =& $this->metadata['tags']['id3v2']; 
			// se presente ID3V1
			} elseif (isset($this->metadata['tags']['id3v1'])) {
				$tags['type'] = 'id3v1'; 
				$tags['value'] =& $this->metadata['tags']['id3v1'];				 	
			} 
		} 
		// se non presenta informazioni esce
		if (empty($tags)) {return false;}		
		
		// setta i tags
		$this->setMultimediaAllTags($tags['value']);
			
		return true;
	}
		
	/**
	 * Setta ulteriori informazioni del file multimediale
	 * Questo metodo è indipendente dal tipo di file multimediale
	 * 
	 * @param array &$info
	 * @return 
	 */
	private function setAllInfo() {
		// tempo di esecuzione come stringa
		if (isset($this->metadata['playtime_string'])) {			
			$this->setPlayTimeString ($this->metadata['playtime_string']);
			$this->textTag['playtime_string'] = $this->getPlayTimeString();	
		}
		// tempo di esecuzione in secondi
		if (isset($this->metadata['playtime_string'])) {
			$this->setPlayTimeSecond ($this->metadata['playtime_seconds']);
			$this->textTag['playtime_seconds'] = $this->getPlayTimeSecond();
		}
		// bitrate
		if (isset($this->metadata['bitrate'])) {
			$this->setBitrate ($this->metadata['bitrate']);
			$this->textTag['bitrate'] = $this->getBitrate();
		}		
		// risoluzione video X
		if (isset($this->metadata['video']['resolution_x'])) {
			$this->setXResolution ($this->metadata['video']['resolution_x']);
			$this->textTag['XResolution'] = $this->getXResolution();
		}
		// risoluzione video Y
		if (isset($this->metadata['video']['resolution_y'])) {
			$this->setYResolution ($this->metadata['video']['resolution_y']);
			$this->textTag['YResolution'] = $this->getYResolution();
		}
		// Formato Video
		if (isset($this->metadata['video']['dataformat'])) {
			$this->setVideoFormat ($this->metadata['video']['dataformat']);
			$this->textTag['videoFormat'] = $this->getVideoFormat();
		}
		// Frame rate
		if (isset($this->metadata['video']['frame_rate'])) {
			$this->setFrameRate ($this->metadata['video']['frame_rate']);
			$this->textTag['frameRate'] = $this->getFrameRate();
		}
		// Four character code
		if (isset($this->metadata['video']['fourcc'])) {
			$this->setFourcc ($this->metadata['video']['fourcc']);
			$this->textTag['fourcc'] = $this->getFourcc();
		}
		// Video Encoder
		if (isset($this->metadata['video']['encoder'])) {
			$this->setVideoEncoder ($this->metadata['video']['encoder']);
			$this->textTag['videoEncoder'] = $this->getVideoEncoder();
		}
		// Formato Audio
		if (isset($this->metadata['audio']['dataformat'])) {
			$this->setAudioFormat ($this->metadata['audio']['dataformat']);
			$this->textTag['audioFormat'] = $this->getAudioFormat();
		}
		// Codec Audio
		if (isset($this->metadata['audio']['codec'])) {
			$this->setCodecAudio ($this->metadata['audio']['codec']);
			$this->textTag['codecAudio'] = $this->getCodecAudio();
		}
		// Tipo di Canale Audio
		if (isset($this->metadata['audio']['channelmode'])) {
			$this->setAudioChannelMode ($this->metadata['audio']['channelmode']);
			$this->textTag['audioChannelMode'] = $this->getAudioChannelMode();
		}
		// Canali Audio
		if (isset($this->metadata['audio']['channels'])) {
			$this->setAudioChannels ($this->metadata['audio']['channels']);
			$this->textTag['audioChannels'] = $this->getAudioChannels();
		}
		// Frequenza campionamento
		if (isset($this->metadata['audio']['sample_rate'])) {
			$this->setSampleRate ($this->metadata['audio']['sample_rate']);
			$this->textTag['sampleRate'] = $this->getSampleRate();
		}
		// Audio Encoder
		if (isset($this->metadata['audio']['encoder'])) {
			$this->setAudioEncoder ($this->metadata['audio']['encoder']);
			$this->textTag['audioEncoder'] = $this->getAudioEncoder();
		}
		// Audio Encoder Options
		if (isset($this->metadata['audio']['encoder_options'])) {
			$this->setAudioEncoderOptions ($this->metadata['audio']['encoder_options']);
			$this->textTag['audioEncoderOptions'] = $this->getAudioEncoderOptions();
		}
	}
	
	/**
	 * Setta i tag per l'indicizzazione.
	 * Questo metodo è indipendente dal tipo di file multimediale
	 * 
	 * @param array $tags
	 * @return 
	 */
	private function setMultimediaAllTags ($tags) {
		
		foreach ($tags as $key => $tag) {			
			switch ($key) {
				// Gruppo
				case 'band':
					$this->setBand( $tag[0] );
					$this->textTag[$key] = $this->getBand();
					break;				
				// copyright
				case 'copyright_message':
					$this->setCopyright( $tag[0] );
					$this->textTag[$key] = $this->getCopyright();
					break;
				// artista originale
				case 'original_artist':
					$this->setOriginalArtist( $tag[0] );
					$this->textTag[$key] = $this->getOriginalArtist();
					break;
				// battiti per minuto
				case 'bpm':
					$this->setBpm( $tag[0] );
					$this->textTag[$key] = $this->getBpm();
					break;
				// disco
				case 'part_of_a_set':
					$this->setPartOfASet( $tag[0] );
					$this->textTag[$key] = $this->getPartOfASet();
					break;
				// Editore
				case 'publisher':
					$this->setPublisher( $tag[0] );
					$this->textTag[$key] = $this->getPublisher();
					break;
				// Compositore
				case 'composer':
					$this->setComposer( $tag[0] );
					$this->textTag[$key] = $this->getComposer();
					break;
				// Album
				case 'album':
					$this->setAlbum ( $tag[0] );
					$this->textTag[$key] = $this->getAlbum();
					break;
				// Artista
				case 'artist':
					$this->setArtist( $tag[0] );
					$this->textTag[$key] = $this->getArtist();
					break;
				// Titolo
				case 'title':
					$this->setTitle ( $tag[0] );
					$this->textTag[$key] = $this->getTitle();
					break;
				// Anno
				case 'year':
				case 'date':
					$this->setYear  ( $tag[0] );
					$this->textTag[$key] = $this->getYear();
					break;
				// Genere
				case 'genre':
					$this->setGenre  ( $tag[0] );
					$this->textTag[$key] = $this->getGenre();
					break;
				// Traccia
				case 'track':
				case 'track_number':
					$this->setTrack  ( $tag[0] );
					$this->textTag[$key] = $this->getTrack();
					break;
				// Codificato da
				case 'encoded_by':
					$this->setEncodedBy  ( $tag[0] );
					$this->textTag[$key] = $this->getEncodedBy();
					break;
				// url_user
				case 'url_user':
					$this->setUrlUser  ( $tag[0] );
					$this->textTag[$key] = $this->getUrlUser();
					break;
				// commneti
				case 'comment':
					$this->setComment( $tag[0] );
					$this->textTag[$key] = $this->getComment();
					break;
				// Testo della canzone
				case 'unsynchronised_lyric':
					$this->setUnsynchronisedLyric( $tag[0] );
					$this->textTag[$key] = $this->getUnsynchronisedLyric();
					break;
				// Numero del disco
				case 'disc_number':
					$this->setDiscNumber( $tag[0] );
					$this->textTag[$key] = $this->getDiscNumber();
					break;
				// Compilation
				case 'compilation':
					$this->setCompilation( $tag[0] );
					$this->textTag[$key] = $this->getCompilation();
					break;
				// Valutazione
				case 'rating':
					$this->setRating( $tag[0] );
					$this->textTag[$key] = $this->getRating();
					break;
				// Stick
				case 'stik':
					$this->setStik( $tag[0] );
					$this->textTag[$key] = $this->getStik();
					break;
				// Encoder
				case 'encoder':
					$this->setEncoder( $tag[0] );
					$this->textTag[$key] = $this->getEncoder();
					break;
			}						
		}
	} 
		
	/**
	 * Verifia se la proprieta' non e' vuota
	 * 
	 * @param string $property
	 * @return bool
	 */
	public function issetNotEmpty($property) {
		$isNotEmpty = false;
		
		if (isset($this->$property) && (trim($this->$property) !== '')) {
			$isNotEmpty = true;				
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