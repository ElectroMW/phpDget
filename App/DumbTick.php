<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 08/06/17
 * Time: 22:25
 */

namespace App;
use Data\ClientCollection;
use System\Base;

class DumbTick extends Base
{
    public function tick(ClientCollection $clientData)
    {
      $reads = $this->reads($clientData);
      if ($reads){
          $writes = "";
          foreach ($reads as $read){
              $writes .= $read;
          }
          foreach($reads as &$read){
              $read = $writes;
          }
          $this->writes($clientData, $reads);
      }
    }

    private function writes(ClientCollection $clientsData, $writes)
    {
        $clientsData->rewind();
        if(false === $client = $clientsData->valid()){
            return false;
        }

        do{
            $client->setWrite($writes[$client->getId()]);
        }while($client = $clientsData->next());
    }

    private function reads(ClientCollection $dataFromClients)
    {
        $dataFromClients->rewind();
        if(false === $clientData = $dataFromClients->valid()){
            return false;
        }

        $clientReads = array();
        do{
            $clientReads[$clientData->getId()] = $clientData->getRead();
            $clientData->setRead(null);
        } while($clientData = $dataFromClients->next());

        return $clientReads;
    }
}