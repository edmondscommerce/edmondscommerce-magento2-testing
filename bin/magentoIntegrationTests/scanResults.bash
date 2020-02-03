#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd ${DIR};
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'

cd var;

SHOW_COLOURS=${1:-always};

grep "Fatal error\|Error in \|ERROR\|OK" *txt| \
 sed 's#:.*Fatal error.*#:Fatal error#'| \
 sed 's#:.*Error in fixture.*#:Error in fixture#'| \
 sort -u| \
 column -s ':' -t| \
 GREP_COLOR='01;31' grep --color=${SHOW_COLOURS} '^.*ERROR.*\|$'| \
 GREP_COLOR='01;32' grep --color=${SHOW_COLOURS} '^.*OK.*\|$'| \
 GREP_COLOR='01;34' grep --color=${SHOW_COLOURS} '^.*Error in fixture.*\|$'| \
 GREP_COLOR='01;35' grep --color=${SHOW_COLOURS} '^.*Fatal error\|$' | \
 sort
