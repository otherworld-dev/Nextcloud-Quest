<?php

namespace OCA\NextcloudQuest\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version1020Date20260413160000 extends SimpleMigrationStep {

    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        // Add quantity column to character unlocks for crafting
        if ($schema->hasTable('quest_char_unlocks')) {
            $table = $schema->getTable('quest_char_unlocks');
            if (!$table->hasColumn('quantity')) {
                $table->addColumn('quantity', 'integer', [
                    'notnull' => true,
                    'default' => 1,
                ]);
            }
        }

        return $schema;
    }
}
