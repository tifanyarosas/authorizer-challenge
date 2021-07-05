<?php

namespace Authorizer\exceptions;

class InvalidOperationException extends \Exception {

    function __construct() {
        parent::__construct("Invalid operation type");
    }
}