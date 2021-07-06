<?php

namespace Authorizer;

class OperationParser {

    function parseOperations(array $operationsData): array {
        $operations = [];
        foreach($operationsData as $operationData) {
            if (isset($operationData["account"])) {
                $operations[] = new CreateAccountOperation($operationData["account"]);
            } else {
                $operations[] = new TransactionOperation($operationData["transaction"]);
            }
        }
        return $operations;
    }
}