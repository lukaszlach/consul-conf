#!/usr/bin/env bash
set -e
. /consul-conf/bin/core.sh

if [[ "$LDAP_ENABLED" != "1" ]] && [[ "$LDAP_ENABLED" != "true" ]]; then
    log_error "LDAP is disabled"
    exit 100
fi
USERNAME="$1"
PASSWORD="$2"
LDAP_OBJECT="$LDAP_ATTR_UID=$USERNAME,$LDAP_BIND_DN"
#LDAP_OBJECT=`curl "$LDAP_URL/$LDAP_BIND_DN?memberOf?sub?($LDAP_ATTR_UID=$USERNAME)" -ks | grep -E "^DN: " | cut -d" " -f2`
if [ "$LDAP_OBJECT" == "" ]; then
    log_error "LDAP object not found for $USERNAME"
    exit 101
fi
if ! curl "$LDAP_URL/$LDAP_OBJECT" -u "$LDAP_OBJECT:$PASSWORD" -ks &>/dev/null; then
    log_error "Invalid password for $USERNAME"
    exit 102
fi
exit 0