# Edmonds Commerce Magento 2 Integration Testing

## Installation

First make sure the container has access to the BitBucket repos. If it does not then add the public key to the
account, details can be found [here](https://confluence.atlassian.com/bitbucket/set-up-an-ssh-key-728138079.html)

Then cd to the project directory and run the following commands

```bash
composer config repositories.edmondscommerce-test-runner vcs git@github.com:edmondscommerce/edmondscommerce-magento2-testing.git
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

