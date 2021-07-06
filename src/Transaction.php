<?php
namespace Authorizer;

class Transaction {

    private $account;
    private $merchant;
    private $amount;
    private $time;

    public function __construct(Account $account, string $merchant, int $amount, \DateTime $time) {
        $this->account = $account;
        $this->merchant = $merchant;
        $this->amount = $amount;
        $this->time = $time;
    }

    function getAccount(): Account {
        return $this->account;
    }

    function getAmount(): int {
        return $this->amount;
    }

    function getMerchant(): string {
        return $this->merchant;
    }

    function getTime(): \DateTime {
        return $this->time;
    }
}