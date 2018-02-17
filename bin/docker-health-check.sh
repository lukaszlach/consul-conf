#!/usr/bin/env bash
set -ex

# local connection to nginx
curl -sSf http://localhost:80/ping &>/dev/null
if nc -z 127.0.0.1 443; then
    curl -sSfk https://localhost:443/ping &>/dev/null
fi
# local process existence
pgrep php-fpm &>/dev/null
pgrep nginx &>/dev/null
/consul-conf/bin/consul-is-reachable.sh