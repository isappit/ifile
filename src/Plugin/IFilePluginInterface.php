<?php
namespace Isappit\Ifile\Plugin;

use Zend\EventManager\Event;

/**
 * IFile framework
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage servercheck
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 * @version    2.0
 */

/**
 * Interfaccia pubblica per la gestione dei plugin
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage servercheck
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
interface IFilePluginInterface {

    /**
     * Evento prima dell'aggiunta di un documento nell'indice
     *
     * event: document
     *
     * @param \Zend\EventManager\Event $e
     * @return mixed
     */
    public function onDocumentBeforeAdd(Event $e);
}