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
