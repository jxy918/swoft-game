<?php declare(strict_types=1);

namespace Swoft\Processor;

use Swoft\Contract\SwoftInterface;
use Swoft\SwoftApplication;

/**
 * Abstract processor
 * @since 2.0
 */
abstract class Processor implements ProcessorInterface
{
    /**
     * Swoft application
     *
     * @var SwoftInterface|SwoftApplication
     */
    protected $application;

    /**
     * Processor constructor.
     *
     * @param SwoftInterface $application
     */
    public function __construct(SwoftInterface $application)
    {
        $this->application = $application;
    }
}
