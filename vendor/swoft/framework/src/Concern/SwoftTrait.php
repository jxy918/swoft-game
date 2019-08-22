<?php

namespace Swoft\Concern;

/**
 * Trait SwoftTrait
 * @since 2.0
 */
trait SwoftTrait
{
    /**
     * Get env name
     *
     * Return `true` is to continue
     *
     * @return string
     */
    public function getEnvFile(): string
    {
        return $this->envFile;
    }

    /**
     * Before run
     *
     * Return `true` is to continue
     *
     * @return bool
     */
    public function beforeRun(): bool
    {
        return true;
    }

    /**
     * After env
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterEnv(): bool
    {
        return true;
    }

    /**
     * Before env
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeEnv(): bool
    {
        return true;
    }

    /**
     * Before annotation
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeAnnotation(): bool
    {
        return true;
    }

    /**
     * After annotation
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterAnnotation(): bool
    {
        return true;
    }

    /**
     * Before config
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeConfig(): bool
    {
        return true;
    }

    /**
     * After annotation
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterConfig(): bool
    {
        return true;
    }

    /**
     * Before bean
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeBean(): bool
    {
        return true;
    }

    /**
     * After bean
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterBean(): bool
    {
        return true;
    }

    /**
     * Before event
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeEvent(): bool
    {
        return true;
    }

    /**
     * After Event
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterEvent(): bool
    {
        return true;
    }

    /**
     * Before console
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function beforeConsole(): bool
    {
        return true;
    }

    /**
     * After console
     *
     * Return `true` is to continue
     * Return `false` is to stop application
     *
     * @return bool
     */
    public function afterConsole(): bool
    {
        return true;
    }
}