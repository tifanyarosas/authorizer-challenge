<?php

namespace Authorizer\exceptions;

class NegativeAvaliableLimitException extends \Exception {

    function __construct() {
        parent::__construct("Negative limit for account");
    }
}