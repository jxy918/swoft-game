<?php declare(strict_types=1);

namespace Swoft\Context;

use RuntimeException;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Co;
use Swoft\Contract\WaitGroupInterface;
use Swoft\WaitGroup;

/**
 * Class ContextWaitGroup
 *
 * @since 2.0
 *
 * @Bean()
 */
class ContextWaitGroup implements WaitGroupInterface
{
    /**
     * @var array
     * @example
     * [
     *     'tid' => WaitGroup
     * ]
     */
    private $waitGroups = [];

    /**
     * Add task
     */
    public function add(): void
    {
        $tid       = Co::tid();
        $waitGroup = $this->getWaitGroup();
        if ($waitGroup instanceof WaitGroup) {
            $waitGroup->add();
            return;
        }

        $waitGroup = new WaitGroup();
        $waitGroup->add();

        $this->waitGroups[$tid] = $waitGroup;
    }

    /**
     * Done
     */
    public function done(): void
    {
        $waitGroup = $this->getWaitGroup();
        if ($waitGroup === null) {
            throw new RuntimeException('You must to be done then add by wait group');
        }

        $waitGroup->done();
    }

    /**
     * Wait
     */
    public function wait(): void
    {
        // Not wait group
        $waitGroup = $this->getWaitGroup();
        if ($waitGroup === null) {
            return;
        }

        $waitGroup->wait();

        $tid = Co::tid();
        unset($this->waitGroups[$tid]);
    }

    /**
     * Get wait group
     *
     * @return WaitGroup|null
     */
    private function getWaitGroup(): ?WaitGroup
    {
        $tid = Co::tid();

        return $this->waitGroups[$tid] ?? null;
    }
}
