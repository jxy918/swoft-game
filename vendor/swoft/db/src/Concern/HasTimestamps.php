<?php declare(strict_types=1);


namespace Swoft\Db\Concern;

use Swoft\Db\Schema\Grammars\Grammar;
use function time;
use function date;
use function is_null;

/**
 * Trait HasTimestamps
 *
 * @since 2.0
 */
trait HasTimestamps
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    protected $modelTimestamps = true;

    /**
     * Write date format
     *
     * @var string
     */
    protected $modelDateFormat = 'Y-m-d H:i:s';

    /**
     * Update the model's update timestamp.
     *
     * @return bool
     */
    public function touch(): bool
    {
        if (!$this->usesTimestamps()) {
            return false;
        }

        $this->updateTimestamps();

        return $this->save();
    }

    /**
     * Update the creation and update timestamps.
     *
     * @return array
     */
    public function updateTimestamps(): array
    {
        $updated = [];

        if (!is_null(static::UPDATED_AT) &&
            !$this->isDirty(static::UPDATED_AT) &&
            $this->hasSetter(static::UPDATED_AT)
        ) {
            $createTimestamp = $this->freshTimestamp(static::UPDATED_AT);

            $this->setModelAttribute(static::UPDATED_AT, $createTimestamp);

            $updated[static::UPDATED_AT] = $createTimestamp;
        }

        if (!$this->swoftExists &&
            !is_null(static::CREATED_AT) &&
            !$this->isDirty(static::CREATED_AT) &&
            $this->hasSetter(static::CREATED_AT)
        ) {
            $createTimestamp = $this->freshTimestamp(static::CREATED_AT);

            $this->setModelAttribute(static::CREATED_AT, $createTimestamp);

            $updated[static::CREATED_AT] = $createTimestamp;
        }

        return $updated;
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @param string $column
     *
     * @return int|string
     */
    public function freshTimestamp(string $column)
    {
        [, $type, ,] = $this->getMappingByColumn($column);

        // Auto choose timestamp type
        return $type === Grammar::STRING ? date($this->modelDateFormat) : time();
    }

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps(): bool
    {
        return $this->modelTimestamps;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }
}
