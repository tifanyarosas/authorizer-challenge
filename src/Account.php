<?php

namespace Authorizer;

class Account {

    private $activedCard;
    private $avaliableLimit;

    function __construct(bool $activedCard, int $avaliableLimit = 0) {
        $this->activedCard = $activedCard;
        $this->avaliableLimit = $avaliableLimit;
    }

    function getActivedCard(): bool {
        return $this->activedCard;
    }

    function getAvaliableLimit(): int {
        return $this->avaliableLimit;
    }

    function setActivedCard(bool $active) {
        $this->activedCard = $active;
    }

    function setAvaliableLimit(int $limit) {
        $this->avaliableLimit = $limit;
    }

    function modifyAvaliableLimit(int $limit) {
        $newLimit = $this->getAvaliableLimit() + $limit;
        if ($newLimit < 0) {
            //throw Exception('asd');
        }
        $this->setAvaliableLimit($newLimit);
    }

    function getJsonRepresentation(): array {
        return [
            "activedCard" => $this->getActivedCard(),
            "avaliableLimit" => $this->getAvaliableLimit()
        ];
    }
}