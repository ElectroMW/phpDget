<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 08/06/17
 * Time: 22:04
 */

namespace Data;

class ClientCollection
{
    private $clients = array();

    public function setClientRead($id, $data)
    {
        if(!isset($this->clients[$id])) $this->initClient($id);
        $this->clients[$id]->setRead($data);
    }

    public function setClientWrite($id, $data)
    {
        if(!isset($this->clients[$id])) $this->initClient($id);
        $this->clients[$id]->setWrite($data);
    }

    public function setClientDisconnect($id)
    {
        if(!isset($this->clients[$id])) $this->initClient($id);
        return $this->clients[$id]->setDisconnect(true);
    }

    public function getClientRead($id)
    {
        if(!isset($this->clients[$id])) throw new \RuntimeException("Invalid client");
        return $this->clients[$id]->getRead();
    }

    public function getClientWrite($id)
    {
        if(!isset($this->clients[$id])) throw new \RuntimeException("Invalid client");
        return $this->clients[$id]->getWrite();
    }

    public function getClientDisconnect($id)
    {
        if(!isset($this->clients[$id])) throw new \RuntimeException("Invalid client");
        return $this->clients[$id]->getDisconnect();
    }

    protected function initClient($id)
    {
        $this->clients[$id] = new Data();
        $this->clients[$id] ->setId($id);
        $this->clients[$id] ->setRead(null);
        $this->clients[$id] ->setWrite(null);
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