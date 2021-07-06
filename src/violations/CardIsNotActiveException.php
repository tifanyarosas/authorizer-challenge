<?php

namespace Authorizer\violations;

class CardIsNotActiveViolation implements Violation {

    function getMessage(): string {
        return "card-not-active";
    }
}