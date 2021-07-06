<?php

namespace Authorizer;

class createAccountOperation extends Operation {

    function getType(): int {
        return self::ACCOUNT_CREATION_OPERATION;
    }
}