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

gitIgnoreFile="$magentoRoot/.gitignore";
envPath="$magentoRoot/tests.bash";
# ----------------------- #
# Validation of env file  #
# ----------------------- #
if [[ ! -f "$envPath" ]]
then
    echo "Env file does not exist, create your tests.bash file at $envPath";
    exit 1;
fi
echo "Found env file at $envPath";

envIsGitIgnored="$(grep -E "^tests.bash$" $gitIgnoreFile || true)";
if [[ "$envIsGitIgnored" != "tests.bash" ]]
then
    echo "Make sure your tests.bash file is git ignored.
Use a tests.bash.dist file to version things without sensitive data such as database details";
    exit 1;
fi

echo "Env file is git ignored correctly";

# ------------------------------------#
# Populate from environment variables #
# ------------------------------------#

requiredVariables="
BASE_URL
DB_NAME
DB_USER
DB_PASS
MAGENTO_BACKEND_NAME
MAGENTO_ADMIN_USERNAME
MAGENTO_ADMIN_PASSWORD
THEME_ID
TEST_SUITE
";

source $envPath;

for v in $requiredVariables
do
    # Check we have the required values
    if [[ ! -v $v ]]
    then
        echo "$v is not set";
        exit 1;
    fi
done

echo "Required env variables are set";

# --------------------- #
# End of Variable setup #
# --------------------- #

echo "
---------------------
Pre run sanity checks
---------------------
"

magento()
{
    php ${magentoRoot}/bin/magento $@;
}

magerun()
{
    php "$mageRunFullPath" $@;
}

mftf()
{
    php ${magentoRoot}/vendor/bin/mftf $@;
}

mageRunName="magerun";
mageRunInstalledGlobally="$(command -v $mageRunName || true)"
if [[ "$mageRunInstalledGlobally" == "" ]]
then
    echo "Magerun must be installed globally and named 'magerun' in the system path";
    exit 1;
fi
mageRunFullPath="$(which magerun)";

hostsFileHasDomain=$(grep $BASE_URL /etc/hosts || true);
if [[ $hostsFileHasDomain == "" ]]
then
    echo "/etc/hosts does not contain your testing domain and IP ${hostsFileHasDomain}";
    exit 1;
fi

mftf build:project;

magentoAcceptanceEnv="${magentoRoot}/dev/tests/acceptance/.env";
if [[ ! -f ${magentoAcceptanceEnv} ]]
then
    echo "Testing env file not present in ${magentoAcceptanceEnv}, creating default based on tests.bash";
    echo "\
MAGENTO_BASE_URL=https://${BASE_URL}/
MAGENTO_BACKEND_NAME=${MAGENTO_BACKEND_NAME}
MAGENTO_ADMIN_USERNAME=${MAGENTO_ADMIN_USERNAME}
MAGENTO_ADMIN_PASSWORD=${MAGENTO_ADMIN_PASSWORD}
WAIT_TIMEOUT=180
" > ${magentoAcceptanceEnv};
fi

CLI_SETUP=`curl -o /dev/null -k -w "%{http_code}" -s https://${BASE_URL}/dev/tests/acceptance/utils/command.php`

if [[ ${CLI_SETUP} == 404 ]]
then
   echo "
Web based CLI is not setup! Follow the instructions here
https://devdocs.magento.com/mftf/docs/getting-started.html#nginx-settings
"

   exit 5
fi

testSuiteDefinitionPath="$magentoRoot/dev/tests/acceptance/tests/_suite/${TEST_SUITE}.xml";
if [[ ! -f "$testSuiteDefinitionPath" ]]
then
    echo "";
    echo "Could not find test suite definition file at $testSuiteDefinitionPath";
    echo "Make sure you have created it and defined what tests you wish to run!";
    exit 1;
fi

echo "
------------------------------------------------------------
Going to clear out the caches and sessions before continuing
------------------------------------------------------------
"
rm -rf ${magentoRoot}/var/cache/*
rm -rf ${magentoRoot}/var/sessions/*

echo "
-------------------------------------------
Going to install a clean version of Magento
-------------------------------------------
"
magento setup:install \
    --language="en_US" \
    --timezone="UTC" \
    --currency="USD" \
    --base-url="http://${BASE_URL}/" \
    --base-url-secure="https://${BASE_URL}/" \
    --admin-firstname="John" \
    --admin-lastname="Doe" \
    --backend-frontname="${MAGENTO_BACKEND_NAME}" \
    --admin-email="admin@example.com" \
    --admin-user="${MAGENTO_ADMIN_USERNAME}" \
    --use-rewrites=1 \
    --admin-use-security-key=0 \
    --admin-password="${MAGENTO_ADMIN_PASSWORD}" \
    --db-name="${DB_NAME}" \
    --db-user="${DB_USER}" \
    --db-password=${DB_PASS} \
    --cleanup-database

echo "
---------------------------------------
Going to set the required configuration
---------------------------------------
"
magento config:set web/secure/use_in_adminhtml 1
magento config:set admin/security/admin_account_sharing 1
magento config:set cms/wysiwyg/enabled disabled

echo "
-----------------------
Using the correct theme
-----------------------
"
magerun db:query \
"INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default', 0, 'design/theme/theme_id', ${THEME_ID})"

echo "
-------------------------------------
Setting the site into production mode
-------------------------------------
"
magento deploy:mode:set production

echo "
------------------------------------------
Generating test suite
------------------------------------------
"
mftf generate:suite ${TEST_SUITE} -r

echo "
----------------
$(hostname) $0 completed
----------------
"
