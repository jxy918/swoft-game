<?php
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://doc.swoft.org
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Db\Bean\Annotation;

use Swoft\Db\Driver\Driver;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Statement
{
    /**
     * @var string
     */
    private $driver = Driver::MYSQL;

    /**
     * Connect constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->driver = $values['value'];
        }
        if (isset($values['driver'])) {
            $this->driver = $values['driver'];
        }
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }
}
