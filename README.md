![Logo](http://www.isapp.it/images/logo/Logo_isapp_it_250x87.png)

# Welcome to the *IFile 2.0* Release!

## RELEASE INFORMATION

*IFile 2.0dev*

Please see [CHANGELOG.md](CHANGELOG.md).

### SYSTEM REQUIREMENTS

IFile 2.0 requires PHP 5.3 or later; we recommend using the latest 
PHP version whenever possible.

### What is IFile?

IFile is a "Documental Search Engine", you can improve your applications written in PHP and allow to index and search 
within documents. Its facility of use allows to use it in any context or domain. 
One of the fundamental aspects on which is based is the ability to read texts in the documents as PDF, DOC or Excel 
(iFile supports more of the 25 formats) and search the terms present in the documents. 
The traceability of the contents of files in search results on its own website, emphasizes the importance of allowing 
the search of all content within own website. 

The our users, uses IFile for sites of University, Scientific Communities, School, Libraries, Newspapers and 
all web application or site that publish documents, images or audio files, to improve the user experience, 
and research on the site.

### Installation

The easiest way to install IFile project is to use [Composer](https://getcomposer.org/).  If you don't have it already installed,
then please install as per the [documentation](https://getcomposer.org/doc/00-intro.md).

```json
{
    "minimum-stability": "RC",
    "prefer-stable": true,
    "require": {
        "isappit/ifile": "2.x"
    }
}
```

IFile need that you configure the composer.json file with the key *_"minimum-stability": "RC"_* (or "dev") because the ZendSearch dependence is implemented only in "RC" stability.

#### How Download and use Binaries files in IFile
If you want use third-part component XPDF or ANTIWORD you can use this method:

##### Zip Archive
 - Download ZIP Archive Binaries Files from [here](https://github.com/isappit/ifile-binaries/archive/master.zip)
 - Unzip the package on your server
 - Copy the _"ifile-binaries"_ folder in _"vendor/isappit/ifile/src/Adapter/Helpers/"_ or configure IFile to read 
 the "ifile-binaries" folder from external path at IFile. [More detail](https://github.com/isappit/ifile/blob/master/src/Config/xml/README.md#binaries) 

 Example how configure external path:
```xml
 <binaries>/usr/local/var/ifile/ifile-binaries</binaries>
```

##### Clone project from git
 - Clone project 
 - Copy the _"ifile-binaries"_ folder in _"vendor/isappit/ifile/src/Adapter/Helpers/"_ or configure IFile to read 
 the "ifile-binaries" folder from external path at IFile. [More detail](https://github.com/isappit/ifile/blob/master/src/Config/xml/README.md#binaries)

Example clone project:
```bash
git clone https://github.com/isappit/ifile-binaries.git
```

##### Install and configure third-part component
If you don't want download and install "ifile-binaries" folder and you want use XPDF or ANTIWORD installed on your server,
you can configure the third-parts executable files in configuration.

For more detail how configure third-parts components in IFile see:
 - [XPDF](https://github.com/isappit/ifile/tree/master/src/Config/xml#xpdf)
 - [ANTIWORD](https://github.com/isappit/ifile/tree/master/src/Config/xml#doctotxt)

### Configuration
The configuration file is stored in "src/Config/xml/IFileConfig.xml", but we recommended you to configure external file 
at project and set this file configuration first to create the IFileFactory.

Example:
```php
    // Define external configuration file ( if not defined, IFile use: src/Config/xml/IFileConfig.xml in vendor )
    $fileConfig = "/Users/isapp/Sites/personal/github/ifile/IFileConfig.xml";
    
    // try/catch
    try {
    	// IMPORTANT: 
    	// if use a external Configuration file is need to set external configuration file first to instance IFileFactory
    	IFileConfig::setXmlConfig($fileConfig);
    	
    	// instance IFileFactory
    	$IFile = IFileFactory::getInstance();
    	.....
```

For more detail how configure IFile you can read the [README](https://github.com/isappit/ifile/tree/master/src/Config/xml)

### Documentation
The documentation of IFile is written only in "Italian" language (we hope translate in English in next release). 
You can read documentation from [here](http://www.isapp.it/en/documentazione-ifile.html), 
or you can [Donwload PDF](http://www.isapp.it/documentazione/IFile_Introduzione_1_2.pdf).

In IFile library exists also the *_example/_* folder were you can find many [examples](https://github.com/isappit/ifile/tree/master/example) 
to use correctly IFile.


### LICENSE

The files in this archive are released under the LGPL-3.0
You can find a copy of this license in [LICENSE](LICENSE).