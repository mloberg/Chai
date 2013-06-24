<?php

use Chai\Migrations\BaseMigration;

class CreateTest extends BaseMigration
{

    public function up()
    {
        $this->schema()->create('test', function($table) {
            $table->string('name');
        });
    }

    public function down()
    {
        $this->schema()->drop('test');
    }

}
