# IFile configuration

This file describes how to properly configure Ifile.

**Important:**

1. The configuration file is in "src/Config/xml/IFileConfig.xml". Since version 2.0 you can also use an external XML file to the library (see IFile documentation)
2. The configuration file is validated by the XSD file "Config/xml/IFileConfig.xsd

## STRUCTURE
 
 ```xml
 <ifile>
 	<root-application>...</root-application>
 	<binaries>...</binaries>
 	<table-name collation="..." engine="...">...</table-name>
 	<timelimit>...</timelimit>
	<memorylimit>...</memorylimit>
	<resultlimit>...</resultlimit>
	<default-search-field>...</default-search-field>	
	<duplicate>...</duplicate>
	<server bit="..." />
 	<encoding>...</encoding>
	<doctotxt encoding="..." type="..." >...</doctotxt>
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
 
## DESCRIPTION XML TAG

### ifile
This is the root of xml file configuration

```xml
<ifile>...</ifile>
```

Tag          | Property     | Occurrences   | Type
------------ | ------------ | ------------ | -------------
ifile        |  mandatory   | 1            | ComplexType - all

### root-application
Configure the path-root application.

This tag is very helpful if you want migrate your index in other application or different enviroment or server.
IFile define relative path of the document indexing. 

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
root-application |  mandatory | 1            | string 

Example:

```xml
<root-application>/usr/local/var/wwww/myproject</root-application>
```

### binaries
Configure the path of the binaries files. This tag is need to configure the correct path where the binaries files of 
third-parts components are installed. 

**_If not defined the default value is "src/Adapter/Helpers/ifile-binaries"._**

You need download the third-parts binaries files from [here](#).

Tag          | Property     | Occurrences   | Type
------------ | ------------ | ------------ | -------------
binaries     |  optional    | 1            | string 

Example:

```xml
<binaries>/usr/local/var/ifile/binaries</binaries>
```

### table-name
Configure the Table Name used from IFile for store the content and file information.

**_Defined only if you want use MySql FullText Engine how Search Engine_** 

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
table-name   |  optional    | 1            | string

Example:

```xml
<table-name collation="utf8_general_ci" engine="MyISAM">my_table</table-name>
```

Attribute    | Property    | Type          |  Description
------------ | ------------ | ------------- | ------------ 
collation    |  optional    | string        | Name of the collation to use for sorting associated with the charset
engine       |  optional    | string        | Name of the Engine Type to use for FullText ( MyISAM | InnoDB )

### timelimit
Configure the execusion time limit to the process parser. 

**_The minimum value accepted is 180 second._**

**_If not defined the default value is get from your PHP configuration (php.ini)._**

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
timelimit    |  optional    | 1            | integer

Example:

```xml
<timelimit>600</timelimit>
```

### memorylimit
Configure the memory limit (in MEGABYTE) used for the parsering and indexing process.  

**_If not defined the default value is get from your PHP configuration (php.ini)._**

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
memorylimit  |  optional    | 1            | integer

Example:

```xml
<memorylimit>512</memorylimit>
```

### resultlimit
Configure the maximum number of result that returned the query search. 

**_If not defined the query return all results._**

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
resultlimit  |  optional    | 1            | integer

Example:

```xml
<resultlimit>100</resutllimit>
```

### default-search-field
Configure the default search field that IFile used to search the terms. 

**_If not defined IFile search in all fields of the index._**

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
default-search-field  |  optional    | 1            | string

Example:

```xml
<default-search-field>body</default-search-field>
```

### encoding
Configure the type encoding. 

**_If not defined, default value is:_** _null_

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
encoding     |  optional    | 1            | enumeration

_Allowed values -  Attribute "encoding":_
 - UTF-8
 - ASCII
 - ISO8859-1
 - ISO8859-15
 - ISO8859-2
 - ISO8859-7
 - CP1256
 - Windows-1252

Example:

```xml
<encoding>UTF-8</encoding>
```

### duplicate
Configure if the document is unique in the index. 

If is setted to "zero" (0), IFile checked if the document exists in the index and invoke an exception if document exists.

If is setted to "one" (1) you can index many time the same document.

**_If not defined, default value is:_** _0_

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
duplicate    |  optional    | 1            | enumeration

 _Allowed values -  Attribute "duplicate":_
 - 0 (default)
 - 1

Example:

```xml
<duplicate>1</duplicate>
```

### server
Close tag to define the server type.

**_If not defined, default value is:_** _32_

Tag          | Property     | Occurrences   | Type
------------ | ------------ | ------------ | -------------
server       |  optional    | 1            | 

Attribute    | Property     | Type          |  Description
------------ | ------------ | ------------- | ------------ 
bit          |  mandatory   | enumaration   | It defines if the server is 32 or 64 bits, which is useful for proper 
use of XPDF and other third-parts components 

_Allowed values -  Attribute "bit":_
 - 32 (default)
 - 64

Example:

```xml
<server bit="64" />
```

### doctotxt
Close tag to define the parser type that IFile must use to get the content of the Microsoft Word Document (.doc)

**_If not defined, default value is:_** _PHP_

Tag          | Property     | Occurrences   | Type
------------ | ------------ | ------------ | -------------
doctotxt     |  optional    | 1            | ComplexType 

Attribute    | Property    | Type          |  Description
------------ | ------------ | ------------- | ------------ 
encoding     |  optional    | string        | **_Used only for the ANTIWORD parser type_**. List of encoding types for the recovery of the content of Microsoft Word Document (.doc) 
type         |  mandatory   | enumeration   | List of types of parser to use for retrieving the content from Microsoft Word Document (.doc) 

_Allowed values -  Attribute "encoding":_
The allowed encoding are present in the folder "Adapter/Helpers/binaries/resources".
In the "encoding" attribute you must use the only name file without extension (see antiword example).   
 
_Allowed values -  Attribute "type":_
 - PHP (default)
 - COM
 - ANTIWORD

**type = "PHP"**

IFile uses an PHP class to get the content, this class support only Microsoft Word Document written with 8859.1 encoding.

Example:

```xml
<doctotxt type="PHP" />
```

**type = "COM"**

IFile uses the "COM" library. This library is available only on Windows Operation Sistem,
[more datail](http://php.net/manual/en/com.requirements.php)  

Example:

```xml
<doctotxt type="COM" />
```
**type = "ANTIWORD"**

IFile uses the third-part component ANTIWORD to get the content of the Microsoft Word Document -
[more detail](http://www.winfield.demon.nl/) 

You need define the "encoding" attribute using only the name of the file, for example if you want use the encoding 
file "UTF-8.txt", you mest set encoding="UTF-8"    

**_If "encoding" isn't defined ANTIWORD uses default value:_** _8859-1_

Remember that if you want use encoding in ANTIWORD you need download the "ifile-binaries" folder, 
[more detail](https://github.com/isappit/ifile-binaries) 

Example:

```xml
<doctotxt encoding="UTF-8" type="ANTIWORD" />
```

**_If you want use a custom executable installed in your server you need define the absolute path in tag "doctotxt"_**

```xml
<doctotxt encoding="UTF-8" type="ANTIWORD">/usr/local/bin/antiword</doctotxt>
```


### xpdf
Configure the third-part component XPDF.

_If you use an Operating System not supported to [ifile-binaries](https://github.com/isappit/ifile-binaries), 
you can install on your server the correct [XPDF]((http://www.foolabs.com/xpdf/)) for your OS and configure this 
executable file._

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
xpdf         |  optional    | 1            | ComplexType - sequence

Example:

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
Configure the password used to protect PDF files.

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
opw          |  optional    | 1            | string

Example:

```xml
<opw>38sh7s9#@9hs0</opw>
```

#### xpdf -> pdftotext
It contains the tag to configure the third-part component XPDF (pdftotext)

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
pdftotext    |  optional    | 1            | ComplexType - sequence

Example:

```xml
<pdftotext>
    <executable>...</executable>
    <xpdfrc>...</xpdfrc>
