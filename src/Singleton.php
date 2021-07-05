<?php

namespace Authorizer;

abstract class Singleton {

    private static $instance = null;
  
    private function __construct() {
    }
 
    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
