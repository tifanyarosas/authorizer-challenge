<?php

namespace Authorizer\violations;

class HighFrecuencyTransactionViolation implements Violation {

    function getMessage(): string {
        return "high-frequency-small-interval";
    }
}