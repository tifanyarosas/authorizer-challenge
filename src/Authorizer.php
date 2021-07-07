<?php

namespace Authorizer;
use Authorizer\exceptions\InvalidOperationException;
use Authorizer\exceptions\ThereIsNotAccountException;
error_reporting(E_ERROR | E_PARSE);
require './vendor/autoload.php';

class Authorizer {

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
            echo $result . PHP_EOL;
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
if (!$handle) {
    echo 'Error opening operations file' . PHP_EOL;
    return;
}
while (($line = fgets($handle)) !== false) {
    $operations[] = $json = json_decode($line, true); 
}
fclose($handle);
    
$parsedOperations = (new OperationParser())->parseOperations($operations);
$authorizer = new Authorizer(AccountCreator::getInstance(), TransactionManager::getInstance());
$authorizer->processOperations($parsedOperations);