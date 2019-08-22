<?php declare(strict_types=1);


namespace Swoft\Db\Connection;

use InvalidArgumentException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Db\Query\Grammar\MySqlGrammar;
use Swoft\Db\Query\Grammar\Grammar;
use Swoft\Db\Query\Processor\MySqlProcessor;
use Swoft\Db\Query\Processor\Processor;
use Throwable;

/**
 * Class MySqlConnection
 *
 * @Bean(scope=Bean::PROTOTYPE)
 *
 * @since 2.0
 */
class MySqlConnection extends Connection
{
    /**
     * Set the table prefix and return the grammar.
     *
     * @param Grammar $grammar
     *
     * @return Grammar
     */
    public function withTablePrefix(Grammar $grammar)
    {
        $grammar->setTablePrefix($this->database->getPrefix());

        return $grammar;
    }

    /**
     * Get the default query grammar instance.
     *
     * @return MySqlGrammar
     * @throws Throwable
     */
    protected function getDefaultQueryGrammar(): Grammar
    {
        $grammar = \bean(MySqlGrammar::class);
        if (!$grammar instanceof MySqlGrammar) {
            throw new InvalidArgumentException('%s class is not Grammar instance', get_class($grammar));
        }
        return $this->withTablePrefix($grammar);
    }

    /**
     * Get the default post processor instance.
     *
     * @return object|string|MySqlProcessor|Processor
     * @throws Throwable
     */
    protected function getDefaultPostProcessor()
    {
        return \bean(MySqlProcessor::class);
    }

    /**
     * Whether to reconnect
     *
     * @return bool
     */
    protected function isReconnect(): bool
    {
        $pdo = null;
        if ($this->pdoType == self::TYPE_WRITE) {
            $pdo = $this->pdo;
        } elseif ($this->pdoType == self::TYPE_READ) {
            $pdo = $this->readPdo;
        }

        if (empty($pdo)) {
            return false;
        }

        $errorInfo = $pdo->errorInfo();
        $errorCode = $errorInfo[1] ?? 0;
        $errorCode = (int)$errorCode;
        if ($errorCode == 2006 || $errorCode == 2013) {
            return true;
        }

        return false;
    }
}