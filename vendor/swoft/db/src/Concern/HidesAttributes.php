<?php declare(strict_types=1);


namespace Swoft\Db\Concern;

use function array_diff;
use function array_merge;
use function array_unique;

/**
 * Class HidesAttributes
 *
 * @since 2.0
 */
trait HidesAttributes
{
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $modelHidden = [];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array
     */
    protected $modelVisible = [];

    /**
     * Get the hidden attributes for the model.
     *
     * @return array
     */
    public function getModelHidden()
    {
        return $this->modelHidden;
    }

    /**
     * Set the hidden attributes for the model.
     *
     * @param array $modelHidden
     *
     * @return $this
     */
    public function setModelHidden(array $modelHidden)
    {
        $this->modelHidden = $modelHidden;

        return $this;
    }

    /**
     * Add hidden attributes for the model.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function addHidden(array $attributes)
    {
        $this->modelVisible = array_diff($this->modelVisible, $attributes);

        $this->modelHidden = array_unique(array_merge($this->modelHidden, $attributes));

    }

    /**
     * Get the visible attributes for the model.
     *
     * @return array
     */
    public function getModelVisible()
    {
        return $this->modelVisible;
    }

    /**
     * Set the visible attributes for the model.
     *
     * @param array $modelVisible
     *
     * @return $this
     */
    public function setModelVisible(array $modelVisible)
    {
        $this->modelVisible = $modelVisible;

        return $this;
    }

    /**
     * Add visible attributes for the model.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function addVisible(array $attributes): void
    {
        $this->modelHidden = array_diff($this->modelHidden, $attributes);

        $this->modelVisible = array_unique(array_merge($this->modelVisible, $attributes));
    }

    /**
     * Make the given, typically hidden, attributes visible.
     *
     * @param array $attributes
     *
     * @return self
     */
    public function makeVisible(array $attributes): self
    {
        $this->addVisible($attributes);

        return $this;
    }

    /**
     * Make the given, typically visible, attributes hidden.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function makeHidden(array $attributes): self
    {
        $this->addHidden($attributes);

        return $this;
    }
}
