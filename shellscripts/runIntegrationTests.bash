#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd ${DIR};
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'

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

cd ../dev/tests/integration/


if [[ $copyConfig == 'true' ]]
then
    cp ../../../app/code/EdmondsCommerce/Testing/Test/Integration/phpunit.edmondscommerce.xml ./
fi

php ../../../vendor/bin/phpunit -c phpunit.edmondscommerce.xml
