# MODIFICHE

##  2.0 - 15 Settembre 2016
- Migration: migrazione da SourceForge (SVN) a GitHub (Git)
- Feature: definito il pacchetto IFile in Packagist
- Feature: implementati gli standart PHP-FIG: PRS-2, PRS-4
- Feature: configurazione del path della cartella contenente i file binari di terze parti
- Feature: configurazione del path assoluto di ANTIWORD installato sul server


## 1.4 - 16 Febbraio 2015
- Fixed: Risolto il bug sulla ricerca MultiTermine integrando il "signs" per la gestione dei termini "facoltativi" o "obbligatori"
- Fixed: Problema sul recupero della data di creazione e di modifica con la "infopdf" 
- Fetaure: integrata nella classe di configurazione il metodo per ritornare gli encoding supportati da IFile
- Fetaure: definizione delle coordinate in positivo e padding (utile nell'utilizzo della ricerca per Range) nell'indicizzazione delle immagini (JPG)
- Feature: migliorata la presentazione della server check - creato il nuovo metodo printReportCheckWeb() 
- Feature: migliorata la ricerca di tipo Boolean permettendo ora anche l'integrazione di search di tipo Range
- Feature: Aggiunto l'attributo "search type" nella IFileQuery per la gestione della Range nella ricerca di tipo Boolean
- Feature: Integrata la libreria getID3 per la lettura dei metadata dei file immagini e multimediali [Grazie a James Heinrich](http://www.getid3.org ) 
- Feature: Creati gli Adapter per il recupero dei contenuti di file multimediali
  - 3GP  (Third Generation Platform)
  - AAC  (Advanced Audio Coding)	
  - AC3  (Audio Compression - 3)	
  - AIF  (Audio Interchange Format)
  - AIFF (Audio Interchange File Format)
  - ASF  (Advanced Systems Format)
  - FLAC (Free Lossless Audio Codec)
  - FLV  (Flash Video)
  - IFF  (Interchange File Format)	
  - M4A  (MPEG 4 Audio)
  - MID  (Musical Instrument Digital Interface)
  - MOV  (MOVie)
  - MP1  (MPEG-1)
  - MP2  (MPEG-2)
  - MP4  (MPEG-4)
  - SWF  (Shockwave Flash)
  - WAV  (WAVEform Audio File Format)
  - WMA  (Windows Media Audio)
  - WMV  (Windows Media Video)
  - PNG  (Portable Network Graphics)
  - SVG  (Scalable Vector Graphics)

## 1.3 - 11 Novembre 2013
- Fixed: Permessa la ricerca per frase anche per singolo termine
- Fixed: Utilizzo dei TokenFilter anche in fase di ricerca
- Fixed: Creazione degli oggetti Analyzer e Token Filter configurati solo quando servono
- Feature: Integrato l'utilizzo del Porter Stemmer per l'inglese
- Feature: Integrato l'utilizzo della PECL Stem per le seguenti lingue:
  - Danese
  - Dutch
  - Tedesco
  - Inglese
  - Finlandese
  - Francese
  - Ungherese
  - Italiano
  - Norvegese
  - Portoghese
  - Rumeno
  - Russo
  - Spagnolo
  - Svedese
  - Turco

## 1.2.1 - 03 Settembre 2013
- Feature: Inserita la possibilità di configurare il field di default della ricerca
- Feature: Inserita la possibilità di configurare il limite di risultati della ricerca
- Feature: Aggiunto il metodo terms() che ritorna tutti i termini indicizzati (solo per l'interfacia Lucene)
- Feature: Aggiunto il metodo getTermsForField($field) che ritorna tutti i termini indicizzati per il solo campo (solo per l'interfacia Lucene)
- Fixed: Modificato il metodo getConfig nella classe IFileConfig, che ritorna tutta la struttura della configurazione se non viene passata nessune proprieta'

## 1.2 - 30 Luglio 2013
- Feature: Aggiunto il field "serchablename" di tipo "UnStored" per permettere l'indicizzazione e la ricerca sul nome del file
- Fixed: Modificato il Type di default a "CAMPO CHIAVE" del field: "extensionfile" per permettere la ricerca per tipo di estensione
- Fixed: Modificato il Type di default a "BINARIO" dei fields: "name", "path", "filename" per il corretto salvataggio di path contenenti caratteri speciali 
- Feature: Aggiunta la pdfinfo per linux e windows, per leggere le informazioni dai file PDF
- Feature: Aggiunta nella IFileQueryRegistry::setQuery(), la definizione del tipo di encoding per i termini di ricerca, utilizzato nella ricerca per frase e wildcard
- Feature: Integrata la possibilità di definire il tipo di server se 32 o 64 bit per la gestione della XPDF
- Feature: Integrata la possibilità di recuperare la configurazione originale dopo averla modificata
- Feature: Integrata la possibilità di sovrascrivere la configurazione originale
- Feature: Integrata la possibilità di definire una password per leggere i documenti PDF protetti
- Feature: Integrata la possibilità di definire una XPDF personale per leggere i documenti PDF
- Feature: Integrata la possibilità di definire una xpdfrc personale per configurare la XPDF
- Feature: Integrata nella Check Report la lista delle estensioni consentite in IFILE per l'indicizzazione automatica
- Feature: Creato l'Adapter per il recupero dei contenuti degli EXIF Tag dei file TIFF 

## 1.1.5 - 12 Settembre 2012
- Feature: Integrata la possibilità di recuperate il contenuto dei file DOC mediatne l'utilizzo di "Antiword" (solo per sistemi a 32bit) o le librerie "COM" per sistemi windows
- Feature: Il metodo addDocument() ora ritorna l'oggetto Zend_Search_Lucene_Document con i contenuti del documento indicizzato.
- Feature: Eliminato l'utilizzo della XPDF del server. Dalla versione 1.1.5 IFile utilizza solo la XPDF presente nella cartella /adapter/helpers/binaries/

## 1.1.4 - 05 Marzo 2012  
- Feature: Integrata nella Search dell'interfaccia MySqli la ricerca su tutti i fields indicizzati
- Feature: Integrata la classe IFileVersion per il recupero della versione del pacchetto
- Feature: Gestione del file di configurazione (xpdfrc) per la pdftotext
- Feature: Integrata la LIMIT alla getAllDocument()
- Feature: Integrato il percorso della XPDF nei messaggi della servercheck.
- Feature: Aggiunta l'interfaccia deleteAll() per la cancellazione di tutti i documenti o dell'intero indice.
- Feature: Integrata la gestione delle "COLLATION" per l'interfaccia MySqli.
- Feature: Aggiunti i tipi di "Encoding" ISO-8859-2 e ISO-8859-7 
- Fixed: Eliminate le chiamate alla funzione "eval()" nella classe IFile_Indexing_Mysqli.php   

## 1.1.3 - 16 Gennaio 2012
- Feature: Integrata la configurazione del tipo di indicizzazione e l'encoding dei Field (campi) utilizzati da IFile dal file XML di configurazione 
- Feature: Integrato controllo se PHP gira su sistemi a 36bit o 64bit per la gestione delle binary XPDF

## 1.1.2 - 03 Gennaio 2012
- BugFix (bug item #3468872): Il percorso presente nella require() del file "ifile\helpers\IFileQueryHit.php" non e' corretto per i sistemi linux
- BugFix (bug item #3468880): L'Adapter per la parserizzazione dei documenti RTF che contenevano immagini andava in time-limit 
- Feature: Creato l'Adapter per il recupero dei contenuti dei file XML
- Feature: Gestita meglio la classe PHPWordLib per la parserizzazione dei file RTF
- Feature: Integrato il controllo nella classe LuceneServerCheck sulla esistenza della funzione strip_tag per la parserizzazione dei file XML

## 1.1.1 - 11 Ottobre 2011
- BugFix (bug item #3421591): Il percorso per il recupero delle classi StopWords o ShortWords non e' corretto per i sistemi linux
- BugFix (bug item #3416730): Spostata la gestione del TimeLimit per tutto il processo di parserizzazione e indicizzazione 
- Feature: Integrata negli adapter la verifica della presenza delle funzioni / estensioni di PHP siano installete per evitare i FATAL ERROR
- Feature: Gestita meglio la classe LuceneServerCheck per il check dei requisiti

## 1.1 - 28 Settembre 2011
- BugFix (bug item #3391555): NOTICE in IFile_Indexing_Abstract.php
- BugFix (bug item #3392419): Utilizzando Zend Framework 1.11 non trova le classi per la gestione delle stop-word  e short-word
- BugFix (bug item #3401621): Il controllo sull'eseguibilita' della XPDF non funziona su sistemi Macintosh OS
- Feature: Trasformata la classe LuceneServerCheck in SINGLETON
- Feature: Integrato il controllo sulla presenza del framework Zend nell'utilizzo dell'interfaccia LUCENE
- Feature: Creato l'Adapter per il recupero dei contenuti degli ID3 Tag dei file MP3
- Feature: Creato l'Adapter per il recupero dei contenuti dei file RTF
- Feature: Creato l'Adapter per il recupero dei contenuti degli EXIF Tag dei file JPEG 
- Feature: Creata la nuova interfaccia MYSQLI per indicizzare i contenuti nel DB MySql 
- Feature: Creato il field "introtext" che contiene una porzione (circa 200 caratteri) del testo indicizzato
- Feature: Creato il field "extensionfile" che contiene l'estensione del file indicizzato

## 1.0.1 - 08 Agosto 2011 
- BugFix (bug item #3381997): Se il tag <analyzer> (tag opzionale) non e' presente, IFile non lavorara correttamente
- BugFix (bug item #3382019): Quando si provava a parserizzare file PDF in Linux , questo ritornava sempre contenuti vuoti
- BugFix (bug item #3382908): Sui sistemi Linux, il controllo delle librerie PEAR non funzionava a causa di un errata gestione del carattere separatore dei path
- BugFix (bug item #3386275): La queryBoolean per l'interfaccia LUCENE non gestisce correttamente le ricerche per gruppi di termini
- BugFix (bug item #3387239): NOTICE in LuceneServerCheck.php

## 1.0 - 28 Giugno 2011  
- Creati gli Adapter per il recupero dei contenuti dei seguenti docimenti DOC, DOCX, HTM, HTML, ODS, ODT, PDF, PPTX, TXT, XLS, XLSX.
- Creata l'interfaccia per lavorare con Zend_Search_Lucene.
- Creato framework IFile.