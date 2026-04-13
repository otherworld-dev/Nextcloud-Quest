<?php

namespace OCA\NextcloudQuest\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1019Date20260413140000 extends SimpleMigrationStep {

    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('ncquest_challenges')) {
            $table = $schema->createTable('ncquest_challenges');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('user_id', 'string', ['notnull' => true, 'length' => 64]);
            $table->addColumn('challenge_type', 'string', ['notnull' => true, 'length' => 30]);
            $table->addColumn('period', 'string', ['notnull' => true, 'length' => 10]); // daily or weekly
            $table->addColumn('title', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('description', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('target', 'integer', ['notnull' => true]);
            $table->addColumn('progress', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('xp_reward', 'integer', ['notnull' => true]);
            $table->addColumn('is_completed', 'integer', ['notnull' => true, 'default' => 0, 'length' => 1]);
            $table->addColumn('is_claimed', 'integer', ['notnull' => true, 'default' => 0, 'length' => 1]);
            $table->addColumn('expires_at', 'datetime', ['notnull' => true]);
            $table->addColumn('created_at', 'datetime', ['notnull' => true]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id', 'period'], 'quest_chal_user_period');
            $table->addIndex(['user_id', 'expires_at'], 'quest_chal_user_exp');
        }

        return $schema;
    }
}
