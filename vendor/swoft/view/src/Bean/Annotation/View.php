<?php

namespace Swoft\View\Bean\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Action 方法注解
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @uses      View
 * @version   2017-11-08
 * @author    huangzhhui <huangzhwork@gmail.com>
 * @copyright Copyright 2010-2017 Swoft software
 * @license   PHP Version 7.x {@link http://www.php.net/license/3_0.txt}
 */
class View
{

    /**
     * @var string
     */
    private $template = '';

    /**
     * @var string
     */
    private $layout = '';

    /**
     * View constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        isset($values['value']) && $this->setTemplate($values['value']);
        isset($values['template']) && $this->setTemplate($values['template']);
        isset($values['layout']) && $this->setLayout($values['layout']);
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return self
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     *
     * @return self
     */
    public function setLayout(string $layout): self
    {
        $this->layout = $layout;

        return $this;
    }
}
