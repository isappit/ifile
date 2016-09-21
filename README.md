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

```bash
composer require isappit/ifile
```

```json
{
    "minimum-stability": "RC",
    "prefer-stable": true,
    "require": {
        "isappit/ifile": "dev-master"
    }
}
```

IFile need the key *_"minimum-stability": "RC"_* because the ZendSearch dependence is implemented only in "RC" stability.

## Download Binaries files
If you want indexing PDF files you need:

 - Download the binaries files of third-parts from [here](#)
 - Configure the "binaries" folder [more detail](src/Config/xml/README.md)   

 Example:
 ```xml
 <binaries>/usr/local/var/ifile/binaries</binaries>
 ```

### LICENSE

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in [LICENSE](LICENSE).