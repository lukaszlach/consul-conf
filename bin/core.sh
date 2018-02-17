#!/usr/bin/env bash
CL_RED='\033[0;31m'
CL_YELLOW='\033[0;93m'
CL_GRAY='\033[0;37m'
CL_WHITE='\033[0;30m'
CL_DEFAULT='\033[0m'
. /consul-conf/bin/project-config.sh

log () {
    echo -e "$CL_GRAY[Notice]$CL_DEFAULT $@"
}
log_notice () {
    log "$@"
}
log_error () {
    echo -e "$CL_RED[Error]$CL_DEFAULT $@"
}
log_warning () {
    echo -e "$CL_YELLOW[Warning]$CL_DEFAULT $@"
}
project_header () {
    echo -e "$CL_WHITE$PROJECT_NAME $PROJECT_VERSION$CL_DEFAULT"
}
project_env () {
    printenv | \
        sed 's/^\(CRYPT_KEY\|CONSUL_ACL_TOKEN\|BASIC_AUTH\|HIPCHAT_TOKEN\|SLACK_URL\)=.*$/\1=(hidden)/g' | \
        sort | \
        sed 's/^/    /g'
}
consul_kv_request () {
    local CONSUL_KEY="$1"
    shift
    curl -sfk -m 3 -H"X-Consul-Token: $CONSUL_ACL_TOKEN" "$CONSUL_API_URL/v1/kv/$CONSUL_KEY" "$@"
}