</pdftotext>
```

#### xpdf -> pdftotext -> executable
Configure the external XPDF (pdftotext) at [ifile-binaries](https://github.com/isappit/ifile-binaries). 
 
**_If not defined, IFile search the "pdftotext" binary component in the library._**

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
executable   |  optional    | 1            | string

Example:

```xml
<executable>/usr/local/bin/pdftotext</executable>
```

#### xpdf -> pdftotext -> xpdfrc
Configure the external XPDF configuration file at [ifile-binaries](https://github.com/isappit/ifile-binaries).

**_If not defined, IFile search the xpdfrc configuration file in the library._**

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
xpdfrc       |  optional    | 1            | string

Example:

```xml
<xpdfrc>/usr/local/var/www/xpdfrc</xpdfrc>
```

#### xpdf -> pdfinfo
It contains the tag to configure the third-part component XPDF (pdfinfo)

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
pdfinfo      |  optional    | 1            | ComplexType - sequence

Example:

```xml
<pdfinfo>
    <executable>...</executable>
    <xpdfrc>...</xpdfrc>
</pdfinfo>
```

##### xpdf -> pdfinfo -> executable
Configure the external XPDF (pdftotext) at [ifile-binaries](https://github.com/isappit/ifile-binaries). 
 
**_If not defined, IFile search the "pdftotext" binary component in the library._**


Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
executable   |  optional    | 1            | string

Example:

```xml
<executable>/usr/local/bin/pdfinfo</executable>
```

##### xpdf -> pdfinfo -> xpdfrc
Configure the external XPDF configuration file at [ifile-binaries](https://github.com/isappit/ifile-binaries).

**_If not defined, IFile search the xpdfrc configuration file in the library._**

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
xpdfrc       |  optional    | 1            | string

Example:

```xml
<xpdfrc>/usr/local/var/www/xpdfrc</xpdfrc>
```

### zend-document
It contains the tag to configure of the parameter of the ZendSearch\Lucene\Document

**_If not defined IFile uses the default valueu of the ZendSearch\Lucene\Document._**

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
zend-document|  optional    | 1            | ComplexType - sequence

Example:

```xml
<zend-document>
    <fields>
        <field name="..." type="..." />
        ...						
    </fields>		
