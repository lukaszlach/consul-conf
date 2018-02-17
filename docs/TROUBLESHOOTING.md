# Consul.conf

## Troubleshooting

### No connection to Consul HTTP API

Use `kv-ping` command-line tool to check if currently set or custom Consul HTTP API is reachable from inside of the Docker container. The address you will find working needs to be placed inside `CONSUL_API_URL` configuration variable followed by Consul.json restart.

```bash
# check currently set API URL
$ docker exec consul_conf kv-ping
Failed to connect to http://consul.service.consul:8500

# check custom API URL
$ docker exec consul_conf kv-ping "http://consul-server-1:8500"
Successfully connected to http://consul-server-1:8500
```

If you have no external connectivity to Consul HTTP API and local agent listens only on localhost - you need to configure the agent to listen additionally on `docker0` interface and use it's IP address.

### Container is not healthy

Before creating an issue check what causes the container to be unhealthy. Run health-check script manually and check it's output status code:

```bash
$ docker exec consul_conf /consul-conf/bin/docker-health-check.sh
+ curl -sSf http://localhost:80/ping
+ nc -z 127.0.0.1 443
+ pgrep php-fpm
+ pgrep nginx
+ /consul-conf/bin/consul-is-reachable.sh

$ echo $?
0
```