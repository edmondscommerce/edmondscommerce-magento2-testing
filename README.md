# Edmonds Commerce Magento 2 Integration Testing

## Installation

Use composer:

```bash
composer require edmondscommerce/module-magento2-test-runner --dev
```

Also ensure that you have the Magento 2 coding standards installed

```bash
composer require --dev magento/magento-coding-standard
```

## Running QA

There will now be a `qa.m2.bash` file in the `vendor/bin` folder. By default it will scan the entire `app/code`
directory. If there are already several modules in there that are failing, then it can just scan a specific
directory by passing the relative path to it.

```bash
# Scan the entire directory
bash vendor/bin/qa.m2.bash

# Just scan the EdmondsCommerce directory
bash vendor/bin/qa.m2.bash app/code/EdmondsCommerce
```

This tool should be run on a regular basic, and must be passing before requesting changes are merged.

## Running Integration Tests

### MySql Config

In `dev/tests/integration/etc` make a copy of `install-config-mysql.php.dist` called `install-config-mysql.php`

Alter the contents to have the correct configuration, you MUST use a totally separate database.

You can remove the `amqp-` files if not using rabbit queue.

See [here](https://devdocs.magento.com/guides/v2.3/test/integration/integration_test_execution.html#setup) for further details

### PHP Unit Config

Copy `vendor/edmondscommerce/module-magento2-test-runner/Test/Integration/phpunit.xml` to `dev/tests/integration`.

```
cp vendor/edmondscommerce/module-magento2-test-runner/Test/Integration/phpunit.xml dev/tests/integration/
```

### Copy scripts

Copy `vendor/edmondscommerce/module-magento2-test-runner/bin/magentoIntegrationTests/generateTests.bash` and `vendor/edmondscommerce/module-magento2-test-runner/bin/magentoIntegrationTests/runTests.bash` to `dev/tests/integration`.

```
cp vendor/edmondscommerce/module-magento2-test-runner/bin/magentoIntegrationTests/generateTests.bash dev/tests/integration
cp vendor/edmondscommerce/module-magento2-test-runner/bin/magentoIntegrationTests/runTests.bash dev/tests/integration
```

### Generating the Tests

`cd dev/tests/integration` then `bash generateTests.bash` to generate all the tests.

### Running the Tests

To run an indivual test: 

`cd dev/tests/integration`

`../../../vendor/bin/phpunit -c [Name Of Test]` ie `../../../vendor/bin/phpunit -c phpunit.magento.Checkout.xml`

To run all the tests use `bash runTests.bash`.

### Setting up php unit configuration

By default the tool will copy a standard phpunit.xml file into the test folder. This can be bypassed by passing
false to the script, however you will need to create a `dev/tests/integration/phpunit.edmondscommerce.xml` file
before the tests will run.

### Running the tests

There is a new tool in the `vendor/bin` directory called `runIntegrationTests.bash`. It can be run like so

```bash
# Copy the default config and run the tests
bash vendor/bin/runIntegrationTests.bash
# Don't copy the config and use the file that is already there
bash vendor/bin/runIntegrationTests.bash false
```