</zend-document>
```

#### zend-document -> fields
Contain the configuration of the Field.

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
fields       |  optional    | 1            | ComplexType - sequence

Example: 

```xml
<fields>
    <field name="..." type="..." />
    ...						
</fields>		
```

##### zend-document -> fields -> field
Configure the Field used in IFile.

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
field        |  mandatory | n            | ComplexType 

Example:

```xml
<field name="title" type="UnStored" encoding="UTF-8"/>
```

Attribute    | Property     | Type          |  Description
------------ | ------------ | ------------- | ------------ 
name         |  mandatory   | enumeration   | List of Fields Name allowed 
type         |  mandatory   | enumeration   | List of types allowed in Lucene, [more detail](https://framework.zend.com/manual/1.10/en/zend.search.lucene.overview.html) 
encoding     |  optional    | enumeration   | List of encoding to use on the field 

_Allowed values -  Attribute "name":_
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
 
_Allowed values -  Attribute "type":_  
 - Keyword
 - UnIndexed
 - Binary
 - Text
 - UnStored
 
**_If you want use the Search Engine of MySql, you need defined the following field with type="text"_**
 
 - name
 - path
 - filename

Example:

```xml
<zend-document>
    <fields>			
        <field name="name" type="Text" />
        <field name="path" type="Text" />
        <field name="filename" type="Text" />			
    </fields>		
</zend-document>
```

_Allowed values -  Attribute "type":_
 - UTF-8
 - ASCII
 - ISO8859-1
 - ISO8859-15
 - ISO8859-2
 - ISO8859-7
 - CP1256
 - Windows-1252
 
### analyzer
It contains the tag to managment the analizer and filters.

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
analyzer     |  optional    | 1            | ComplexType - all

Example: 

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
It contains the tag to managment the analizer type.
 
**_If not defined, default value is:_** _Utf8_CaseInsensitive_

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
type         |  optional    | 1            | ComplexType - choise

Example: 

```xml
<type>
    <default>...</default>
    <custom-default class="...">...</custom-default>				
