<?php

namespace Authorizer;

use Authorizer\exceptions\NegativeAvaliableLimitException;

class Account {

    private $activeCard;
    private $avaliableLimit;

    function __construct(bool $activeCard, int $avaliableLimit = 0) {
        $this->activeCard = $activeCard;
        $this->avaliableLimit = $avaliableLimit;
    }

    function getActiveCard(): bool {
        return $this->activeCard;
    }

    function getAvaliableLimit(): int {
        return $this->avaliableLimit;
    }

    function setActiveCard(bool $active) {
        $this->activeCard = $active;
    }

    function setAvaliableLimit(int $limit) {
        if ($limit < 0) {
            throw new NegativeAvaliableLimitException();
        }
        $this->avaliableLimit = $limit;
    }

    function getArrayRepresentation(): array {
        return [
            "activeCard" => $this->getActiveCard(),
            "avaliableLimit" => $this->getAvaliableLimit()
        ];
    }
}