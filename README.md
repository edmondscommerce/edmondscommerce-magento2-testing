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

# Running Acceptance Tests

There is a helper script that will try to setup the Magento 2 acceptance testing suite, this will
also validate as much as possible and allows for the use of a centralised environment configuration file.

## Project Requirements

* You must have the appropriate version of MFTF installed as a dev dependency
* Magerun2 must be installed and usable on the system path as `magerun`
* System level bash support, this has been tested under Centos 7

## Setup

The script is written in a way to provide as much feedback as possible when something is wrong with the
configuration.

In places it will auto fix issues with data from the main environment file and also try to protect you.

### Environment file

In this repository there is a [test.bash.dist](./test.bash.dist) file that needs to be copied to the
root of the Magento 2 project and renamed to `test.bash` - this will be used to load your project/environment
specific variables to use later.

This file should also be git ignored, this will be checked as part of the setup script, this also serves as an environment check
as this file should never exist in production nor should this module be installed in production for that matter.

Once the file is copied, fill out the different variable values as required.

### Running the Setup Script

Run `bash vendor/bin/setupAcceptanceTests.bash` from the root of your project, this is assuming that 
bin files are still setup to be symlinked with composer. 

You can use the full path `vendor/edmondscommerce/module-magento2-test-runner/bin/setupAcceptanceTests.bash` if necessary
and the tool will recognise this.

During the running of the script the configured Magento store will be set to use a new test database set in the
environment file, this test database must exist and be accessible by the configured Mysql user in the database file.

The script will validate different things in the environment and let you know what to do if it finds a problem.

## Running the tests
The setup script will generate the test suite that you configure but will not run them.

For the acceptance tests to run you must have Selenium or Zalenium configured and working before the tests
will run correctly.