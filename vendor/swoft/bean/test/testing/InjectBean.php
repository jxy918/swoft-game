<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing;

use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class InjectBean
 *
 * @since 2.0
 *
 * @Bean(name="injectBean", alias="injectBeanAlias")
 */
class InjectBean
{
    /**
     * @Inject()
     *
     * @var InjectChildBean
     */
    private $injectChildBean;

    /**
     * @return string
     */
    public function getData(): string
    {
        return 'InjectBeanData-' . $this->injectChildBean->getData();
    }
}