#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
echo "
===========================================
$(hostname) $0 $@
===========================================
"
magentoRoot="$(realpath $DIR/../../../../)"
if [[ ! -f "$magentoRoot/composer.json" ]]
then
    magentoRoot="$(realpath $DIR/../../)";
    if [[ ! -f "$magentoRoot/composer.json" ]]
    then
        echo "Could not detect Magento root directory";
        exit 1;
    fi
fi

envPath="$magentoRoot/tests.bash";
if [[ ! -f "$envPath" ]]
then
    echo "Env file does not exist, create your tests.bash file at $envPath";
    exit 1;
fi
echo "Found env file at $envPath";

mftf()
{
    php ${magentoRoot}/vendor/bin/mftf $@;
}

exists(){
  if [ "$2" != in ]; then
    echo "Incorrect usage."
    echo "Correct usage: exists {key} in {array}"
    return
  fi
  eval '[ ${'$3'[$1]+muahaha} ]'
}

source $envPath;

echo "
------------------------
Test Runner
------------------------
"

read -p "Are you sure you want to run tests now (y/n)? " choice

case $choice in
    y|Y|yes|Yes|YES )
        answer='y'
    ;;
    * )
        answer='n'
    ;;
esac

if [ "$answer" != 'y' ]; then
    exit
fi

read -p "Would you like to run all tests (y/n)? " allTestChoice
case $allTestChoice in
    y|Y|yes|Yes|YES )
        allTest='y'
    ;;
    * )
        allTest='n'
    ;;
esac

if [ "$allTest" == 'y' ]; then
    php ${magentoRoot}/vendor/bin/mftf run:group ${TEST_SUITE};
    exit
fi

testDir="${magentoRoot}/dev/tests/acceptance/tests/functional/Magento/FunctionalTest/_generated/${TEST_SUITE}"
num=1
for file in $testDir/*; do
    fileName=${file##*/}
    fnames+=(${fileName::-8})
    echo "$num) " "${fileName::-8}"
    let "num += 1"
done

read -p "Which test would you like to run? " testNum
if [ "$testNum" = '' ]; then
    echo "No Test Selected";
    exit 1;
fi
if [ $(($testNum)) != $testNum ]; then
    echo "Input must be a number";
    exit 1;
fi
testNum=$(($testNum-1))
if ! exists testNum in fnames; then
    echo "File not found in directory";
    exit 1;
fi
echo "
-------------------------------------------------
Running Test ${fnames[$testNum]}
-------------------------------------------------
"
mftf run:test ${TEST_SUITE}:${fnames[$testNum]};
