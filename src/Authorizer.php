<?php

namespace Authorizer;
use Authorizer\exceptions\InvalidOperationException;
use Authorizer\exceptions\ThereIsNotAccountException;

require './vendor/autoload.php';

class MainApp {

    const ACCOUNT_CREATION_OPERATION = "account";
    const TRANSACTION_OPERATION = "transaction";

    private $accountCreator;
    private $transactionManager;

    function __construct(AccountCreator $accountCreator, TransactionManager $transactionManager) {
        $this->accountCreator = $accountCreator;
        $this->transactionManager = $transactionManager;
    }

    function processOperations(array $operations) {
        foreach($operations as $operation) {
            switch ($operation->getType()) {
                case Operation::ACCOUNT_CREATION_OPERATION: 
                    $result = $this->createAccountOperation($operation->getData());
                    break;
                case Operation::TRANSACTION_OPERATION:
                    $result = $this->makeTransactionOperation($operation->getData());
                    break;
                default:
                    throw new InvalidOperationException();
                    break;
            }
            var_dump($result);
        }
    }

    private function createAccountOperation(array $operationData): string {
        $operationResult = $this->accountCreator->createAccount(
            $operationData['activeCard'], 
            $operationData['availableLimit']
        );
        return $operationResult->getJsonRepresentation();
    }

    private function makeTransactionOperation(array $operationData): string {
        $account = $this->accountCreator->getAccount();
        if (!$account) {
            throw new ThereIsNotAccountException();
        }
        $operationResult = $this->transactionManager->makeTransaction(
            $account, 
            $operationData['merchant'], 
            $operationData['amount'], 
            new \DateTime($operationData['time'])
        );
        return $operationResult->getJsonRepresentation();
    }
}

$handle = fopen($argv[1], "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $operations[] = $json = json_decode($line, true); 
    }
    fclose($handle);
} else {
    // error opening the file.
} 
$parsedOperations = (new OperationParser())->parseOperations($operations);
//var_dump($parsedOperations);

$mainApp = new MainApp(AccountCreator::getInstance(), TransactionManager::getInstance());
$mainApp->processOperations($parsedOperations);


//$input = trim(fgets(STDIN));