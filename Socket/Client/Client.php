<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 05/06/17
 * Time: 20:50
 */

namespace Socket\Client;
use \Socket\Socket;
use \Socket\Listen\Listen;

class Client extends Socket
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        $this->shutdownSocket       ($this->getSocket());
        $this->closeSocket          ($this->getSocket());
    }

    public function init(Listen $listenSocket)
    {
        $this->setSocket            ($this->acceptSocket($listenSocket->getSocket()));
        $this->setNonBlock          ($this->getSocket());
        $this->setRemoteName        ($this->getPeerName($this->getSocket()));
        $this->setSocketOption      ($this->getSocket(), SOL_SOCKET, SO_LINGER, array('l_linger' => 0, 'l_onoff' => 1));
        $this->setId                ($this->getRemoteName());
        $this->setConnectTime       (new \DateTime());
        $this->setLastResponseTime  ($this->getConnectTime());
    }

    public function pollThenRead()
    {
        if ($this->pollSocket()){
            $data = $this->readFromClient();
            if ($data){
                $this->setLastReadFromClient($data);
                $this->setLastResponseTime(new \DateTime());
                return $data;
            }
        }
        return false;
    }

    protected function readFromClient()
    {
        $data = '';
        try {
            while($buffer = $this->recvSocket($this->getSocket())){
                $data .= $buffer;
            }
            return $data;
        } catch (\RuntimeException $rE){
            echo $rE->getMessage().PHP_EOL;
            return false;
        }
    }

    public function writeToClient($data)
    {
        $this->sendSocket($this->getSocket(), $data);
    }
}