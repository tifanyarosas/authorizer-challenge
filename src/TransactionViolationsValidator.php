<?php

namespace Authorizer;
use Authorizer\violations\CardIsNotActiveViolation;
use Authorizer\violations\DoubleTransactionViolation;
use Authorizer\violations\HighFrecuencyTransactionViolation;
use Authorizer\violations\InsufficientLimitViolation;

class TransactionViolationsValidator implements TransactionValidator {

    function validateTransaction(Account $account, Transaction $transaction, array $transactions): array {
        $violations = [];
        if (!$account->getActiveCard()) {
            $violations[] = new CardIsNotActiveViolation();
        }
        if (!$this->hasEnoughLimit($account, $transaction)) {
            $violations[] = new InsufficientLimitViolation();
        }
        if ($this->isDoubleTransaction($transaction, $transactions)) {
            $violations[] = new DoubleTransactionViolation();
        }
        if ($this->isHighFrecuencyTransaction($transaction, $transactions)) {
            $violations[] = new HighFrecuencyTransactionViolation();
        }
        return $violations;
    }

    protected function hasEnoughLimit(Account $account, Transaction $transaction) {
        return $account->getAvaliableLimit() - $transaction->getAmount() >= 0;
    }

    protected function isDoubleTransaction(Transaction $transaction, array $transactions): bool {
        if (empty($transactions) || 
        !($lastTransaction = $this->getLastTransaction($transaction->getMerchant(), $transaction->getAccount(), $transactions))) {
            return false;
        }
        if ($this->getTimeDifferenceInMinutes($transaction, $lastTransaction) < 2) {
            return true;
        }
        return false;
    }

    private function getLastTransaction(string $merchant, Account $account, array $transactions) {
        foreach(array_reverse($transactions) as $transaction) {
            if ($transaction->getMerchant() == $merchant && $transaction->getAccount() == $account) {
                return $transaction;
            }
        }
        return null;
    }

    protected function isHighFrecuencyTransaction(Transaction $transaction, array $transactions): bool {
        $countTransactions = count($transactions);
        if ($countTransactions < 3) {
            return false;
        }
        $previousLastTransaction = $transactions[$countTransactions - 2];
        if ($this->getTimeDifferenceInMinutes($transaction, $previousLastTransaction) < 2) {
            return true;
        }
        return false;
    }

    private function getTimeDifferenceInMinutes(Transaction $t1, Transaction $t2): int {
        return abs(($t1->getTime()->getTimestamp() - $t2->getTime()->getTimestamp())) / 60;
    }
}