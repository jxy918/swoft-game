<?php declare(strict_types=1);

namespace Swoft\Contract;

use Swoft\Processor\Processor;

/**
 * Swoft interface
 * @since 2.0
 */
interface SwoftInterface
{
    public const VERSION = '1.0.0';

    /**
     * Get env name
     *
     * Return `true` is to continue
     *
     * @return string
     */
    public function getEnvFile(): string;

    /**
     * Before run
     *
     * Return `true` is to continue
     *
     * @return bool
     */
    public function beforeRun(): bool;

    /**
     * Before env
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeEnv(): bool;

    /**
     * After env
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterEnv(): bool;

    /**
     * Before annotation
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeAnnotation(): bool;

    /**
     * After annotation
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterAnnotation(): bool;

    /**
     * Before config
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeConfig(): bool;

    /**
     * After annotation
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterConfig(): bool;

    /**
     * Before bean
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeBean(): bool;

    /**
     * After bean
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterBean(): bool;

    /**
     * Before event
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeEvent(): bool;

    /**
     * After Event
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterEvent(): bool;

    /**
     * Before console
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeConsole(): bool;

    /**
     * After console
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterConsole(): bool;

    /**
     * Add first processor
     *
     * @param Processor[] $processors
     *
     * @return true
     */
    public function addFirstProcessor(Processor ...$processors): bool;

    /**
     * Add last processor
     *
     * @param Processor[] $processor
     *
     * @return true
     */
    public function addLastProcessor(Processor ...$processor): bool;

    /**
     * Add processors
     *
     * @param int         $index
     * @param Processor[] $processors
     *
     * @return true
     */
    public function addProcessor(int $index, Processor ...$processors): bool;

    /**
     * Get bean file
     *
     * @return string
     */
    public function getBeanFile(): string;
}