#!/usr/bin/env bash
set -e
CONSUL_KEY="$1"
VALUE="$2"
ACL_TOKEN="$3"
if [[ "$ACL_TOKEN" != "" ]]; then
    export CONSUL_ACL_TOKEN="$ACL_TOKEN"
fi
/consul-conf/bin/consul-set.sh "$CONSUL_KEY" "$VALUE"