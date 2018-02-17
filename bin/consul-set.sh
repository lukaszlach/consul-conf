#!/usr/bin/env bash
set -e
. /consul-conf/bin/core.sh

CONSUL_KEY="$1"
VALUE="$2"
if ! consul_kv_request "$CONSUL_KEY" -X PUT -d "$VALUE" | grep true &>/dev/null; then
    log_error "Failed to store $CONSUL_KEY"
    exit 1
fi