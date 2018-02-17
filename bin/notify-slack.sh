#!/usr/bin/env bash
set -e

if [[ "$NOTIFY_SLACK" != "1" ]] && [[ "$NOTIFY_SLACK" != "true" ]]; then
    log_warning "Slack notifications are disabled"
    exit 0
fi
MESSAGE="$1"
COLOR="${2:-gray}"

# reformat message
MESSAGE=$(echo "$MESSAGE" | sed 's#</*code>#`#g; s#</*b>#**#g')
MESSAGE=`echo "$MESSAGE" | jq -R '.'`
# translate color names to Material color codes
case "$COLOR" in
    green)
        COLOR="#4CAF50"
    ;;
    red)
        COLOR="#F44336"
    ;;
    *)
        COLOR="#9E9E9E"
    ;;
esac

JSON='{"channel": "#'"$SLACK_ROOM"'", "username": "Consul.conf", "text": "", "icon_emoji": ":globe_with_meridians:", "attachments": [{ "text": '"$MESSAGE"', "color": "'"$COLOR"'", "mrkdwn_in": ["text"] }]}'
curl -sSf -m 3 \
    --data-urlencode "payload=$JSON" \
    "$SLACK_URL"