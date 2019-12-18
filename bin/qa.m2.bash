#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'

bin_dir=${DIR}

base_dir="${DIR}/../../"

err_report() {
    echo "
********************************************************
QA Checks Failed - Please check the output and fix these
********************************************************
"
}

trap 'err_report $LINENO' ERR

function usage(){
    echo "

Usage:
    ./$0 [directoryToTest]

    [directoryToTest] - The directory to test relative to root directory (default app/code)

    "
}

if (( $# > 1 ))
then
    usage
    exit 1
fi

directoryToTest=${1:-"app/code"}

if [[ ${directoryToTest} == 'help' ]]
then
    usage
    exit 1
fi

directoryToTest="${base_dir}${directoryToTest}"

echo "
---------------------------------
Running CS fixer against the code
---------------------------------
"
php $bin_dir/php-cs-fixer fix ${directoryToTest}
echo "
-----------------------------------
Running Beautifier against the code
-----------------------------------
"
set +e
php $bin_dir/phpcbf \
    --standard=${base_dir}vendor/magento/magento-coding-standard/Magento2/ruleset.xml \
    ${directoryToTest} || true # BF return a non 0 exit code when it fixes things
set -e
echo "
-------------------------------------
Running Code Sniffer against the code
-------------------------------------
"
php $bin_dir/phpcs \
    --standard=${base_dir}vendor/magento/magento-coding-standard/Magento2/ruleset.xml \
    ${directoryToTest}
echo "
--------------------------------------
Running Mess Detector against the code
--------------------------------------
"
php $bin_dir/phpmd ${directoryToTest} text ${base_dir}dev/tests/static/testsuite/Magento/Test/Php/_files/phpmd/ruleset.xml
echo "
--------------------------------------------
Running Copy Paste Detector against the code
--------------------------------------------
"
php $bin_dir/phpcpd ${directoryToTest}
#php $base_dir/bin/magento dev:tests:run static

echo "
+++++++++++++++++++++++++
QA Passed - Now run tests
+++++++++++++++++++++++++
"
