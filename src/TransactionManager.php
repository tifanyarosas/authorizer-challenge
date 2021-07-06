<?php

namespace Authorizer;

use Authorizer\violations\CardIsNotActiveViolation;
use Authorizer\violations\DoubleTransactionViolation;
use Authorizer\violations\HighFrecuencyTransactionViolation;
use Authorizer\violations\InsufficientLimitViolation;

class TransactionManager {

    private static $instance = null;
    private $transactions = [];
  
    private function __construct() {}
 
    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new TransactionManager();
        }
        return self::$instance;
    }

    function makeTransaction(Account $account, string $merchant, int $amount, \DateTime $time): OperationResult {
        $transaction = new Transaction($account, $merchant, $amount, $time);
        $violations = $this->validateTransaction($account, $transaction);
        if (empty($violations)) {
            $account->setAvaliableLimit($account->getAvaliableLimit() - $amount);
            $this->addTransactionToList($transaction);
        }
        return new OperationResult($account, $violations);
    }

    private function validateTransaction(Account $account, Transaction $transaction): array {
        $violations = [];
        if (!$account->getActiveCard()) {
            $violations[] = new CardIsNotActiveViolation();
        }
        if (!$this->hasEnoughLimit($account, $transaction)) {
            $violations[] = new InsufficientLimitViolation();
        }
        if ($this->isDoubleTransaction($transaction)) {
            $violations[] = new DoubleTransactionViolation();
        }
        if ($this->isHighFrecuencyTransaction($transaction)) {
            $violations[] = new HighFrecuencyTransactionViolation();
        }
        return $violations;
    }

    private function hasEnoughLimit(Account $account, Transaction $transaction) {
        return $account->getAvaliableLimit() - $transaction->getAmount() >= 0;
    }

    private function isDoubleTransaction(Transaction $transaction): bool {
        if (empty($this->transactions) || 
        !($lastTransaction = $this->getLastTransaction($transaction->getMerchant(), $transaction->getAccount()))) {
            return false;
        }
        if ($this->getTimeDifferenceInMinutes($transaction, $lastTransaction) < 2) {
            return true;
        }
        return false;
    }

    private function getLastTransaction(string $merchant, Account $account) {
        foreach(array_reverse($this->transactions) as $transaction) {
            if ($transaction->getMerchant() == $merchant && $transaction->getAccount() == $account) {
                return $transaction;
            }
        }
        return null;
    }

    private function isHighFrecuencyTransaction(Transaction $transaction): bool {
        $countTransactions = count($this->transactions);
        if ($countTransactions < 3) {
            return false;
        }
        $previousLastTransaction = $this->transactions[$countTransactions - 2];
        if ($this->getTimeDifferenceInMinutes($transaction, $previousLastTransaction) < 2) {
            return true;
        }
        return false;
    }

    private function getTimeDifferenceInMinutes(Transaction $t1, Transaction $t2): int {
        return abs(($t1->getTime()->getTimestamp() - $t2->getTime()->getTimestamp())) / 60;
    }

    private function addTransactionToList(Transaction $transaction) {
        $this->transactions[] = $transaction;
    }
}
