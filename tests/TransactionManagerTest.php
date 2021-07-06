<?php

use Authorizer\Account;
use Authorizer\AccountCreator;
use Authorizer\Transaction;
use Authorizer\TransactionManager;
use Authorizer\TransactionValidator;
use Authorizer\TransactionViolationsValidator;
use Authorizer\violations\DoubleTransactionViolation;
use PHPUnit\Framework\TestCase;

class TransactionManagerTest extends TestCase {

    private $accountCreator;
    private $transactionManager;

    function __construct() {
        parent::__construct();
        $this->accountCreator = AccountCreator::getInstance();
        $this->transactionManager = TransactionManager::getInstance();
    }

    function tearDown(): void {
        $this->accountCreator->setAccount(null);
        $this->transactionManager->setTransactions([]);
    }

    function testMakeTransactionSuccesfully() {
        $validatorMock = $this->createPartialMock(TransactionValidator::class, ['validateTransaction']);
        $validatorMock->method('validateTransaction')->willReturn([]);
        $this->transactionManager->setTransactionValidator($validatorMock);
        
        $this->accountCreator->createAccount(true, 100);
        $account = $this->accountCreator->getAccount();

        $operationResult = $this->transactionManager->makeTransaction($account, "Merchant", 50, new \DateTime());
        $this->assertEquals(50, $account->getAvaliableLimit());
        $this->assertEquals([], $operationResult->getViolations());
        $this->assertEquals(1, count($this->transactionManager->getTransactions()));
    }

    function testMakeTransactionFail() {
        $validatorMock = $this->createPartialMock(TransactionValidator::class, ['validateTransaction']);
        $validatorMock->method('validateTransaction')->willReturn([new DoubleTransactionViolation()]);
        $this->transactionManager->setTransactionValidator($validatorMock);
        
        $this->accountCreator->createAccount(true, 100);
        $account = $this->accountCreator->getAccount();

        $operationResult = $this->transactionManager->makeTransaction($account, "Merchant", 50, new \DateTime());
        $this->assertEquals(100, $account->getAvaliableLimit());
        $this->assertEquals([new DoubleTransactionViolation()], $operationResult->getViolations());
        $this->assertEquals(0, count($this->transactionManager->getTransactions()));
    }
}