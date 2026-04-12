<?php

namespace OCA\NextcloudQuest\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1017Date20260412120000 extends SimpleMigrationStep {

    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        // Epics — user-created quest collections
        if (!$schema->hasTable('ncquest_epics')) {
            $table = $schema->createTable('ncquest_epics');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('user_id', 'string', ['notnull' => true, 'length' => 64]);
            $table->addColumn('title', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('description', 'text', ['notnull' => false]);
            $table->addColumn('emoji', 'string', ['notnull' => false, 'length' => 10]);
            $table->addColumn('color', 'string', ['notnull' => false, 'length' => 7]);
            $table->addColumn('tier', 'string', ['notnull' => true, 'length' => 20, 'default' => 'common']);
            $table->addColumn('status', 'string', ['notnull' => true, 'length' => 20, 'default' => 'active']);
            $table->addColumn('total_tasks', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('completed_tasks', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('total_xp_earned', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('bonus_xp_awarded', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('completed_at', 'datetime', ['notnull' => false]);
            $table->addColumn('created_at', 'datetime', ['notnull' => true]);
            $table->addColumn('updated_at', 'datetime', ['notnull' => true]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'quest_epic_user');
            $table->addIndex(['user_id', 'status'], 'quest_epic_user_status');
        }

        // Epic tasks — links tasks to epics (many-to-many)
        if (!$schema->hasTable('ncquest_epic_tasks')) {
            $table = $schema->createTable('ncquest_epic_tasks');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('epic_id', 'integer', ['notnull' => true]);
            $table->addColumn('user_id', 'string', ['notnull' => true, 'length' => 64]);
            $table->addColumn('task_uid', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('list_id', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('task_title', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('xp_earned', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('is_completed', 'integer', ['notnull' => true, 'default' => 0, 'length' => 1]);
            $table->addColumn('completed_at', 'datetime', ['notnull' => false]);
            $table->addColumn('added_at', 'datetime', ['notnull' => true]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['epic_id'], 'quest_et_epic');
            $table->addIndex(['user_id'], 'quest_et_user');
            $table->addIndex(['task_uid', 'list_id'], 'quest_et_task');
            $table->addUniqueIndex(['epic_id', 'task_uid', 'list_id'], 'quest_et_unique');
        }

        return $schema;
    }
}
