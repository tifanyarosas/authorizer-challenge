<?php

namespace Authorizer;

abstract class Operation {

    const ACCOUNT_CREATION_OPERATION = 1;
    const TRANSACTION_OPERATION = 2;

    abstract function getType(): int;

    function __construct(array $data) {
        $this->data = $data;
    }

    function getData(): array {
        return $this->data;
    }
}