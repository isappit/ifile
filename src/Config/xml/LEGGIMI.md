# IFile configurazione

 Questo file descrive come configurare correttamente la libreria IFile.
 
 **Importante:**
 
1. Il file di configurazione si trova in "Config/xml/IFileConfig.xml". Dalla versione 2.0 è possibile anche utilizzare un file XML esterno alla libreria (vedi IFile documentazione)
2. il file di configurazione viene validato dal file XSD "Config/xml/IFileConfig.xsd"

## STRUTTURA DEL FILE
 
 ```xml
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
	<doctotxt encoding="..." type="..." />
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
 ```
 
## DESCRIZIONE TAG XML

### ifile
La root del file di configurazione 

```xml
<ifile>...</ifile>
```

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
ifile        | obbligatorio | 1            | ComplexType - all

### root-application
Configurazione del path-root dell'applicazione.

Questo e' molto utile da utilizzare in caso si debba spostare l'applicazione in ambienti diversi, 
permettendo cosi' di avere path relativi dei file indicizzati in fase di ricerca e recupero.

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
root-application | obbligatorio | 1            | string 

Esempio:

```xml
<root-application>/usr/local/var/wwww/myproject</root-application>
```

### binaries
Configura il percorso dei file binari. Questo tag è necessario per la configurazione corretta del percorso dove verranno salvati i binari delle componenti di terza parte. 

**_Se non settato IFile utilizza di default:_** "vendor/isappit/ifile/src/Adapter/Helpers/binaries"

E' necessario scaricare i binari di terze parti da questo [link](#).

Tag          | Property     | Occurrences   | Type
------------ | ------------ | ------------ | -------------
binaries     |  optional    | 1            | string 

Esempio:

```xml
<binaries>/usr/local/var/ifile/binaries</binaries>
```

### table-name
Configurazione del nome della tabella del database utilizzata per l'indicizzazione. 

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
table-name   | opzionale    | 1            | string

Esempio:

```xml
<table-name collation="utf8_general_ci" engine="MyISAM">my_table</table-name>
```

Attributo    | Proprietà    | Tipo          | Descizione
------------ | ------------ | ------------- | ------------ 
collation    | opzionale    | string        | Nome della collation da utilizzare per l'ordinamento associato al charset
engine       | opzionale    | string        | Nome del tipo di Engine da utilizzare per la FullText (MyISAM | InnoDB)

### timelimit
Configurazione del tempo massimo di esecuzione del processo di parsering.

**_Il valore minimo accettato e' di 180 secondi._**

**_Se non settato il valore è quello configurato nel php.ini._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
timelimit    | opzionale    | 1            | integer

Esempio:

```xml
<timelimit>600</timelimit>
```

### memorylimit
Configurazione la memoria massima (in MEGABYTE) che lo script può allocare
durante l'esecuzione del processo di parsering e indicizzazione.

**_Se non settato il valore è quello configurato nel php.ini._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
memorylimit  | opzionale    | 1            | integer

Esempio:

```xml
<memorylimit>512</memorylimit>
```

### resultlimit
Configurazione del numero massimo di risultati che la query di ricerca deve restituire.

**_Se non settato ritorna tutti i risultati._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
resultlimit  | opzionale    | 1            | integer

Esempio:

```xml
<resultlimit>100</resutllimit>
```

### default-search-field
Configurazione per forzare la ricerca dei termini in un determinato field (campo).

**_Se non settato ricerca in tutti i fields (campi) dell'indice_**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
default-search-field  | opzionale    | 1            | string

Esempio:

```xml
<default-search-field>body</default-search-field>
```

### encoding
Elenco del tipo di enconding.

**_Se non settato IFile utilizza di default:_** _null_

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
encoding     | opzionale    | 1            | enumeration

_Valori ammessi - Attributo "encoding":_
 - UTF-8
 - ASCII
 - ISO8859-1
 - ISO8859-15
 - ISO8859-2
 - ISO8859-7
 - CP1256
 - Windows-1252

Esempio:

```xml
<encoding>UTF-8</encoding>
```

### duplicate
Definisce la possibilità di avere documenti duplicati all'interno dell'indice.
Ovvero se settato a zero (0) o il tag non è presente, il sistema verifica che il contenuto
del documento da indicizzare non sia gia' presente nell'indice.
Se presente invoca una eccezione. 
Altrimenti se settato a uno (1) il sistema non verifica l'esistenza del documento all'interno dell'indice

