<?php

namespace Authorizer;

interface TransactionValidator {

    function validateTransaction(Account $account, Transaction $transaction, array $transactions): array;
}