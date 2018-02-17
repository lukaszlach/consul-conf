#!/usr/bin/env bash
set -e
if [[ "$1" != "" ]]; then
    export CONSUL_API_URL="$1"
fi
set +e
/consul-conf/bin/consul-is-reachable.sh
if [[ "$?" != "0" ]]; then
    echo "Failed to connect to $CONSUL_API_URL"
    exit 1
fi
echo "Successfully connected to $CONSUL_API_URL"
exit 0
