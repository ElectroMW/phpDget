<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 07/06/17
 * Time: 20:09
 */

namespace Socket\Listen;


use Socket\Socket;

class Listen extends Socket
{

    public function __construct($address, $port)
    {
        parent::__construct();
        $this->setPort          ($port);
        $this->setAddress       ($address);
    }

    public function __destruct()
    {
        $this->closeSocket      ($this->getSocket());
    }

    public function init()
    {
        $this->setSocket        ($this->createSocket());
        $this->bindSocket       ($this->getSocket(), $this->getAddress(), $this->getPort());
        $this->listenSocket     ($this->getSocket());
        $this->setNonBlock      ($this->getSocket());
        $this->setSocketOption  ($this->getSocket(), SOL_SOCKET, SO_LINGER, array('l_linger' => 0, 'l_onoff' => 1));
    }
}