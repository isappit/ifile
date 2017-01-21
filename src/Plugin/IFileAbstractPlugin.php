<?php
namespace Isappit\Ifile\Plugin;

use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

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
 * Classe astratta per la gestione dei plugin
 *
 * @category   IndexingFile
 * @package    ifile
 * @subpackage servercheck
 * @author 	   Giampaolo Losito, Antonio Di Girolomo
 * @copyright
 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 2.1, February 1999
 */
abstract class IFileAbstractPlugin implements EventManagerAwareInterface, IFilePluginInterface {

    protected $events;

    /**
     * Evento prima dell'agginta di un documento nell'indice
     *
     * @param \Zend\EventManager\Event $e
     * @return mixed
     */
    public function onDocumentBeforeAdd(Event $e)
    {
        // TODO: Implement onDocumentBeforeAdd() method.
    }

    /**
     * Inietta un EventManager instance
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers([
            __CLASS__,
            get_class($this)
        ]);
        $this->events = $events;
    }

    /**
     * Ritorna event manager
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (! $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}