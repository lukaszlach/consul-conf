#!/usr/bin/env bash
set -e
. /consul-conf/bin/core.sh

BASE_PATH="${1}"
consul_kv_request "$BASE_PATH?recurse=true" | \
    jq -r '[.[] | {(.Key|tostring): .Value}] | add | with_entries(select(.key | test("/$") | not))'