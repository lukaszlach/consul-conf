#!/usr/bin/env bash
set -e

/consul-conf/bin/consul-get-all-keys.sh | \
    jq -r 'map(select(. | endswith(".dashboard/.config")) | gsub("/\\.dashboard/\\.config$"; ""))'