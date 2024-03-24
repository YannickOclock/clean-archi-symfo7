<?php

namespace App\Domain\Home\UseCase\ShowHome;

interface ShowHomeUseCaseInterface
{
    public function execute(ShowHomeRequest $request, ShowHomeResponse $response, ShowHomePresenterInterface $presenter): void;
}