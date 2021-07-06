<?php

namespace Authorizer\violations;

class DoubleTransactionViolation implements Violation {

    function getMessage(): string {
        return "doubled-transaction";
    }
}