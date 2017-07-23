<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 04/06/17
 * Time: 20:51
 */

const DS = DIRECTORY_SEPARATOR;
spl_autoload_register(function(string $fQCN){
    $fName = implode( DS , explode('\\', $fQCN)) . '.php';
    if (file_exists($fName)){
        require($fName);
    } else {
        throw new \Exception("Unable to Load: ". $fName);
    }
});