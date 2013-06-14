# Chai

Chai is a collection of tools to aid in the development of PHP applications.
It aims to be easy to use and completely framework agnostic. It utilizes
components from other frameworks and libraries, so as not to re-invent the
wheel.

## Requirements

* PHP 5.3 or greater
* Composer

## Components

### Migrations

Migrations allow you to "version control" your database. You can create tables,
update tables, or perform other actions. Most migration systems have an up and
down method. The up method does the desired action such as creating or updating
a table. The down method does the opposite allowing you to run migrations
multiple times without any issues. In addition, Chai has an update method that
is ran if the migration has already been applied.

#### Getting Started

To get started using migrations, you will need to create a console script
(`bin/console`).

    <?php

    //use Symfony\Component\Console\Application;
    use Chai\Console\Application;
    use Chai\Migrations\Migrations;

    $migrations = new Migrations();
    $migrations->setMigrationsDirectory(__DIR__.'/../app/database/migrations');
    $migrations->setDatabaseParameters(array(
        'host' => '',
        'port' => '',
        'username' => '',
        'password' => '',
        'database' => '',
    ));

    $app = new Application('Description', '0.1.0');
    $app->register($migrations);
    $app->run();

Then run `bin/console migration:init`, which will create the *migrations* table.

#### Creating Migrations

You cna create a migration using the console application.

`bin/console migration:create <name>`

Migration names must be all lowercase, using underscores (`_`) as seperators, and not begin with a number.

**Valid Migration Names**

* create_user_table
* update_post_table
* add_index_to_metadata_table
* test_migration_2

**Invalid Migration Names**

* CreateTable
* Add Post Table
* 1test_migration

All migrations are prefixed with a timestamp. This is to keep migrations unique and to know which order they are to be ran in.

A basic migration looks like:

	<?php

	use Chai\Migrations\BaseMigration;

	class TestMigration extends BaseMigration
	{

		public function up()
		{
			// Do something here
		}

		public function down()
		{
			// Do the opposite here
		}

		public function update()
		{
			// Do something only if the
			// migration has already been ran
		}

	}

#### Running Migrations

`bin/console migration:up [name]`

`bin/console migration:down [name]`

#### Status

`bin/console migration:status`
