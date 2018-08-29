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
use Swoft\Db\Driver\DriverType;

/**
 * The connect annotation
 *
 * @Annotation
 * @Target("CLASS")
 *
 * @uses      Connection
 * @version   2018年01月27日
 * @author    stelin <phpcrazy@126.com>
 * @copyright Copyright 2010-2016 swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class Connection
{
    /**
     * @var string
     */
    private $driver = Driver::MYSQL;

    /**
     * @var string
     */
    private $type = DriverType::COR;

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
        if (isset($values['type'])) {
            $this->type = $values['type'];
        }
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
