<?php
/**
 * Created by PhpStorm.
 * User: sam
 * Date: 04/06/17
 * Time: 19:51
 */
namespace System;
abstract class Base
{
    private $data;

    public function __construct()
    {
        $this->data = new \stdClass();
    }

    /**
     * @param $name
     * @param $args
     * @return bool
     */
    public function __call($name, $args)
    {
        if (preg_match('/get([a-zA-Z0-9_-]+)/', $name, $call) ) {
            if (isset($call[1])) {
                $property = lcfirst($call[1]);
                if (isset($this->data->$property)) {
                    return $this->data->$property;
                }
            }
            return false;
        }
        if (preg_match('/set([a-zA-Z0-9_-]+)/', $name, $call) && (count($args) == 1)) {
            if (isset($call[1])) {
                $property = lcfirst($call[1]);
                $this->data->$property = $args[0];
                return true;
            }
            return false;
        }
        if (preg_match('/unset([a-zA-Z0-9_-]+)/', $name, $call)) {
            if (isset($call[1])) {
                $property = lcfirst($call[1]);
                if (isset($this->data->$property)){
                    unset($this->data->$property);
                    return true;
                }
            }
            return false;
        }
    }
}