**_Se non settato IFile utilizza di default:_** _0_

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
duplicate    | opzionale    | 1            | enumeration

_Valori ammessi - Attributo "duplicate":_
 - 0 (default)
 - 1

Esempio:

```xml
<duplicate>1</duplicate>
```

### server
E' un tag chiuso e serve a definire il tipo server

**_Se non settato IFile utilizza di default:_** _32_

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
server       | opzionale    | 1            | 

Attributo    | Proprietà    | Tipo          | Descizione
------------ | ------------ | ------------- | ------------ 
bit          | obbligatorio | enumaration   | Definisce se il server è a 32 o 64 bit, utile per l'utilizzo corretto della XPDF e altre componenti di terze parti 

_Valori ammessi - Attributo "bit":_
 - 32 (default)
 - 64

Esempio:

```xml
<server bit="64" />
```

### doctotxt
E' un tag chiuso e serve a definire il tipo di parser da utilizzare per il recupero dei contenuti dei file Microsoft Word ( con estensione .doc )

**_Se non settato IFile utilizza il type di default:_** _PHP_

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
doctotxt     | opzionale    | 1            | ComplexType 

Attributo    | Proprietà    | Tipo          | Descizione
------------ | ------------ | ------------- | ------------ 
encoding     | opzionale    | string        | Utilizzato **_solo per il tipo di parser ANTIWORD_**. Elenco dei tipi di encoding per il recupero del contenuto dei .doc 
type         | obbligatorio | enumeration   | Elenco dei tipi di parser da utilizzare per il recupero del contenuto dai file .doc 

_Valori ammessi - Attributo "encoding":_
 Gli encoding disponibili sono presenti nella cartella "Adapter/Helpers/binaries/resources", 
 nell'attributo "encoding" deve essere utilizzato il nome del file senza estensione.
 Vedi esempio per il parser con ANTIWORD.
 
 **_Se non settato ANTIWORD utilizza l'encodind di default:_** _8859-1_
 
 Esempio: se si vuole utilizzare come file di encoding "UTF-8.txt", andrà settato l'attributo encoding="UTF-8" 

_Valori ammessi - Attributo "type":_
 - PHP (default)
 - COM
 - ANTIWORD

**type = "PHP"**

IFile utilizza una classe PHP per il recupero dei contenuti, questa classe permette la lettura di documenti nel solo encoding 8859-1   

Esempio:

```xml
<doctotxt type="PHP" />
```

**type = "COM"**

