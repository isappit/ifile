<?php
namespace Isappit\Ifile\Plugin;

use Zend\EventManager\Event;

/**
 * IFile framework
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    1.0 IFile_Indexing_Interface.php 2011-01-24 20:13:58
 */

/**
 * Interfaccia pubblica per la gestione dei plugin
 *
 * @category   IndexingFile
 * @package    ifile
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
interface IFilePluginInterface {

    /**
     * Evento prima dell'agginta di un documento nell'indice
     *
     * @param \Zend\EventManager\Event $e
     * @return mixed
     */
    public function onDocumentBeforeAdd(Event $e);
}