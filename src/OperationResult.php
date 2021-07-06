<?php 

namespace Authorizer;

class OperationResult {

    private $account;
    private $violations;

    function __construct(Account $account, array $violations = []) {
        $this->account = $account;
        $this->violations = $violations;
    }

    function getJsonRepresentation() {
        $result = [
            "account" => $this->account->getArrayRepresentation(),
            "violations" => $this->getViolationMessages()
        ];
        return json_encode($result);
    }

    private function getViolationMessages(): array {
        return array_map(function($violation) {
            return $violation->getMessage();
        }, $this->violations);
    }
}