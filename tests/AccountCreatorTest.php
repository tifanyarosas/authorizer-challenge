<?php //declare(strict_types=1);

use Authorizer\Account;
use PHPUnit\Framework\TestCase;
use Authorizer\AccountCreator;

class AccountCreatorTest extends TestCase {

    private $accountCreator;

    function __construct() {
        parent::__construct();
        $this->accountCreator = AccountCreator::getInstance();
    }

    function tearDown(): void {
        $this->accountCreator->setAccount(null);
    }

    function testCreateAccount() {
        $this->accountCreator->createAccount(true, 100);
        $this->assertInstanceOf(Account::class, $this->accountCreator->getAccount());
        $this->assertTrue($this->accountCreator->getAccount()->getActiveCard());
        $this->assertEquals(100, $this->accountCreator->getAccount()->getAvaliableLimit());
    }

    function testCreateAccountWhenThereIsAlreadyOne() {
        $this->accountCreator->createAccount(true, 100);
        $operationResult = $this->accountCreator->createAccount(false, 200);
        $this->assertEquals("account-already-initialized", $operationResult->getViolations()[0]->getMessage());
    }
}
