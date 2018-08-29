<?php

namespace Swoft\Task\Bean\Annotation;

/**
 * Scheduled annotation
 *
 * @Annotation
 * @Target({"METHOD"})
 */
class Scheduled
{
    /**
     * @var string
     */
    private $cron;

    /**
     * @var string
     */
    private $description = '';

    /**
     * Bean constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->cron = $values['value'];
        }

        if (isset($values['cron'])) {
            $this->cron = $values['cron'];
        }

        if (isset($values['description'])) {
            $this->description = $values['description'];
        }
    }

    /**
     * @return string
     */
    public function getCron(): string
    {
        return $this->cron;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
