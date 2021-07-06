<?php 

use PHPUnit\Framework\TestCase;
use Authorizer\AccountCreator;
use Authorizer\exceptions\NegativeAvaliableLimitException;

class AccountTest extends TestCase {

    private $accountCreator;

    function __construct() {
        parent::__construct();
        $this->accountCreator = AccountCreator::getInstance();
    }

    function tearDown(): void {
        $this->accountCreator->setAccount(null);
    }

    function testSetPositiveAvaliableLimit() {
        $this->accountCreator->createAccount(true, 100);
        $account = $this->accountCreator->getAccount();
        $account->setAvaliableLimit(200);
        $this->assertEquals(200, $account->getAvaliableLimit());
    }

    function testSetZeroAvaliableLimit() {
        $this->accountCreator->createAccount(true, 100);
        $account = $this->accountCreator->getAccount();
        $account->setAvaliableLimit(0);
        $this->assertEquals(0, $account->getAvaliableLimit());
    }

    function testSetNegativeAvaliableLimit() {
        $this->accountCreator->createAccount(true, 100);
        $account = $this->accountCreator->getAccount();
        $this->expectException(NegativeAvaliableLimitException::class);
        $account->setAvaliableLimit(-20);
    }

    function testArrayRepresentation() {
        $accountCreator = AccountCreator::getInstance();
        $accountCreator->createAccount(true, 100);
        $account = $accountCreator->getAccount();
        $arrayRepExpected = [
            "activeCard" => true,
            "avaliableLimit" => 100
        ];
        $this->assertEquals($arrayRepExpected, $account->getArrayRepresentation());
    }
}
