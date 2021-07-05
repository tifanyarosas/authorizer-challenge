<?php

namespace Authorizer\exceptions;

class AccountAlreadyInitializedException extends \Exception {

    function __construct() {
        parent::__construct("account-already-initialized");
    }
}