<?php
namespace Authorizer;

class Transaction {

    private $merchant;
    private $amount;
    private $time;

    public function __construct(string $merchant, int $amount, \DateTime $time) {
        $this->merchant = $merchant;
        $this->amount = $amount;
        $this->time = $time;
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