#!/usr/bin/env bash
set -e
HTTP_CODE=`curl -sk -m 1 -w "%{http_code}" -o /dev/null "$CONSUL_API_URL/v1/kv/consul-conf-health-check"`
if [[ "$HTTP_CODE" == "200" ]] || [[ "$HTTP_CODE" == "404" ]]; then
    exit 0
fi
exit 1