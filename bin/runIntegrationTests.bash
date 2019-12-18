#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd ${DIR};
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
bin_dir=${DIR}
base_dir="${DIR}/../../"
intergration_test_folder="${base_dir}/dev/tests/integration/"
database_config_file="${intergration_test_folder}/etc/install-config-mysql.php"

function usage(){
    echo "

Usage:
    ./$0 [copyConfig]

    [copyConfig] - Copies the edmonds edmondscommerce phpunit.xml (default true)

    "
}

if (( $# > 1 ))
then
    usage
    exit 1
fi

copyConfig=${1:-true}

if [[ $copyConfig == 'help' ]]
then
    usage
    exit 1
fi

cd ${intergration_test_folder}

if [[ ! -f ${database_config_file}  ]]
then
    echo "
**********************************************************************************************
You must create the database config file before the tests can be run. Run the following

cp ${database_config_file}.dist ${database_config_file}

And then update the new file. See here for more details

https://devdocs.magento.com/guides/v2.3/test/integration/integration_test_execution.html#setup
**********************************************************************************************
"
    exit 5
fi

if [[ $copyConfig == 'true' ]]
then
    cp ${base_dir}vendor/edmondscommerce/module-testing/Test/Integration/phpunit.edmondscommerce.xml ./
fi

php ../../../vendor/bin/phpunit -c phpunit.edmondscommerce.xml
