<?php

namespace App\Domain\Home\UseCase\ShowHome;

interface ShowHomePresenterInterface
{
    public function present(ShowHomeResponse $response): void;
}