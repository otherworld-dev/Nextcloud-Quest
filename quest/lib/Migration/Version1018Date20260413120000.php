<?php

namespace OCA\NextcloudQuest\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1018Date20260413120000 extends SimpleMigrationStep {

    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        // Journey state — one row per user
        if (!$schema->hasTable('ncquest_journey')) {
            $table = $schema->createTable('ncquest_journey');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('user_id', 'string', ['notnull' => true, 'length' => 64]);
            $table->addColumn('current_age_key', 'string', ['notnull' => true, 'length' => 20, 'default' => 'stone']);
            $table->addColumn('steps_taken', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('total_steps', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('steps_per_encounter', 'integer', ['notnull' => true, 'default' => 3]);
            $table->addColumn('encounters_completed', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('bosses_defeated', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('battles_won', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('battles_lost', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('treasures_found', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('events_completed', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('prestige_level', 'integer', ['notnull' => true, 'default' => 0]);
            $table->addColumn('updated_at', 'datetime', ['notnull' => true]);
            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['user_id'], 'quest_journey_user');
        }

        // Journey encounter log
        if (!$schema->hasTable('ncquest_journey_log')) {
            $table = $schema->createTable('ncquest_journey_log');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'notnull' => true]);
            $table->addColumn('user_id', 'string', ['notnull' => true, 'length' => 64]);
            $table->addColumn('encounter_type', 'string', ['notnull' => true, 'length' => 20]);
            $table->addColumn('age_key', 'string', ['notnull' => true, 'length' => 20]);
            $table->addColumn('encounter_data', 'text', ['notnull' => false]);
            $table->addColumn('outcome', 'string', ['notnull' => true, 'length' => 20]);
            $table->addColumn('rewards', 'text', ['notnull' => false]);
            $table->addColumn('created_at', 'datetime', ['notnull' => true]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'quest_jlog_user');
            $table->addIndex(['user_id', 'encounter_type'], 'quest_jlog_user_type');
        }

        return $schema;
    }
}
