<?php

namespace Authorizer;
use Authorizer\violations\AccountAlreadyInitializedViolation;

class AccountCreator {
    
    private static $instance = null;
    private $account;
  
    private function __construct() {}
 
    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new AccountCreator();
        }
        return self::$instance;
    }

    function createAccount(bool $activedCard, int $avaliableLimit): OperationResult {
        $violations = [];
        var_dump($this->account);
        if ($this->account != null) {
            $violations[] = new AccountAlreadyInitializedViolation();
            return new OperationResult($this->account, $violations);
        }
        $this->account = new Account($activedCard, $avaliableLimit);
        return new OperationResult($this->account, $violations);
    }

    function getAccount() {
        return $this->account;
    }
}
