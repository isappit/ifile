Examples: 
=====================================================
You can see how IFile works with this examples. 
You can call the example "IFile_Ex" from 01 to 12 if you want use Lucene Search Engine or "IFile_Mysql_EX" from 01 to 11 (in mysql not exist the range search for FullText Engine) if you want use FullText of MySql as Search Engine.

The examples, 01 and 02, indexing the documents stored in "myfiles" folder.
The examples, 03 and 04, create documents in index manually or with Custom Fields.
Other examples uses the index created to see how IFile search the terms in the index.  

Remember that you can define your configuration in two mode:
- use "Config/IFileConfig.xml" stored in the "vendor" (not recommended)
- you can configure external XML at the "vendor foder" and set this file configuration first to create the IFileFactory

Example:
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

### Configuring MySql FullText Engine

Remember that if you want use FullText of MySql as Search Engine is important define in the configuration file:
 - "Table Name", "Collaction" and Engine if you want use InnoDB and not MyISAM how engine:
 ```xml
 	<table-name collation="utf8_general_ci">ifile_index_table</table-name>
 ```
 - Fields: name, path, filename as "Text":
 ```xml
   <zend-document>
		<fields>			
			<field name="name" type="Text" />
			<field name="path" type="Text" />
			<field name="filename" type="Text" />			
		</fields>		
	</zend-document>
```