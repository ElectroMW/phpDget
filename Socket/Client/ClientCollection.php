<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 05/06/17
 * Time: 20:51
 */

namespace Socket\Client;
use \System\Base as Base;

class ClientCollection extends Base implements \Iterator
{
    private $clients        = array();

    public function __construct()
    {
        parent::__construct();
        $this->setIsNoClients(true);
    }

    public function __destruct()
    {
        unset($this->clients);
    }

    public function addClient(Client $client)
    {
        $this->clients[$client->getId()] = $client;
        $this->setIsNoClients(false);
    }

    public function removeClient($id)
    {
        if (isset($this->clients[$id])){
            unset($this->clients[$id]);
            if (!count($this->clients)) $this->setIsNoClients(true);
            return true;
        }
        return false;
    }

    public function closeCurrentClient()
    {
        $this->removeClient($this->current()->getId());
    }

    public function current()
    {
        return current($this->clients);
    }

    public function rewind()
    {
        reset($this->clients);
    }

    public function key()
    {
        return key($this->clients);
    }

    public function next()
    {
        return next($this->clients);
    }

    public function valid()
    {
        return current($this->clients);
    }
}