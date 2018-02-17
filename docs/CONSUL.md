# Consul.conf

## Consul

### Storing values in key-value storage

#### `consul`

```bash
consul kv put project/key value

# optionally pass custom ACL token
consul kv put -token=custom-acl-token project/key value
# or
CONSUL_HTTP_TOKEN=custom-acl-token \
    consul kv put project/key value
```

#### `curl`

```bash
curl -X PUT -d 'value' \
    http://localhost:8500/v1/kv/project/key

# optionally pass custom ACL token
curl -X PUT -d 'value' \
    -H"X-Consul-Token: custom-acl-token" \
    http://localhost:8500/v1/kv/project/key
```

#### Consul.conf Docker container

```bash
docker exec consul_conf \
    kv-set project/key value

# optionally pass custom ACL token
docker exec consul_conf \
    kv-set project/key value custom-acl-token
# or
docker exec -e CONSUL_ACL_TOKEN=custom-acl-token consul_conf \
    kv-set project/key value
```

#### Consul Web UI

https://www.consul.io/intro/getting-started/ui.html

#### Hashi UI

https://github.com/jippi/hashi-ui