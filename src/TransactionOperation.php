<?php

namespace Authorizer;

class TransactionOperation extends Operation {

    function getType(): int {
        return self::TRANSACTION_OPERATION;
    }
}