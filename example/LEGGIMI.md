Examples: 
=====================================================
Utilizzando questi script di esempio è possibile capire come utilizzare IFile.

Si possono richiamare gli esempi "IFile_Ex" dal 01 al 12 se si vuole usare Lucene Search Engine oppure
"IFile_Mysql_EX" dal 01 al 11 (fino ad unidici perchè la FullText di MySql non permette ricerche per "range"), se invece si vuole usare la Search Engine FullText di MySql 

Gli esempi 01 e 02, indicizzano i documenti che sono presenti nella cartella "myfiles".

Gli esempi 03 e 04, creano documenti nell'indice in modo manuale e con la presenza di campi personalizzati.

Tutti gli altri servono a lavorare sull'indice per la ricerca dei termini e la gestione dei documenti.

Ricorda che è possibile definire la configurazione di IFile in due modi, nei nostri esempi è utilizzato il primo metodo, ma è presente anche come utilizzare il secondo:
1. usando il file "Config/IFileConfig.xml" presente nel vendor (non raccomandato se si effettua un update del pacchetto) 
2. configurando un file XML di configurazione esterno al "vendor" e settando questo prima di istanziare la classe IFileFactory


Esempio configurazione (2):
```php

    // Define external configuration file ( if not defined, IFile use: src/Config/xml/IFileConfig.xml in vendor )
    $fileConfig = "/Users/isapp/Sites/personal/github/ifile/IFileConfigMySql.xml";
    
    // try/catch
    try {
    	// IMPORTANT: 
    	// if use a external Configuration file is need to set external configuration file first to instance IFileFactory
    	IFileConfig::setXmlConfig($fileConfig);
    	
    	// instance IFileFactory
    	$IFileFactory = IFileFactory::getInstance();
    	.....
```
### Configurare MySql FullTexc come Engine

Se si vuole utilizzare la FullText di MySql come Search Engine è importante definire nel file di configurazione, [maggiori dettagli](https://github.com/isappit/ifile/blob/master/src/Config/xml/LEGGIMI.md):
- il tag "table-name"  

```xml
<table-name collation="utf8_general_ci">ifile_index_table</table-name>
```
 - E i campi "name", "path" e "filename" di tipo testo "Text":
 
```xml
   <zend-document>
		<fields>			
			<field name="name" type="Text" />
			<field name="path" type="Text" />
			<field name="filename" type="Text" />			
		</fields>		
	</zend-document>
```