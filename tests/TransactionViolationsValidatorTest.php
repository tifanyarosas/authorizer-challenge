<?php

use Authorizer\Account;
use Authorizer\Transaction;
use Authorizer\TransactionViolationsValidator;
use Authorizer\violations\CardIsNotActiveViolation;
use Authorizer\violations\DoubleTransactionViolation;
use Authorizer\violations\HighFrecuencyTransactionViolation;
use Authorizer\violations\InsufficientLimitViolation;
use PHPUnit\Framework\TestCase;

class TransactionViolationsValidatorTest extends TestCase {

    function testNotActiveCard() {
        $validatorMock = $this->createPartialMock(TransactionViolationsValidator::class, 
            ['hasEnoughLimit', 'isDoubleTransaction', 'isHighFrecuencyTransaction']);
        $validatorMock->method('hasEnoughLimit')->willReturn(true);
        $validatorMock->method('isDoubleTransaction')->willReturn(false);
        $validatorMock->method('isHighFrecuencyTransaction')->willReturn(false);

        $accountMock = $this->createPartialMock(Account::class, ['getActiveCard']);
        $accountMock->method('getActiveCard')->willReturn(false);

        $transactionMock = $this->createMock(Transaction::class);

        $this->assertEquals([new CardIsNotActiveViolation()], $validatorMock->validateTransaction($accountMock, $transactionMock, []));
    }

    function testHasNotEnoughLimit() {
        $validatorMock = $this->createPartialMock(TransactionViolationsValidator::class, 
            ['isDoubleTransaction', 'isHighFrecuencyTransaction']);
        $validatorMock->method('isDoubleTransaction')->willReturn(false);
        $validatorMock->method('isHighFrecuencyTransaction')->willReturn(false);

        $accountMock = $this->createPartialMock(Account::class, ['getActiveCard', 'getAvaliableLimit']);
        $accountMock->method('getActiveCard')->willReturn(true);
        $accountMock->method('getAvaliableLimit')->willReturn(100);

        $transaction = new Transaction($accountMock, "Merchant", 200, new \DateTime());

        $this->assertEquals([new InsufficientLimitViolation()], $validatorMock->validateTransaction($accountMock, $transaction, []));
    }

    function testIsDoubleTransaction() {
        $validatorMock = $this->createPartialMock(TransactionViolationsValidator::class, 
            ['hasEnoughLimit', 'isHighFrecuencyTransaction']);
        $validatorMock->method('isHighFrecuencyTransaction')->willReturn(false);
        $validatorMock->method('hasEnoughLimit')->willReturn(true);

        $accountMock = $this->createPartialMock(Account::class, ['getActiveCard']);
        $accountMock->method('getActiveCard')->willReturn(true);

        $transactions[] = new Transaction($accountMock, "Merchant", 200, new \DateTime('2021-07-06 10:00:00'));

        $transaction = new Transaction($accountMock, "Merchant", 100, new \DateTime('2021-07-06 10:01:00'));

        $this->assertEquals([new DoubleTransactionViolation()], $validatorMock->validateTransaction($accountMock, $transaction, $transactions));
    }

    function testIsNotDoubleTransactionWithDifferentMerchant() {
        $validatorMock = $this->createPartialMock(TransactionViolationsValidator::class, 
            ['hasEnoughLimit', 'isHighFrecuencyTransaction']);
        $validatorMock->method('isHighFrecuencyTransaction')->willReturn(false);
        $validatorMock->method('hasEnoughLimit')->willReturn(true);

        $accountMock = $this->createPartialMock(Account::class, ['getActiveCard']);
        $accountMock->method('getActiveCard')->willReturn(true);

        $transactions[] = new Transaction($accountMock, "Asd", 200, new \DateTime('2021-07-06 10:00:00'));

        $transaction = new Transaction($accountMock, "Merchant", 100, new \DateTime('2021-07-06 10:01:00'));

        $this->assertEquals([], $validatorMock->validateTransaction($accountMock, $transaction, $transactions));
    }

    function testIsNotDoubleTransactionWithIntervalTimeGratherThanTwo() {
        $validatorMock = $this->createPartialMock(TransactionViolationsValidator::class, 
            ['hasEnoughLimit', 'isHighFrecuencyTransaction']);
        $validatorMock->method('isHighFrecuencyTransaction')->willReturn(false);
        $validatorMock->method('hasEnoughLimit')->willReturn(true);

        $accountMock = $this->createPartialMock(Account::class, ['getActiveCard']);
        $accountMock->method('getActiveCard')->willReturn(true);

        $transactions[] = new Transaction($accountMock, "Merchant", 200, new \DateTime('2021-07-06 10:00:00'));

        $transaction = new Transaction($accountMock, "Merchant", 100, new \DateTime('2021-07-06 10:05:00'));

        $this->assertEquals([], $validatorMock->validateTransaction($accountMock, $transaction, $transactions));
    }

