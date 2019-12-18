#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'

base_dir="${DIR}/../"
directoryToTest="${base_dir}app/code/EdmondsCommerce"


php $base_dir/vendor/bin/phpcpd ${directoryToTest}
php $base_dir/vendor/bin/php-cs-fixer fix ${directoryToTest}
php $base_dir/vendor/bin/phpcbf --standard=${base_dir}vendor/magento/magento-coding-standard/Magento2/ruleset.xml ${directoryToTest}
php $base_dir/vendor/bin/phpcs --standard=${base_dir}vendor/magento/magento-coding-standard/Magento2/ruleset.xml ${directoryToTest}
php $base_dir/vendor/bin/phpmd ${directoryToTest} text ${base_dir}dev/tests/static/testsuite/Magento/Test/Php/_files/phpmd/ruleset.xml
#php $base_dir/bin/magento dev:tests:run static
