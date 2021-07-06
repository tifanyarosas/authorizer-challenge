<?php

namespace Authorizer\violations;

class AccountAlreadyInitializedViolation implements Violation {

    function getMessage(): string {
        return "account-already-initialized";
    }
}