    function testHighFrecuencyTransaction() {
        $validatorMock = $this->createPartialMock(TransactionViolationsValidator::class, 
            ['hasEnoughLimit', 'isDoubleTransaction']);
        $validatorMock->method('isDoubleTransaction')->willReturn(false);
        $validatorMock->method('hasEnoughLimit')->willReturn(true);

        $accountMock = $this->createPartialMock(Account::class, ['getActiveCard']);
        $accountMock->method('getActiveCard')->willReturn(true);

        $transactions[] = new Transaction($accountMock, "Merchant", 200, new \DateTime('2021-07-06 10:00:00'));
        $transactions[] = new Transaction($accountMock, "Asd", 100, new \DateTime('2021-07-06 10:01:00'));
        $transactions[] = new Transaction($accountMock, "Other", 50, new \DateTime('2021-07-06 10:01:20'));

        $transaction = new Transaction($accountMock, "Merchant", 100, new \DateTime('2021-07-06 10:02:00'));

        $this->assertEquals([new HighFrecuencyTransactionViolation()], $validatorMock->validateTransaction($accountMock, $transaction, $transactions));
    }

    function testIsNotHighFrecuencyTransactionWhenThereIsLessThan3Transactions() {
        $validatorMock = $this->createPartialMock(TransactionViolationsValidator::class, 
            ['hasEnoughLimit', 'isDoubleTransaction']);
        $validatorMock->method('isDoubleTransaction')->willReturn(false);
        $validatorMock->method('hasEnoughLimit')->willReturn(true);

        $accountMock = $this->createPartialMock(Account::class, ['getActiveCard']);
        $accountMock->method('getActiveCard')->willReturn(true);

        $transactions[] = new Transaction($accountMock, "Merchant", 200, new \DateTime('2021-07-06 10:00:00'));
        $transactions[] = new Transaction($accountMock, "Asd", 100, new \DateTime('2021-07-06 10:01:00'));

        $transaction = new Transaction($accountMock, "Merchant", 100, new \DateTime('2021-07-06 10:02:00'));

        $this->assertEquals([], $validatorMock->validateTransaction($accountMock, $transaction, $transactions));
    }

    function testIsNotHighFrecuencyTransactionWhenThereIntervalTimeIsGratherThan3() {
        $validatorMock = $this->createPartialMock(TransactionViolationsValidator::class, 
            ['hasEnoughLimit', 'isDoubleTransaction']);
        $validatorMock->method('isDoubleTransaction')->willReturn(false);
        $validatorMock->method('hasEnoughLimit')->willReturn(true);

        $accountMock = $this->createPartialMock(Account::class, ['getActiveCard']);
        $accountMock->method('getActiveCard')->willReturn(true);

        $transactions[] = new Transaction($accountMock, "Merchant", 200, new \DateTime('2021-07-06 10:00:00'));
        $transactions[] = new Transaction($accountMock, "Asd", 100, new \DateTime('2021-07-06 10:01:00'));

        $transaction = new Transaction($accountMock, "Merchant", 100, new \DateTime('2021-07-06 10:05:00'));

        $this->assertEquals([], $validatorMock->validateTransaction($accountMock, $transaction, $transactions));
    }

    function testSimultaneousViolations() {
        $validatorMock = $this->createPartialMock(TransactionViolationsValidator::class, 
            ['hasEnoughLimit', 'isDoubleTransaction', 'isHighFrecuencyTransaction']);
        $validatorMock->method('hasEnoughLimit')->willReturn(false);
        $validatorMock->method('isDoubleTransaction')->willReturn(false);
        $validatorMock->method('isHighFrecuencyTransaction')->willReturn(false);

        $accountMock = $this->createPartialMock(Account::class, ['getActiveCard']);
        $accountMock->method('getActiveCard')->willReturn(false);

        $transactionMock = $this->createMock(Transaction::class);

        $this->assertEquals([new CardIsNotActiveViolation(), new InsufficientLimitViolation()], $validatorMock->validateTransaction($accountMock, $transactionMock, []));
    }
}