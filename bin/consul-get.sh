#!/usr/bin/env bash
set -e
. /consul-conf/bin/core.sh

CONSUL_KEY="$1"
(
    consul_kv_request "$CONSUL_KEY" | \
        jq -r '.[0].Value'
) || true