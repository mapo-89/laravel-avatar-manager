<?php
/*
 * AvatarManager.php
 * @author Manuel Postler <info@postler.de>
 * @copyright 2025 Manuel Postler
 */
namespace Mapo89\LaravelAvatarManager\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mapo89\LaravelAvatarManager\Skeleton\SkeletonClass
 */
class AvatarManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'avatar-manager';
    }
}
