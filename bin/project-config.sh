#!/usr/bin/env bash
export PROJECT_NAME="Consul.conf"
export PROJECT_VERSION="${CONSUL_CONF_VERSION:-dev}"
export PROJECT_COPYRIGHT_YEAR=2018
export PROJECT_URL="https://github.com/lukaszlach/consul-conf"
export PROJECT_HELP_URL="https://github.com/lukaszlach/consul-conf"

if [[ -z "$HTTP_PORT" ]];  then export HTTP_PORT=80; fi
if [[ -z "$HTTPS_PORT" ]]; then export HTTPS_PORT=443; fi
if [[ -z "$HTTP_ALLOW" ]]; then export HTTP_ALLOW="0.0.0.0/0"; fi

# load configuration file manually in case of environment was not passed properly
#if [[ -f /etc/consul-conf/consul-conf.conf ]]; then
#    set -a
#    . /etc/consul-conf/consul-conf.conf
#    set +a
#fi