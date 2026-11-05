<?php

namespace Mapo89\LaravelAvatarManager\Tests\Unit;

use Mapo89\LaravelAvatarManager\AvatarManager;
use Mapo89\LaravelAvatarManager\Facades\AvatarManager as AvatarFacade;
use Mapo89\LaravelAvatarManager\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_facade_resolves_and_generates_normalized_urls(): void
    {
        $this->assertInstanceOf(AvatarManager::class, AvatarFacade::getFacadeRoot());
        $this->assertSame(md5('demo@example.com'), AvatarFacade::hash(' DEMO@example.com '));
        $this->assertSame(url('/avatar/'.md5('demo@example.com')), AvatarFacade::url(' DEMO@example.com '));
    }
}
