<?php declare(strict_types=1);


namespace Swoft\Db\Connector;

use Exception;
use PDO;
use Swoft\Db\Contract\ConnectorInterface;

/**
 * Class Connector
 *
 * @since 2.0
 */
abstract class AbstractConnector implements ConnectorInterface
{
    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $options = [
        PDO::ATTR_CASE              => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_EMULATE_PREPARES  => false,
    ];

    /**
     * Create a new PDO connection.
     *
     * @param  string $dsn
     * @param  string $username
     * @param  string $password
     * @param  array  $options
     *
     * @return PDO
     *
     * @throws Exception
     */
    public function createConnection($dsn, string $username, string $password, array $options)
    {
        $options = $this->getOptions($options);
        return new PDO($dsn, $username, $password, $options);
    }

    /**
     * Create a new PDO connection instance.
     *
     * @param  string $dsn
     * @param  string $username
     * @param  string $password
     * @param  array  $options
     *
     * @return PDO
     */
    protected function createPdoConnection($dsn, $username, $password, $options)
    {
        return new PDO($dsn, $username, $password, $options);
    }

    /**
     * Get the PDO options based on the configuration.
     *
     * @param  array $options
     *
     * @return array
     */
    public function getOptions(array $options)
    {
        return array_diff_key($this->options, $options) + $options;
    }
}
