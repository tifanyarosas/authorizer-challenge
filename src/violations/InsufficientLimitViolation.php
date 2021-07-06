<?php

namespace Authorizer\violations;

class InsufficientLimitViolation implements Violation {

    function getMessage(): string {
        return "insufficient-limit";
    }
}