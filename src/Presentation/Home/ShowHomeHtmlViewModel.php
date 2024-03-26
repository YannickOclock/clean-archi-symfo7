<?php

declare(strict_types=1);

namespace App\Presentation\Home;

final class ShowHomeHtmlViewModel
{
    public ?string $username;
    public array $roles;
    public array $menuCategories;
    public int $cartProductsCount;
}
