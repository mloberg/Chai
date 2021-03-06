<?php

use Chai\Migrations\BaseMigration;

class UpdateMethodRan extends Exception {}

class MigrationTwo extends BaseMigration
{

    /**
     * Run the migration.
     *
     * @return void
     */
    public function up()
    {
        //
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    /**
     * Ran if migration has already been applied.
     *
     * @return void
     */
    public function update()
    {
        throw new UpdateMethodRan;
    }

}
