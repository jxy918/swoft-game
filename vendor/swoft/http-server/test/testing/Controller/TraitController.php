<?php declare(strict_types=1);


namespace SwoftTest\Http\Server\Testing\Controller;

use Swoft\Http\Server\Annotation\Mapping\RequestMapping;

/**
 * Class TraitController
 *
 * @since 2.0
 */
trait TraitController
{
    /**
     * @RequestMapping()
     *
     * @return array
     */
    public function traitMethod(): array
    {
        return ['traitMethod'];
    }
}