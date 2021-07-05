<?php

namespace Authorizer;
use Authorizer\Exceptions\AccountAlreadyInitializedException;
use Authorizer\Exceptions\InvalidOperationException;
use Authorizer\exceptions\ThereIsNotAccountException;

require './vendor/autoload.php';

class MainApp {

    private $accountCreator;
    private $transactionManager;

    function __construct(AccountCreator $accountCreator, TransactionManager $transactionManager) {
        $this->accountCreator = $accountCreator;
        $this->transactionManager = $transactionManager;
    }

    function processOperations(array $operations) {
        foreach($operations as $operation => $data) {
            switch ($operation) {
                case "account": 
                    try {
                        $account = $this->accountCreator->createAccount($data['activedCard'], $data['availableLimit']);
                        $result = [
                            "account" => $account->getJsonRepresentation(),
                            "violations" => []
                        ];
                    } catch (AccountAlreadyInitializedException $ex) {
                        $account = $this->accountCreator->getAccount();
                        $result = [
                            "account" => $account->getJsonRepresentation(),
                            "violations" => [$ex->getMessage()]
                        ];
                    }
                    break;
                case "transaction":
                    $account = $this->accountCreator->getAccount();
                    if (!$account) {
                        throw new ThereIsNotAccountException();
                    }
                    $result = $this->transactionManager->makeTransaction(
                        $account, 
                        $operation['merchant'], 
                        $operation['amount'], 
                        $operation['time']
                    );
                    break;
                default:
                    throw new InvalidOperationException();
                    break;
            }
            var_dump(json_encode($result)   );
        }
    }
}

$operations = [
    "account" => [
        "activedCard" => true,
        "availableLimit" => 100
    ]
];

$mainApp = new MainApp(AccountCreator::getInstance());
$mainApp->processOperations($operations);


//$input = trim(fgets(STDIN));