IFile utilizza la libreria "COM" di PHP. Questa libreria è utilizzabile solo su macchine con sistema operativo Windows, vedi [Documentazione PHP](http://php.net/manual/en/com.requirements.php)  

Esempio:

```xml
<doctotxt type="COM" />
```
**type = "ANTIWORD"**

IFile utilizza la componente binaria di terze parti ANTIWORD per la lettura dei contenuti dei documenti Microsoft Word, [maggiori dettagli](http://www.winfield.demon.nl/)   

Esempio per parser ANTIVORD:

```xml
<doctotxt encoding="UTF-8" type="ANTIWORD" />
```

### xpdf
Permette di configurare la componente binaria di terze parti XPDF per la lettura dei contenuti dei documenti PDF, [maggiori dettagli](http://www.foolabs.com/xpdf/)

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
xpdf         | opzionale    | 1            | ComplexType - sequence

Esempio:

```xml
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
```

#### xpdf -> opw
Contiene la password di protezione dei file PDF.

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
opw          | opzionale    | 1            | string

Esempio:

```xml
<opw>38sh7s9#@9hs0</opw>
```

#### xpdf -> pdftotext
Contiene i tag per la configurazione della componente binaria di terze parti "pdftotext"

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
pdftotext    | opzionale    | 1            | ComplexType - sequence

Esempio:

```xml
<pdftotext>
    <executable>...</executable>
    <xpdfrc>...</xpdfrc>
</pdftotext>
```

#### xpdf -> pdftotext -> executable
Definisce il path per la componente binaria di terze parti "pdftotext". 

**_Se non definito, IFile cerca di default di utilizzare la componente presente nella libreria in funzione del sistema operativo._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
executable   | opzionale    | 1            | string

Esempio:

```xml
<executable>/usr/local/bin/pdftotext</executable>
```

#### xpdf -> pdftotext -> xpdfrc
Definisce un path diverso per la xpdfrc da utilizzare nella "pdftotext".

**_Se non definito, IFile utilizza la configurazione presente nella libreria._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
xpdfrc       | opzionale    | 1            | string

Esempio:

```xml
<xpdfrc>/usr/local/var/www/xpdfrc</xpdfrc>
```

#### xpdf -> pdfinfo
Contiene i tag per la configurazione della componente binaria di terze parti "pdfinfo"

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
pdfinfo      | opzionale    | 1            | ComplexType - sequence

Esempio:

```xml
<pdfinfo>
    <executable>...</executable>
    <xpdfrc>...</xpdfrc>
</pdfinfo>
```

#### xpdf -> pdfinfo -> xpdfrc
Definisce il path per la componente binaria di terze parti "pdfinfo". 

**_Se non definito, IFile cerca di default di utilizzare la componente presente nella libreria in funzione del sistema operativo._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
executable   | opzionale    | 1            | string

Esempio:

```xml
<executable>/usr/local/bin/pdfinfo</executable>
```

#### xpdf -> pdfinfo -> executable
Definisce un path diverso per la xpdfrc da utilizzare nella "pdfinfo".

**_Se non definito, IFile utilizza la configurazione presente nella libreria._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
xpdfrc       | opzionale    | 1            | string

Esempio:

```xml
<xpdfrc>/usr/local/var/www/xpdfrc</xpdfrc>
```

### zend-document
Contiene i TAG per la configurazione dei parametri per la ZendSearch\Lucene\Document

**_Se non settato IFile utilizza i valori di deafult della ZendSearch\Lucene\Document._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
zend-document| opzionale    | 1            | ComplexType - sequence

Esempio:

```xml
<zend-document>
    <fields>
        <field name="..." type="..." />
        ...						
    </fields>		
</zend-document>
```

#### zend-document -> fields
Contenitore per la configurazione dei Field. I tag field sono un elenco sequenziale di tag.

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
fields       | opzionale    | 1            | ComplexType - sequence

Esempio: 

```xml
<fields>
    <field name="..." type="..." />
    ...						
</fields>		
```

#### zend-document -> fields -> field
Permette di configurare i field standard di IFile.

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
field        | obbligatorio | n            | ComplexType 

Esempio:

```xml
<field name="title" type="UnStored" encoding="UTF-8"/>
```

Attributo    | Proprietà    | Tipo          | Descizione
------------ | ------------ | ------------- | ------------ 
name         | obbligatorio | enumeration   | Elenco dei Field ammessi per la configurazione, field "Standard" utilizzati da IFile 
type         | obbligatorio | enumeration   | Elenco dei tipi di indicizzazione dei fields permessi in Lucene, [maggiori dettagli](https://framework.zend.com/manual/1.10/en/zend.search.lucene.overview.html) 
encoding     | opzionale    | enumeration   | Elenco degli encoding da utilizzare sul field 

_Valori ammessi - Attributo "name":_
 - name
 - extensionfile
 - path
 - filename
 - introtext			
 - body
 - title
 - subject
 - description
 - creator
 - keywords
 - created
 - modified
 
_Valori ammessi - Attributo "type":_  
 - Keyword
 - UnIndexed
 - Binary
 - Text
 - UnStored
 
**_Se si sta utilizzando come SearchEngine MySql sarà necessario obbligatoriamente definire i seguenti field di tipo "text"_**
 
 - name
 - path
 - filename

Esempio:

```xml
<zend-document>
    <fields>			
        <field name="name" type="Text" />
        <field name="path" type="Text" />
        <field name="filename" type="Text" />			
    </fields>		
</zend-document>
```

_Valori ammessi - Attributo "type":_
 - UTF-8
 - ASCII
 - ISO8859-1
 - ISO8859-15
 - ISO8859-2
 - ISO8859-7
 - CP1256
 - Windows-1252
 
### analyzer
Contiene i TAG per la gestione degli analizatori e dei filtri del testo da indcizzare.

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
analyzer     | opzionale    | 1            | ComplexType - all

Esempio: 

```xml
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
```

#### analyzer -> type
Contiene due TAG "ALTERNATIVI" per la gestione del tipo di analyzer.
 
**_Se non settato si prende come parametro di default: Utf8_CaseInsensitive._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
type         | opzionale    | 1            | ComplexType - choise

Esempio: 

```xml
<type>
    <default>...</default>
    <custom-default class="...">...</custom-default>				
</type>
```

#### analyzer -> type -> default
Configurazione del tipo di analyzer implementati nella ZendSearch, [maggiori dettagli](http://framework.zend.com/manual/en/zend.search.lucene.extending.html).
 
**_Il tag è alternativo con il tag <custom-default>_** 
 
Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
default      | opzionale    | 1            | enumeration

_Valori ammessi - Tag "default":_
 - Text
 - TextNum
 - Text_CaseInsensitive
 - TextNum_CaseInsensitive
 - Utf8
 - Utf8Num
 - Utf8_CaseInsensitive
 - Utf8Num_CaseInsensitive
 
Esempio: 

```xml
<default>Utf8Num_CaseInsensitive</default>
```

#### analyzer -> type -> custom-default
Configurazione del namespace della classe che estende ZendSearch\Lucene\Analysis\Analyzer, [maggiori dettagli](http://framework.zend.com/manual/en/zend.search.lucene.extending.html)

**_Il tag è alternativo con il tag <default>_** 

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
custom-default | opzionale    | 1            | string

Attributo    | Proprietà    | Tipo          | Descizione
------------ | ------------ | ------------- | ------------ 
class        | obbligatorio | string        | Nome della classe 

Esempio: 

```xml
<custom-default class="TestAnalyzer">Isappit\Ifile\CustomAnalyzer</custom-default>
```

In IFile è presente una cartella vuota "CustomAnalyzer" utilizzabile per la gestione di questi Analyzer personalizzati.


#### analyzer -> filters
Contiene i TAG per la gestione del filtraggio dei Token (termini) da indicizzare
 
**_Se non settato IFile utilizza i filtri standard di ZendSearch._**

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
filters      | opzionale    | 1            | ComplexType - all

Esempio: 

```xml
<filters>
    <stop-words>...</stop-words>
    <short-words>...</short-words>
    <custom-filters>
        <filter class="...">...</filter>
        ...
    </custom-filters>
</filters>
```

#### analyzer -> filters -> stop-words
Configurazione del path del file delle parole che non possono essere utilizzate come Token.
Il file deve avere l'estensione .txt e i termini devono essere separati da una "ritorno a capo" (LF)

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
stop-words   | opzionale    | 1            | string

Esempio:

```xml
<stop-words>/Users/isapp/Sites/personal/github/stopwords.txt</stop-words>
```

#### analyzer -> filters -> short-words
Configurazione del numero minimo di caratteri del Token.

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
short-words   | opzionale    | 1            | integer

Esempio:

```xml
<short-words>3</short-words>
```

#### analyzer -> custom-filters
Contenitore dei TAG per la gestione di filtri personalizzati.
 
Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
custom-filters | opzionale    | 1            | ComplexType - all

Esempio: 

```xml
<custom-filters>
    <filter class="...">...</filter>
    ...
</custom-filters>
```

#### analyzer -> custom-filters -> filter
Configurazione del namespace della classe che estende ZendSearch\Lucene\Analysis\TokenFilter, [maggiori dettagli](http://framework.zend.com/manual/en/zend.search.lucene.extending.html)

Tag          | Proprietà    | Occorrenza   | Tipo
------------ | ------------ | ------------ | -------------
filter       | opzionale    | 1            | string

Attributo    | Proprietà    | Tipo          | Descizione
------------ | ------------ | ------------- | ------------ 
class        | obbligatorio | string        | Nome della classe 

Esempio: 

```xml
<filter class="EnglishPorterStemmer">Isappit\Ifile\Tokenfilter\Stemming\English</filter>
``` 

###### Stemmer
IFile definisce già dei filtri personalizzati per lo "Stemmer". 
Le classi sono presenti nella cartella "TokenFilter/" della libreria IFile.

**_IMPORTANTE:_**
La maggior parte degli Stemmer necessitano della libreria "PECL Stem library" 

Di seguito la tabella con gli stemmer implementati in IFile.

Class        | Namespace  
------------ | ------------ 
DanishPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Danish 
DutchPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Dutch 
EnglishPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\English 
EnglishPorterStemmer | Isappit\Ifile\Tokenfilter\Stemming\English 
FinnishPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Finnish 
FrenchPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\French 
GermanPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\German 
HungarianPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Hungarian 
ItalianPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Italian 
NorwegianPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Norwegian 
PorterPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Porter 
PortuguesePECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Portuguese 
RomanianPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Romanian 
RussianPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Russian 
RussianUnicodePECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Russian 
SpanishPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Spanish 
SwedishPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Swedish 
TurkishPECLStemmer | Isappit\Ifile\Tokenfilter\Stemming\Turkish 

