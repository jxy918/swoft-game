<?php declare(strict_types=1);


namespace Swoft\Db;

/**
 * Class DbEvent
 *
 * @since 2.0
 */
class DbEvent
{
    /**
     * Begin transaction
     */
    public const BEGIN_TRANSACTION = 'swoft.db.transaction.begin';

    /**
     * Commit transaction
     */
    public const COMMIT_TRANSACTION = 'swoft.db.transaction.commit';

    /**
     * Rollback
     */
    public const ROLLBACK_TRANSACTION = 'swoft.db.transaction.rollback';

    /**
     * Sql ran after
     */
    public const SQL_RAN = 'swoft.db.ran';

    /**
     * Execute update sql or delete sql
     */
    public const AFFECTING_STATEMENTING = 'swoft.db.affectingStatementing';

    /**
     * Execute selecting sql
     */
    public const SELECTING = 'swoft.db.selecting';

    /**
     * Model saving
     */
    public const MODEL_SAVING = 'swoft.model.saving';

    /**
     * Model saved
     */
    public const MODEL_SAVED = 'swoft.model.saved';

    /**
     * Model updating
     */
    public const MODEL_UPDATING = 'swoft.model.updating';

    /**
     * Model updated
     */
    public const MODEL_UPDATED = 'swoft.model.updated';

    /**
     * Model creating
     */
    public const MODEL_CREATING = 'swoft.model.creating';

    /**
     * Model created
     */
    public const MODEL_CREATED = 'swoft.model.created';

    /**
     * Model deleting
     */
    public const MODEL_DELETING = 'swoft.model.deleting';

    /**
     * Model deleted
     */
    public const MODEL_DELETED = 'swoft.model.deleted';
}
