<?php declare(strict_types=1);


namespace Swoft\Db;


use function bean;
use PDO;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\SwoftComponent;

/**
 * Class AutoLoader
 *
 * @since 2.0
 */
class AutoLoader extends SwoftComponent
{
    /**
     * @return array
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function beans(): array
    {
        return [
            'db'      => [
                'class'  => Database::class,
                'dsn'    => 'mysql:dbname=dbname;host=127.0.0.1',
                'config' => [
                     // fetch array
                    'fetchMode' => PDO::FETCH_ASSOC,
                ],
            ],
            'db.pool' => [
                'class'    => Pool::class,
                'database' => bean('db')
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * @return array
     */
    public function metadata(): array
    {
        return [];
    }
}
