<?php

namespace Authorizer;

use Authorizer\exceptions\CardIsNotActiveException;

class TransactionManager extends Singleton {

    private $transactions = [];

    function makeTransaction(Account $account, string $merchant, int $amount, \DateTime $time) {
        $transaction = new Transaction($merchant, $amount, $time);
        if ($this->assertIsPossibleMakeTransaction($account, $transaction)) {
            $this->addTransactionToList($transaction);
        }
    }

    private function assertIsPossibleMakeTransaction(Account $account, Transaction $transaction): bool {
        if (!$account->getActivedCard()) {
            throw new CardIsNotActiveException();
        }
        if (!$this->hasEnoughLimit($account, $transaction)) {
            throw new InsufficientLimitException();
        }
        if ($this->isDoubleTransaction()) {
            throw new DoubleTransactionException();
        }
        if ($this->isHighFrecuencyTransaction()) {
            throw new HighFrecuencyTransactionException();
        }
    }

    private function hasEnoughLimit(Account $account, Transaction $transaction) {
        return $account->getAvaliableLimit() - $transaction->getAmount() >= 0;
    }

    private function isDoubleTransaction(): bool {
        return false;
    }

    private function isHighFrecuencyTransaction(): bool {
        return false;
    }

    private function addTransactionToList(Transaction $transaction) {
        $this->transactions[] = $transaction;
    }
}
