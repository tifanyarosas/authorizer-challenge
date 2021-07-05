<?php

namespace Authorizer;

class AccountCreator extends Singleton {
    
    private $account;

    function createAccount(bool $activedCard, int $avaliableLimit): Account {
        if ($this->account != null) {
            //throw AccountAlreadyInitializedException();
        }
        $this->account = new Account($activedCard, $avaliableLimit);
        return $this->account;
    }

    function getAccount() {
        return $this->account;
    }
}
