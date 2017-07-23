<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 04/06/17
 * Time: 19:43
 */
namespace Socket;
use Socket\Select\SelectSocketResponse;
use \System\Base as Base;

abstract class Socket extends Base
{

    public function __construct()
    {
        parent::__construct();
    }

    public function pollSocket()
    {
        $soAry = array($this->getSocket());
        if(empty($soAry)) return false;//Perhaps log here as this should not be the case.


        if ($this->selectSocket($soAry)){
            return true;
        }
        return false;
    }

    protected function createSocket()
    {
        if (false === $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) {
            throw new \RuntimeException("Unable to create socket " . socket_strerror(socket_last_error()));
        }
        return $socket;
    }

    protected function closeSocket($socket)
    {
        socket_close($socket);
    }

    protected function bindSocket($socket, $address, $port)
    {
        if (@socket_bind($socket, $address, $port) === false) {
            throw new \RuntimeException("Unable to bind to $address:$port: " . socket_strerror(socket_last_error($socket)));
        }
    }

    protected function listenSocket($socket, $backlog = 5)
    {
        if (socket_listen($socket, $backlog) === false) {
            throw new \RuntimeException("Unable to listen on socket " . socket_strerror(socket_last_error($socket)));
        }
    }

    protected function setNonBlock($socket)
    {
        if (socket_set_nonblock($socket) === false) {
            throw new \RuntimeException("Unable to set socket as non-blocking " . socket_strerror(socket_last_error($socket)));
        }
    }
    protected function setBlock($socket)
    {
        if (socket_set_block($socket)) {
            return true;
        }
        throw new \RuntimeException("Unable to set socket as blocking " . socket_strerror(socket_last_error($socket)));
    }

    protected function acceptSocket($socket)
    {
        if (false === $resource = socket_accept($socket)) {
            throw new \RuntimeException("Unable to accept client socket " . socket_strerror(socket_last_error($socket)));
        }
        return $resource;
    }

    protected function getPeerName($socket)
    {
        if (socket_getpeername($socket, $address, $port)) {
            return $address . ':' . $port;
        }
        throw new \RuntimeException("Unable to determine remote peer " . socket_strerror(socket_last_error($socket)));
    }

    protected function selectSocket($read = null, $write = null, $except = null, $tvSec = 0)
    {
        if(false === $resp = socket_select($read, $write, $except, $tvSec)){
            throw new \RuntimeException("Socket Select Error: " .socket_strerror(socket_last_error()));
        }
        if ($resp){
            $retObj                             = new SelectSocketResponse();
            $retObj->setCount($resp);
            if(!empty($read))   $retObj->setRead($read);
            if(!empty($write))  $retObj->setWrite($write);
            if(!empty($except)) $retObj->setExcept($except);
            return $retObj;
        }
        return false;
    }

    protected function shutdownSocket($socket, $how = 2)
    {
        if (socket_shutdown($socket, $how)){
            return true;
        }
        throw new \RuntimeException("Shutdown Socket Error: " .socket_strerror(socket_last_error($socket)));
    }

    protected function setSocketOption($socket, $level, $optname, $optvalue)
    {
        if(socket_set_option($socket, $level, $optname, $optvalue)){
            return true;
        }
        throw new \RuntimeException("Socket Set Option Error: " .socket_strerror(socket_last_error($socket)));
    }

    protected function recvSocket($socket, $len = 256000, $flags = 0 )
    {
        if(false !== $bytes = socket_recv($socket, $buf, $len, $flags)){
            return $buf;
        }
        if ($bytes == 0){
            return '';
        }
        throw new \RuntimeException("recv Socket Error: " .socket_strerror(socket_last_error($socket)));
    }

    protected function sendSocket($socket, $buf, $len = false, $flags = 0)
    {
        if ($len === false){
            $len = strlen($buf);
        }
        if (false === socket_send($socket, $buf, $len, $flags)){
            throw new \RuntimeException("Send Socket Error: " .socket_strerror(socket_last_error($socket)));
        }
        return true;
    }
}