<?php

namespace Authorizer\exceptions;

class ThereIsNotAccountException extends \Exception {

    function __construct() {
        parent::__construct("There is not a created account");
    }
}