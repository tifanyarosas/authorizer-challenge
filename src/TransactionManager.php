<?php

namespace Authorizer;

class TransactionManager {

    private static $instance = null;
    private $transactions;
    private $validator;
  
    private function __construct() {
        $this->transactions = [];
        $this->validator = new TransactionViolationsValidator();
    }
 
    static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new TransactionManager();
        }
        return self::$instance;
    }

    function makeTransaction(Account $account, string $merchant, int $amount, \DateTime $time): OperationResult {
        $transaction = new Transaction($account, $merchant, $amount, $time);
        $violations = $this->validator->validateTransaction($account, $transaction, $this->transactions);
        if (empty($violations)) {
            $account->setAvaliableLimit($account->getAvaliableLimit() - $amount);
            $this->addTransactionToList($transaction);
        }
        return new OperationResult($account, $violations);
    }

    private function addTransactionToList(Transaction $transaction) {
        $this->transactions[] = $transaction;
    }

    function getTransactions(): array {
        return $this->transactions;
    }

    function setTransactions(array $transactions) {
        $this->transactions = $transactions;
    }

    function setTransactionValidator(TransactionValidator $validator) {
        $this->validator = $validator;
    }
}
