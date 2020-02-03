#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd ${DIR};

for f in testsuite/Magento/*;
do
	TEST_SUITE=$(echo $f | cut -d '/' -f 3);
	PHPUNIT_FILE=./phpunit.magento.${TEST_SUITE}.xml;
	rm -f ${PHPUNIT_FILE};
	cp phpunit.xml.dist ${PHPUNIT_FILE};
	xmllint --shell ${PHPUNIT_FILE} << EOF
cd /phpunit/testsuites/testsuite/directory[1]
set $f
save

EOF
done

for f in testsuite/Magento/*;
do
	TEST_SUITE=$(echo $f | cut -d '/' -f 3);
	PHPUNIT_FILE=./phpunit.magento.${TEST_SUITE}.xml;
	echo "Running tests for ${TEST_SUITE}"
	php ../../../vendor/bin/phpunit -c ${PHPUNIT_FILE} |& tee var/${TEST_SUITE}TestResults.txt
done

cd var

grep "Fatal error\|Error in \|ERROR\|OK" *txt| \
 sed 's#:.*Fatal error.*#:Fatal error#'| \
 sed 's#:.*Error in fixture.*#:Error in fixture#'| \
 sort -u| \
 column -s ':' -t| \
 GREP_COLOR='01;31' grep --color=always '^.*ERROR.*\|$'| \
 GREP_COLOR='01;32' grep --color=always '^.*OK.*\|$'| \
 GREP_COLOR='01;34' grep --color=always '^.*Error in fixture.*\|$'| \
 GREP_COLOR='01;35' grep --color=always '^.*Fatal error\|$'

