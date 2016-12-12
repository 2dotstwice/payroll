<?php

namespace Payroll\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

class Version20161212104622 extends AbstractMigration
{
    /*
        The uuid can be stored more optimally than a char(36),
        but this is the easiest for now
        http://mysqlserverteam.com/storing-uuid-values-in-mysql-tables/
        at least a private function makes it easy to change this behaviour
     */
    private function addUuid(Table $table, String $columnName = 'uuid', Array $params = []) {
        $table->addColumn(
            $columnName,
            'string',
            array_merge(['length' => 36, 'notnull' => true, 'unique' => true], $params)
        );
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $t = $schema->createTable('users');
        $this->addUuid($t, 'uuid');
        $t->addColumn('name',      'string', ['notnull' => true] );
        $t->addColumn('email',     'string', ['notnull' => true, 'unique' => true] );
        $t->addColumn('oauth_uid', 'string', ['notnull' => false] );
        $t->setPrimaryKey(['uuid']);
        unset($t);

        $t = $schema->createTable('loa_decreasing');
        $this->addUuid($t, 'uuid');
        $t->addColumn('name',                 'string',  ['notnull' => true] );
        $t->addColumn('description',          'text',    ['notnull' => false] );
        $t->addColumn('default_value',        'integer', ['notnull' => true] );
        $t->addColumn('max_value',            'integer', ['notnull' => false] );
        $t->addColumn('display_order',        'integer', ['notnull' => false] );
        $t->addColumn('subtract_order',       'integer', ['notnull' => false] );
        $t->addColumn('transferable_to',      'string',  ['notnull' => false] ); // enum('self', 'other')
        $t->addColumn('transferable_to_uuid', 'string',  ['notnull' => false] );
        $t->addColumn('reset_to_default',     'boolean', ['notnull' => false] );
        $t->addColumn('period',                'string', ['notnull' => false] ); // enum('yearly', 'monthly', 'weekly')
        $t->setPrimaryKey(['uuid']);
        unset($t);

        $t = $schema->createTable('loa_increasing');
        $this->addUuid($t, 'uuid');
        $t->addColumn('name',             'string',  ['notnull' => true] );
        $t->addColumn('description',      'text',    ['notnull' => false] );
        $t->addColumn('default_value',    'integer', ['notnull' => true] );
        $t->addColumn('max_value',        'integer', ['notnull' => false] );
        $t->addColumn('display_order',    'integer', ['notnull' => false] );
        $t->addColumn('reset_to_default', 'boolean', ['notnull' => false] );
        $t->addColumn('period',           'string',  ['notnull' => false] ); // enum('yearly', 'monthly', 'weekly')
        $t->setPrimaryKey(['uuid']);
        unset($t);

        $t = $schema->createTable('user_has_loa_decreasing');
        $this->addUuid($t, 'user_uuid', ['unique' => false]);
        $this->addUuid($t, 'loa_decreasing_uuid', ['unique' => false]);
        $t->addColumn('value',                   'integer', ['notnull' => true] );
        $t->addColumn('subtract_order_override', 'integer', ['notnull' => false] );
        $t->addColumn('year',                    'integer', ['notnull' => false] );
        $t->addColumn('month',                   'integer', ['notnull' => false] );
        $t->addColumn('week',                    'integer', ['notnull' => false] );
        $t->setPrimaryKey(['user_uuid', 'loa_decreasing_uuid']);
        unset($t);

        $t = $schema->createTable('user_takes_any_decreasing_loa');
        $this->addUuid($t, 'uuid');
        $this->addUuid($t, 'user_uuid', ['unique' => false]);
        $t->addColumn('date',       'date', ['notnull' => true] );
        $t->addColumn('ts_created', 'datetime', ['notnull' => true] );
        $t->addColumn('amount',     'integer', ['notnull' => true] );
        $t->addColumn('reason',     'string', ['notnull' => false] );
        $t->setPrimaryKey(['uuid']);
        unset($t);

        $t = $schema->createTable('user_takes_loa_increasing');
        $this->addUuid($t, 'uuid');
        $this->addUuid($t, 'user_uuid', ['unique' => false]);
        $this->addUuid($t, 'loa_increasing_uuid', ['unique' => false]);
        $t->addColumn('date',       'date', ['notnull' => true] );
        $t->addColumn('ts_created', 'datetime', ['notnull' => true] );
        $t->addColumn('amount',     'integer', ['notnull' => true] );
        $t->addColumn('reason',     'string', ['notnull' => false] );
        $t->setPrimaryKey(['uuid']);
        unset($t);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('users');
        $schema->dropTable('loa_decreasing');
        $schema->dropTable('loa_increasing');
        $schema->dropTable('user_has_loa_decreasing');
        $schema->dropTable('user_takes_any_decreasing_loa');
        $schema->dropTable('user_takes_loa_increasing');
    }
}
