# Edmonds Commerce Magento 2 Integration Testing

## Setup

### MySql Config

In `dev/tests/integration/etc` make a copy of `install-config-mysql.php.dist` called `install-config-mysql.php`

Alter the contents to have the correct configuration, you MUST use a totally separate database.

You can remove the `amqp-` files if not using rabbit queue.

### Script

To run the tests, copy `app/code/EdmondsCommerce/Testing/shellscripts/runIntegrationTests.bash` into `bin` and then run the script.

This script will copy the php unit config from this module into `dev/tests/integration/etc` and then run the tests.

If you make changes to the copy made in `dev/tests/integration/etc` it will be overwritten when running the script, unless you add a false flag.

# Edmonds Commerce QA runner

To run the qa runner, copy `app/code/EdmondsCommerce/Testing/shellscripts/qa.m2.bash` into `bin` and then run the script.

This will run the qa commands on `app/code/EdmondsCommerce` and display any errors. 
