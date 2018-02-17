#!/usr/bin/env bash
set -e

if [ "$NOTIFY_SLACK" != "1" ]; then
    log_warning "Slack notifications are disabled"
    exit 0
fi
MESSAGE="$1"
COLOR="${2:-gray}"

# reformat message
MESSAGE=$(echo "$MESSAGE" | sed 's#</*pre>#```#g; s#</*b>#*#g')
MESSAGE=`echo "$MESSAGE" | jq -R '.'`
# translate color names to Material color codes
case "$COLOR" in
    gray)
        COLOR="#9E9E9E"
    ;;
    green)
        COLOR="#4CAF50"
    ;;
    red)
        COLOR="#F44336"
    ;;
esac

JSON='{"channel": "#'"$SLACK_ROOM"'", "username": "Consul.conf", "text": "", "icon_emoji": ":package:", "attachments": [{ "text": '"$MESSAGE"', "color": "'"$COLOR"'", "mrkdwn_in": ["text"] }]}'
curl -sSf -m 3 \
    --data-urlencode "payload=$JSON" \
    "$SLACK_URL"