</type>
```

#### analyzer -> type -> default
Configure the default analizer type implemented in ZendSearch, [more detail](http://framework.zend.com/manual/en/zend.search.lucene.extending.html).
 
**_The tag is an alternative with <custom-default>_** 
 
Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
default      |  optional    | 1            | enumeration

_Allowed values - Tag "default":_
 - Text
 - TextNum
 - Text_CaseInsensitive
 - TextNum_CaseInsensitive
 - Utf8
 - Utf8Num
 - Utf8_CaseInsensitive
 - Utf8Num_CaseInsensitive
 
Example: 

```xml
<default>Utf8Num_CaseInsensitive</default>
```

#### analyzer -> type -> custom-default
Configure namespace of the class that extend ZendSearch\Lucene\Analysis\Analyzer,  [more detail](http://framework.zend.com/manual/en/zend.search.lucene.extending.html)

**_The tag is an alternative with <default>_** 

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
custom-default |  optional    | 1            | string

Attribute    | Property    | Type          |  Description
------------ | ------------ | ------------- | ------------ 
class        |  mandatory   | string        | Class name 

Example: 

```xml
<custom-default class="TestAnalyzer">Isappit\Ifile\CustomAnalyzer</custom-default>
```

In IFile exists a "CustomAnalyzer" used for the "custom analizer"


#### analyzer -> filters
It contains the tag for managment of the filter.

**_If not defined, IFile use the ZendSeach standard filter._**

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
filters      |  optional    | 1            | ComplexType - all

Example: 

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
Configure the absolute path of is stored the txt file with the list of stop words.

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
stop-words   |  optional    | 1            | string

Example:

```xml
<stop-words>/Users/isapp/Sites/personal/github/stopwords.txt</stop-words>
```

#### analyzer -> filters -> short-words
Configure the minimun number of character for the single token (term)

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
short-words   |  optional    | 1            | integer

Example:

```xml
<short-words>3</short-words>
```

#### analyzer -> custom-filters
It contains the tag for management the custom filters.

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
custom-filters |  optional    | 1            | ComplexType - all

Example: 

```xml
<custom-filters>
    <filter class="...">...</filter>
    ...
</custom-filters>
```

#### analyzer -> custom-filters -> filter
Configure namespace of the class that extend ZendSearch\Lucene\Analysis\TokenFilter,  [more detail](http://framework.zend.com/manual/en/zend.search.lucene.extending.html)

Tag          | Property    | Occurrences   | Type
------------ | ------------ | ------------ | -------------
filter       |  optional    | 1            | string

Attribute    | Property    | Type          |  Description
------------ | ------------ | ------------- | ------------ 
class        |  mandatory | string        | Nome della classe 

Example: 

```xml
<filter class="EnglishPorterStemmer">Isappit\Ifile\Tokenfilter\Stemming\English</filter>
``` 

#### plugins 
It contains the tag for management the pliugin
 
Tag          | Property     | Occurrences  | Type
------------ | ------------ | ------------ | -------------
plugins      | optional    | 1            | ComplexType - all

Example: 

```xml
<plugins>
    <plugin class="...">...</plugin>
    ...
</plugins>
```

#### plugins -> plugin
Configure namespace of the class that extend Isappit\Ifile\Plugin\IFileAbstractPlugin

Tag          | Property     | Occurrences  | Type
------------ | ------------ | ------------ | -------------
plugin       | optional     | 1            | string

Attribute    | Property     | Type          |  Description
------------ | ------------ | ------------- | ------------ 
class        | mandatory    | string        | Class name 

Esempio: 

```xml
<plugin class="MyPlugin">Isappit\Ifile\Plugin</plugin>
``` 

**_IMPORTANT:_**
The plugin must extend the abstract class "Isappit\Ifile\Plugin\IFileAbstractPlugin" and implement one or more core event 
 
 
#### Other

##### Core Event
###### Document
 - onDocumentBeforeAdd

###### Stemmer
IFile define the custom filters of type "Stemmer".
The class are in the "TokenFilter/" folder in the IFile library. 

**_IMPORTANT:_**

The Stemmer used the "PECL Stem Library" verify if this package is installed on you server to use this filters.

Below the table of the Stemmer implemented in IFile.

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
