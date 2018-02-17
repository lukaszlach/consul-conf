#!/usr/bin/env bash
# Consul.conf
## (c) 2018 Łukasz Lach <llach@llach.pl>
## https://github.com/lukaszlach/consul-conf
set -e +f
. /consul-conf/bin/core.sh

if [[ "$1" != "consul-conf" ]]; then
    exec "$@"
fi

project_header
log "Date: `date`"
log "Environment:"
project_env

log "Configuring logging"
mkfifo /var/log/application.log
chmod 666 /var/log/application.log
bash -c 'tail -F /var/log/application.log > /proc/1/fd/1' &

log "Configuring php-fpm"
rm -f /etc/php/7.0/fpm/pool.d/www.conf /etc/php/7.0/fpm/php-fpm.conf
ln -s /consul-conf/docker/php/php-fpm.conf /etc/php/7.0/fpm/php-fpm.conf
ln -s /consul-conf/docker/php/pool.conf /etc/php/7.0/fpm/pool.d/pool.conf
ln -s /consul-conf/docker/php/web.ini /etc/php/7.0/fpm/conf.d/99-web.ini
log "Starting php-fpm"
php-fpm7.0 -v | head -n1
php-fpm7.0 -D

log "Configuring nginx"
rm -f /etc/nginx/nginx.conf /etc/nginx/sites-enabled/*
ln -s /consul-conf/docker/nginx/nginx.conf /etc/nginx/nginx.conf
ln -s /consul-conf/docker/nginx/web.conf /etc/nginx/sites-enabled/web.conf
# env HTTP_ALLOW
log "Setting http_allow=$HTTP_ALLOW"
sed 's/^#allow//g; s#$HTTP_ALLOW#'"$HTTP_ALLOW"'#g' -i /etc/nginx/sites-enabled/*.conf
if [ -f /etc/consul-conf/https/cert.pem ] && [ -f /etc/consul-conf/https/key.pem ]; then
    # env HTTPS_PORT
    log "Enabling HTTPs, setting https_port=$HTTPS_PORT"
    sed 's/^#ssl//g; s#$HTTPS_PORT#'"$HTTPS_PORT"'#g' -i /etc/nginx/sites-enabled/*.conf
fi
log "Starting nginx"
nginx -v
exec nginx -g 'daemon off;'