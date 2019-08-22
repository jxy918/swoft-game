<?php declare(strict_types=1);

namespace SwoftTest\Stdlib\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Swoft\Stdlib\Helper\Str;

/**
 * Class StringHelperTest
 *
 * @since 2.0
 */
class StringHelperTest extends TestCase
{
    public function testGetClassName(): void
    {
        $name = Str::getClassName('App\Http\Controller\UserController', 'Controller');
        $this->assertSame('user', $name);

        $name = Str::getClassName('App\Http\Controller\UserController', '');
        $this->assertSame('App\Http\Controller\UserController', $name);
    }

    public function testFormatPath(): void
    {
        $this->assertSame('/', Str::formatPath(''));
        $this->assertSame('/a', Str::formatPath('a'));
        $this->assertSame('/a', Str::formatPath('a/'));
        $this->assertSame('/a', Str::formatPath('/a/'));
    }

    public function testRmPharPrefix(): void
    {
        $path = Str::rmPharPrefix('phar:///path/to/some.phar/vendor/composer');
        $this->assertSame('/path/to/vendor/composer', $path);

        $path = Str::rmPharPrefix('phar:///path/to/some.phar/vendor/composer/');
        $this->assertSame('/path/to/vendor/composer/', $path);

        $path = Str::rmPharPrefix('phar:///path/to/some.phar/vendor/composer/.env');
        $this->assertSame('/path/to/vendor/composer/.env', $path);

        $path = Str::rmPharPrefix('/vendor/composer/.env');
        $this->assertSame('/vendor/composer/.env', $path);
    }

    public function testGetUnique(): void
    {
        $uniqueId = Str::getUniqid();

        $this->assertNotEmpty($uniqueId);
        $this->assertIsString($uniqueId);
    }
}
