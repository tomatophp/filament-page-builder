<?php

namespace TomatoPHP\FilamentPageBuilder\Tests\Policies;

use TomatoPHP\FilamentPageBuilder\Tests\Models\Page;
use TomatoPHP\FilamentPageBuilder\Tests\Models\User;

class OwnerPagePolicy
{
    public function update(User $user, Page $page): bool
    {
        return (int) $page->user_id === (int) $user->getKey();
    }
}
