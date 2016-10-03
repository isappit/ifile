# CHANGELOG

##  2.0 - 03 Ottobre 2016
- Migration: migration from SourceForge (SVN) to GitHub (Git)
- Feature: defined IFile how package in Packagist
- Feature: implemented standart PHP-FIG: PRS-2, PRS-4
- Feature: configuration of the folder of third-parts bionaries files
- Feature: configuration of the absolute path of ANTIWORD installed on server

## 1.4 - 16 February 2015
- Fixed: Solved bug on MultiTerms search. Integrated the "signs" to management the "optional" terms or "required"  
- Fixed: Correct creation and modify date with the "infopdf" 
- Fetaure: Created in the configuration class the new method to return the supported encoding type 
- Feature: new layout to display the server check result 
- Feature: improved the Boolean search with the integration of the Range search 
- Feature: new attribute "search type" in the IFileQuery class, to management Range in Boolean search
- Feature: imported the getID3 library [Thanks to James Heinrich](http://www.getid3.org ) 
- Feature: created new adapters to parser other multimedia files
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
	
## 1.3 - 11 November 2013
- Fixed: Searchable for single term also for "Exact Phrase" 
- Fixed: Use of the TokenFilter also in search method
- Fixed: Creating object in Lazy pattern for Analyzer and Token Filter object
- Feature: Integrated the use for English Porter Stemmer
- Feature: Integrated the use of PECL Stemmer for languages:
  - danish
  - dutch
  - english
  - finnish
  - french
  - german
  - hungarian
  - italian
  - norwegian
  - porter
  - portuguese
  - romanian
  - russian
  - spanish
  - swedish
  - turkish

## 1.2.1 - 03 September 2013
- Feature: Added the ability to configure the "default search field"
- Feature: Added the ability to configure the limit of search results
- Feature: Added the method terms(), returns all indexed terms (only for Lucene Interface)
- Feature: Added the method getTermsForField($field) returns all indexed terms for only one field (for the Lucene Interface)
- Fixed: Changed the method GetConfig in the IFileConfig class, returns the entire structure of the configuration is not passed no specific properties

## 1.2 - 30 July 2013
- Feature: Added the field "serchablename" of type "UnIndexed" to allow searching on the file name
- Fixed: Changed the defaults Type to "KEYWORDS" at the field: "extensionfield" to search the documents for type of extension 
- Fixed: Changed the defaults Type to "BINARY" of fields: "name", "path", "filename" for saving the correct path containing special characters
- Feature: Add pdfinfo for linux and windows, for read information fron PDF files
- Feature: Added in the nella IFileQueryRegistry::setQuery(), the definition of the type of encoding for the search terms, used in the phrase search and wildcard
- Feature: Integrated the ability to define the type of the server at 32 or 64 bit for the XPDF
- Feature: Integrated the ability to recover the original configuration after editing
- Feature: Integrated the ability to overwrite the original configuration
- Feature: Integrated the ability to define a password to read PDF documents protected
- Feature: Integrated the ability to define a personal XPDF to read PDF documents
- Feature: Integrated the ability to define a personal "xpdfrc" to configured the XPDF
- Feature: Integrated in the "Report Check", the list of allowed extensions in iFile for automatic indexing
- Feature: Created the adapter to retrieve the contents of the EXIF tags of TIFF files

## 1.1.5 - 12 September 2012
- Feature: Integrated "ANTIWORD" and libraries COM (for windows system) to retrieve the contents of the DOC files    
- Feature: addDocument() method, return Zend_Search_Lucene_Document object
- Feature: Deleted use of XPDF, installed in the server. IFile 1.1.5 uses only XPDF incluse in "/adapter/helpers/binaries/" folder

## 1.1.4 -  05 March 2012  
- Feature: New search for MySqli interface with all fields
- Feature: Added class IFileVersion 
- Feature: Management of the configuration file (xpdfrc) for the pdftotext
- Feature: Added LIMIT for getAllDocument()
- Feature: Added path XPDF in message error
- Feature: Added method deleteAll() for delete all documents or index
- Feature: Added attribute "COLLATION" for interface "MySqli"
- Feature: Added "Encoding" ISO-8859-2 and ISO-8859-7 
- Fixed: Deleted method eval() in IFile_Indexing_Mysqli class

## 1.1.3 - 16 January 2012
- Feature: Configure the fields type and encoding, used by IFile 
- Feature: check if PHP is running on Server to 36bit or 64bit for management binary XPDF

## 1.1.2 - 03 January 2012
- Bugfix (bug item # 3468872): The require_once() path in the IFileQueryHit is not 'correct for linux systems
- Bugfix (bug item # 3468880): The adapter parses the RTF documents containing images was time-limit
- Feature: Created the adapter to retrieve the contents of XML file
- Feature: New management PHPWordLib class for parses RTF files
- Feature: Integrated control on the existence strip_tag function for parses the XML files

## 1.1.1 - 11 October 2011
- BugFix (bug item #3421591): In the class IFile_Indexing_Lucene the path for require StopWord or ShortWords is incorrect in LINUX.
- BugFix (bug item #3416730): New management MAX TIME-LIMIT
- Feature: Integrated in the Adapter Class to verify the presence of Function / Extensions of PHP
- Feature: New management LuceneServerCheck Class


## 1.1 - 28 September 2011
- BugFix (bug item #3391555): NOTICE in IFile_Indexing_Abstract.php
- BugFix (bug item #3392419): Using Zend Framework 1.11 does not find classes for managing and short-word and stop-word
- BugFix (bug item #3401621): Control over the xpdf does not work on Macintosh OS
- Feature: Change class LuceneServerCheck in SINGLETON
- Feature: Integrated control over the presence of the Zend Framework use interface LUCENA
- Feature: Created the adapter to retrieve the contents of the ID3 tags of MP3 files
- Feature: Created the adapter to retrieve the contents of the RTF files
- Feature: Created the adapter to retrieve the contents of the EXIF tags of JPEG files
- Feature: Created the new "mysqli" interface to index content in MySQL DB
- Feature: Created the field "introtext" that contains a portion (approximately 200 characters) of text indexed ion the "Body" field
- Feature: Created the field "extensionfile" that contains the extension of the indexed file

## 1.0.1 - 08 August 2011
- BugFix (bug item #3381997): Tag <analyzer> (tag optional)
- BugFix (bug item #3382019): XPDF on Linux not works
- BugFix (bug item #3382908): Error on linux for "separator directory"
- BugFix (bug item #3386275): queryBoolean not works
- BugFix (bug item #3387239): NOTICE in LuceneServerCheck.php

## 1.0 - 28 June 2011  
- Created Adapter for DOC, DOCX, HTM, HTML, ODS, ODT, PDF, PPTX, TXT, XLS, XLSX documents.
- Created Lucene interface for Zend_Search_Lucene use.
- Created IFile.