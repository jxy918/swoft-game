<?php declare(strict_types=1);


namespace SwoftTest\Bean\Testing\Definition;


use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use SwoftTest\Bean\Testing\BeanProperty;
use SwoftTest\Bean\Testing\InjectBean;

/**
 * Class SessionClass
 *
 * @since 2.0
 *
 * @Bean(scope=Bean::SESSION, name="sessionClass")
 */
class SessionClass extends BeanProperty
{
    /**
     * @var string
     */
    private $classPrivate = '';

    /**
     * @var int
     */
    private $classPublic = 0;

    /**
     * @var string
     */
    private $setProp = '';

    /**
     * @Inject()
     *
     * @var InjectBean
     */
    private $injectBean;

    /**
     * @Inject(InjectBean::class)
     *
     * @var InjectBean
     */
    private $injectBeanClass;

    /**
     * @Inject("injectBeanAlias")
     *
     * @var InjectBean
     */
    private $injectBeanAlias;

    /**
     * @Inject("injectBean")
     *
     * @var InjectBean
     */
    private $injectBeanName;

    /**
     * @var InjectBean
     */
    private $definitionBean;

    /**
     * @var InjectBean
     */
    private $definitionBeanAlias;

    /**
     * @var InjectBean
     */
    private $definitionBeanClass;

    /**
     * @param string $setProp
     */
    public function setSetProp(string $setProp): void
    {
        $this->setProp = $setProp . '-setter';
    }

    /**
     * @return string
     */
    public function getClassPrivate(): string
    {
        return $this->classPrivate;
    }

    /**
     * @return int
     */
    public function getClassPublic(): int
    {
        return $this->classPublic;
    }

    /**
     * @return string
     */
    public function getSetProp(): string
    {
        return $this->setProp;
    }

    /**
     * @return InjectBean
     */
    public function getInjectBean(): InjectBean
    {
        return $this->injectBean;
    }

    /**
     * @return InjectBean
     */
    public function getInjectBeanClass(): InjectBean
    {
        return $this->injectBeanClass;
    }

    /**
     * @return InjectBean
     */
    public function getInjectBeanAlias(): InjectBean
    {
        return $this->injectBeanAlias;
    }

    /**
     * @return InjectBean
     */
    public function getInjectBeanName(): InjectBean
    {
        return $this->injectBeanName;
    }

    /**
     * @return InjectBean
     */
    public function getDefinitionBean(): InjectBean
    {
        return $this->definitionBean;
    }

    /**
     * @return InjectBean
     */
    public function getDefinitionBeanAlias(): InjectBean
    {
        return $this->definitionBeanAlias;
    }

    /**
     * @return InjectBean
     */
    public function getDefinitionBeanClass(): InjectBean
    {
        return $this->definitionBeanClass;
    }
}