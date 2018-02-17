#!/usr/bin/env bash
set -e
. /consul-conf/bin/core.sh

if [[ "$NOTIFY_HIPCHAT" != "1" ]] && [[ "$NOTIFY_HIPCHAT" != "true" ]]; then
    log_warning "HipChat notifications are disabled"
    exit 0
fi
MESSAGE=`echo "$1" | jq -R '.'`
COLOR="${2:-gray}"
curl -sSf -m 3 \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $HIPCHAT_TOKEN" \
    -d '{"color" : "'"$COLOR"'", "message_format" : "html", "message" : '"$MESSAGE"'}' \
    "${HIPCHAT_API}/v2/room/$HIPCHAT_ROOM/notification"