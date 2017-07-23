<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 04/06/17
 * Time: 21:11
 */
chdir(dirname(__FILE__));
(include_once 'System/AutoLoader.php') || die("Unable to initialise application");
echo "phpDGet initializing...".PHP_EOL;
try{
    $app    = new App\DumbTick();
    $logger = new \stdClass();
    $tcpIp  = new \Server\TcpServer($app, $logger);
    $tcpIp->init();
} catch (\Exception $e) {
    print_r($e);
}
unset($tcpIp);//Ensure resources are released
unset($app);
unset($logger);
echo "phpDGet closed.".PHP_EOL;
