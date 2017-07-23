<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 02/06/17
 * Time: 20:46
 */
namespace Server;
use Data\ClientCollection as DataClientCollection;
use System\Base;
use Socket\Listen\Listen;
use Socket\Client\Client;
use Socket\Client\ClientCollection as SocketClientCollection;



class TcpServer extends Base
{
    /**
     * TcpServer constructor.
     * @param $app
     * @param $logger
     */
    public function __construct($app, $logger)
    {
        parent::__construct();
        $this->setApp($app);
        $this->setLogger($logger);
    }

    public function __destruct()
    {
        $this->unsetClientCollection();
        $this->unsetListenSocket();
    }

    /**
     * @param int $address
     * @param int $port
     * @param int $initTimeout
     */
    public function init($address = 0, $port = 9876, $initTimeout = 60)
    {

        try{
            $this->setClientCollection(new SocketClientCollection());
            $this->setListenSocket(new Listen($address, $port));
        } catch(\RuntimeException $runtimeException){
            print_r($runtimeException);
            return;
        }

        $cnt = 0;
        do{
            try {
                $this->getListenSocket()->init();
                echo "Server Initialized".PHP_EOL;
                $this->run();
                $cnt = $initTimeout;
            } catch (\RuntimeException $rE) {
                sleep(1);
            }
        }while($initTimeout > ++$cnt);
    }

    private function run()
    {

        $cnt = 0;
        do {
            $soData = new DataClientCollection();
            /////////////////////////////////////////////////
            $cnt++;
            if ($cnt >= 180) {
                //return;
            }
            usleep(10000);
            ////////////////////////////////////////////////

            if ($this->getListenSocket()->pollSocket()){
                $this->initClient();
            } elseif ($this->getClientCollection()->getIsNoClients()) {
                continue;
            }

            $this->processClientReads($this->getClientCollection(), new \DateInterval('PT120S'), $soData);
            $this->getApp()->tick($soData);
            $this->processClientWrites($this->getClientCollection(), $soData);
            unset($soData);
        } while (true);
    }

    /**
     * @param ClientCollection|SocketClientCollection $collection
     * @param \DateInterval $timeout
     * @param DataClientCollection $soData
     */
    private function processClientReads(SocketClientCollection $collection, \DateInterval $timeout, DataClientCollection $soData)
    {
        $collection->rewind();
        if(false === $client = $collection->valid()){
            return;
        }

        do{
            if ($this->isClientTimedOut($client, $timeout)) {
                $soData->setClientDisconnect($client->getId());
                $collection->closeCurrentClient();
                continue;
            }
            $soData->setClientRead($client->getId(), $client->pollThenRead());
        } while($client = $collection->next());
    }

    private function processClientWrites(SocketClientCollection $collection, DataClientCollection $soData)
    {
        $collection->rewind();
        if(false === $client = $collection->valid()){
            return;
        }

        do{
            $write = $soData->getClientWrite($client->getId());
            if ($write === null) continue;
            $client->writeToClient($write);
        } while($client = $collection->next());
    }

    /**
     * @param Client $client
     * @param \DateInterval $timeout
     * @return bool
     */
    private function isClientTimedOut(Client $client, \DateInterval $timeout)
    {
        $now = new \DateTime();
        $clientTime = clone $client->getLastResponseTime();
        $clientTime->add($timeout);
        if ($clientTime < $now) {
            $peer = $client->getRemoteName();
            echo "Clients $peer disconnected - Timeout limit exceeded".PHP_EOL;
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    private function initClient()
    {
        try {
            $client = new Client();
            $client->init($this->getListenSocket());
            $this->getClientCollection()->addClient($client);
        } catch (\RuntimeException $rE) {
            print_r($rE->getMessage());
            return false;
        }
        //welcome message
        echo "Clients {$client->getRemoteName()} connected".PHP_EOL;
        return true;
    }
}