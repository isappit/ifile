/**
 * Descrizione file XML IFileConfig.xml
 *
 * Il file permette di configurare la libreria IFile
 *
 * Importante:
 * 
 * 1. Il file si deve trovare sotto la cartella "config"
 * 2. Questo file XML viene validato dal file XSD "config\IFileConfig.xsd"
 *
 * STRUTTURA
 *
 <ifile>
 	<root-application>...</root-application>
 	<table-name collation="..." engine="...">...</table-name>
 	<timelimit>...</timelimit>
	<memorylimit>...</memorylimit>
	<resultlimit>...</resultlimit>
	<default-search-field>...</default-search-field>	
	<duplicate>...</duplicate>
	<server bit="..." />
 	<encoding>...</encoding>
	<doctotxt encoding"..." type="..." />
	<xpdf>
		<opw>...</opw>
		<pdftotext>
			<executable>...</executable>
			<xpdfrc>...</xpdfrc>
		</pdftotext>				
		<pdfinfo>
			<executable>...</executable>
			<xpdfrc>...</xpdfrc>
		</pdfinfo>				
	</xpdf>
	<zend-document>
		<fields>
			<field name="..." type="..." />
			...						
		</fields>		
	</zend-document>
 	<analyzer>
 		<type>
 			<default>...</default>
			<custom-default class="...">...</custom-default>				
 		</type>
		<filters>
	 		<stop-words>...</stop-words>
	 		<short-words>...</short-words>
			<custom-filters>
				<filter class="...">...</filter>
				...
			</custom-filters>
		</filters>
	</analyzer>
 </ifile>
 *
 *
 * DESCRIZIONE
 * 1
 * <ifile>...</ifile> [obbligatorio]{1}
 * TYPE: ComplexType - all
 *
 * 2
 * <root-application>...</root-application> [obbligatorio]{1}
 * TYPE: string
 * 
 * Configurazione della root dell'applicazione.
 * Questo e' molto utile da utilizzare in caso si debba spostare
 * l'applicazione in ambienti diversi, permettendo cosi' di avere
 * path relativi dei file indicizzati.  
 *
 * 3
 * <table-name>...</table-name> [opzionale] {1}
 * TYPE: string
 *
 * Configurazione del nome della tabella utilizzata per l'indicizzazione
 * dei file su un DB (non ancora implementata).
 *
 * Attribute: collation [opzionale]
 * TYPE: string
 *
 * Nome della collation da utilizzare per l'ordinamento associato al charset
 *
 * Attribute: engine [opzionale]
 * TYPE: string
 *
 * Nome del tipo di Engine da utilizzare per la FullText (MyISAM | InnoDB)
 *
 * 4
 * <timelimit>...</timelimit> [opzionale] {1}
 * TYPE: integer
 *
 * Configurazione del tempo massimo di esecuzione del processo di parsering.
 * Il valore minimo e' di 180 secondi.
 *
 * 5
 * <memorylimit>...</memorylimit> [opzionale] {1}
 * TYPE: integer
 *
 * Configurazione la memoria massima (in MEGABYTE) che lo script può allocare durante 
 * l'esecuzione del processo di parsering e indicizzazione.
 *
 * 6
 * <resultlimit>...</resultlimit> [opzionale] {1}
 * TYPE: integer
 *
 * Configurazione del numero massimo di risultati che la query di ricerca deve restituire.
 * Se non settato ritorna tutti i risultati.
 *
 * 7
 * <default-search-field>...</default-search-field> [opzionale] {1}
 * TYPE: integer
 *
 * Configurazione del field (campo) dove effettuare la ricerca del termine.
 * Se non settato ricerca in tutti i fields (campi) dell'indice
 *
 * 8
 * <encoding>...</encoding> [opzionale] {1} 
 * TYPE: enumeration
 * - UTF-8
 * - ASCII
 * - ISO8859-1
 * - ISO8859-15
 * - ISO8859-2
 * - ISO8859-7
 * - CP1256
 * - Windows-1252
 *
 * Elenco del tipo di enconding.
 * Se non settato si prende come parametro di default: null.
 *
 * 9
 * <duplicate>...</duplicate> [opzionale] {1}
 * TYPE: enumeration
 * - 0
 * - 1
 *
 * Definisce la possibilità di avere documenti duplicati all'interno dell'indice.
 * Ovvero se settato a zero (0) o non presente il tag il sistema verifica che il contenuto
 * del documento da indicizzare non sia gia' presente nell'indice.
 * Se presente invoca una eccezione. Altrimenti se settato a uno (1) il sistema non
 * verifica l'esistenza del documento all'interno dell'indice
 *
 * 10 
 * <server bit="..." /> [opzionale] {1}
 *
 * Serve a definire il tipo server
 * TYPE: ComplexType
 *
 * Attribute: bit [opzionale]
 * TYPE: enumeration
 * - 32
 * - 64
 *
 * 11 
 * <doctotxt encoding="..." type="..." /> [opzionale] {1}
 *
 * Serve a definire il tipo di parser da utilizzare per il recupero dei contenuti dei file .doc
 * TYPE: ComplexType
 *
 * Attribute: encoding [opzionale]
 * TYPE: string
 * 
 * Elenco dei tipi di encoding per il recupero del contenuto dei .doc 
 * utilizzato solo per il tipo di parser ANTIWORD, vedi elenco nella cartella: 
 * 	adapter/helpers/binaries/resources/
 *
 * Nell'attributo va solo scritto il nome del file senza estensione
 * Esempio: * adapter/helpers/binaries/resources/8859-1.txt
 * encoding="8859-1"
 * In caso non viene settato ANTIWORD utilizza l'encodind di default 8859-1.txt
 *
 * Attribute: type [obbligatorio]
 * TYPE: enumeration
 * - PHP
 * - COM
 * - ANTIWORD
 * 
 * Elenco dei tipi di parser da utilizzare per il recupero del contenuto dai file .doc
 * Se non definito IFile utilizza il metodo PHP  
 *
 * 12
 * <xpdf>...</xpdf> [opzionale] {1}
 * TYPE: ComplexType - sequence
 *
 * 12.1
 * <opw>...</opw> [opzionale] {1}
 * TYPE: string
 * Contiene la password di protezione dei file PDF
 *
 * 12.2
 * <pdftotext>...</pdftotext> [opzionale] {1}
 * TYPE: ComplexType - sequence
 *
 * 12.2.1
 * <executable>...</executable> [opzionale] {1}
 * TYPE: string
 * Definisce un path diverso per l'eseguibile della pdftotext, compreso il nome dell'eseguibile
 *
 * 12.2.2
 * <xpdfrc>...</xpdfrc> [opzionale] {1}
 * TYPE: string
 * Definisce un path diverso per la xpdfrc da utilizzare nella pdftotext, compreso il nome del file
 * 
 * 12.3
 * <pdfinfo>...</pdfinfo> [opzionale] {1}
 * TYPE: ComplexType - sequence
 *
 * 12.3.1
 * <executable>...</executable> [opzionale] {1}
 * TYPE: string
 * Definisce un path diverso per l'eseguibile della pdftotext, compreso il nome dell'eseguibile
 *
 * 12.3.2
 * <xpdfrc>...</xpdfrc> [opzionale] {1}
 * TYPE: string
 * Definisce un path diverso per la xpdfrc da utilizzare nella pdftotext, compreso il nome del file 
 *
 * 13
 * <zend-document>...</zend-document> [opzionale] {1}
 * TYPE: ComplexType - sequence
 *
 * Contiene i TAG per la configurazione dei parametri per la Zend_Search_Lucene_Document
 *
 * 13.1
 * <fields>...</fields> [opzionale] {1}
 * TYPE: ComplexType - sequence
 *
 * Contenitore per la configurazione dei Field
 *
 * 13.1.1
 * <field /> [obbligatorio]{n}
 * TYPE: ComplexType
 * Attribute: name [obbligatorio]
 * TYPE: enumeration
 * - name
 * - extensionfile
 * - path
 * - filename
 * - introtext			
 * - body
 * - title
 * - subject
 * - description
 * - creator
 * - keywords
 * - created
 * - modified
 *
 * Elenco dei Field "Standard" utilizzati da IFile
 *
 * Attribute: type [obbligatorio]
 * TYPE: enumeration
 * - Keyword
 * - UnIndexed
 * - Binary
 * - Text
 * - UnStored
 *
 * Se si sta utilizzando come SearchEngine MySql sarà necessario obbligatoriamente definire i seguenti field di tipo "text"
 * - name
 * - path
 * - filename
 *
 * Esempio:
 * <zend-document>
 *		<fields>			
 *			<field name="name" type="Text" />
 *			<field name="path" type="Text" />
 *			<field name="filename" type="Text" />			
 *		</fields>		
 *	</zend-document>
 *
 * Elenco dei tipi di field permessi da Zend Lucene
 *
 * Attribute: encoding [opzionale]
 * TYPE: enumeration
 * - UTF-8
 * - ASCII
 * - ISO8859-1
 * - ISO8859-15
 * - ISO8859-2
 * - ISO8859-7
 * - CP1256
 * - Windows-1252
 *
 * Elenco del tipo di enconding.
 *
 * 14
 * <analyzer>...</analyzer> [opzionale] {1}
 * TYPE: ComplexType - all
 * 
 * Contiene i TAG per la gestione degli analizatori e dei filtri del testo
 *
 * 14.1
 * <type>...</type> [opzionale] {1}
 * TYPE: ComplexType - choice
 *
 * Contiene due TAG ALTERNATIVI per la gestione del tipo di analyzer
 * Se non settato si prende come parametro di default: Utf8_CaseInsensitive (vedi enumeration sotto).
 *
 * 14.1.1
 * <default>...</default> [a scelta] {1}
 * TYPE: enumeration
 * - Text
 * - TextNum
 * - Text_CaseInsensitive
 * - TextNum_CaseInsensitive
 * - Utf8
 * - Utf8Num
 * - Utf8_CaseInsensitive
 * - Utf8Num_CaseInsensitive
 *
 * Configurazione del tipo di analyzer implementato nella Zend Framework-
 *
 * Per maggiori dettagli sul tipo di analyzer si demanda al sito della Zend Framework.
 * http://framework.zend.com/manual/en/zend.search.lucene.extending.html
 * Il tag è alternativo con <custom-default>
 *
 * 14.1.2
 * <custom-default>...</custom-default> [a scelta] {1}
 * TYPE: string
 * Attribute: class [obbligatorio]
 *
 * Configurazione del nome della classe (Attributo: class="nomeClasse") che estende Zend_Search_Lucene_Analysis_Analyzer
 * e del path assoluto del file che la contiene.
 *
 * Per maggiori dettagli su come implementare un analyzer personalizzato si demanda al sito della Zend Framework
 * http://framework.zend.com/manual/en/zend.search.lucene.extending.html
 * Il tag è alternativo con <default>
 *
 * 14.2
 * <filters>...</filters> [opzionale] {1}
 * TYPE: ComplexType - all
 * 
 * Contiene i TAG per la gestione del filtraggio dei Token da indicizzare 
 *
 * 14.2.1
 * <stop-words>...</stop-words> [opzionale] {1}
 * TYPE: string
 *
 * Configurazione del path del file .txt delle parole che non possono essere utilizzate come Token.
 *
 * 14.2.2
 * <short-words>...</short-words> [opzionale] {1}
 * TYPE: integer
 *
 * Configurazione del numero minimo di caratteri del Token.
 *
 * 14.2.3
 * <custom-filters>...</custom-filters> [opzionale] {1}
 * TYPE: ComplexType - all
 *
 * Contenitore dei TAG per la gestione di filtri personalizzati
 *
 * 14.2.3.1
 * <filter class="...">...</filter> [opzionale] {n}
 * TYPE: string
 * Attribute: class [obbligatorio]
 *
 * Configurazione del nome della classe (Attributo: class="nomeClasse") che estende Zend_Search_Lucene_Analysis_TokenFilter
 * e del path assoluto del file che la contiene.
 *
 * Per maggiori dettagli su come implementare un analyzer personalizzato vedi il sito della Zend Framework
 * http://framework.zend.com/manual/en/zend.search.lucene.extending.html
 */