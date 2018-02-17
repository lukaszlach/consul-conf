# Consul.conf

## Configuration :wrench:

| Section | `/etc/consul-conf/consul-conf.conf` variable |
| --- | --- |
| [Core](#core) | [HTTP_PORT](#http_port) |
| [Consul](#consul) | [CONSUL_API_URL](#consul_api_url), [CONSUL_ACL_TOKEN](#consul_acl_token) |
| [Security](#security) | [HTTP_ALLOW](#http_allow), [CRYPT_KEY](#crypt_key) |
| [HTTPs](#https) | [HTTPS_PORT](#https_port) |
| [Basic Authorization](#authorization) | [BASIC_AUTH](#basic_auth) |
| [LDAP](#ldap) | [LDAP_ENABLED](#ldap_enabled), [LDAP_URL](#ldap_url), [LDAP_BIND_DN](#ldap_bind_dn), [LDAP_ATTR_UID](#ldap_attr_uid) |
| [Slack](#ldap) | [NOTIFY_SLACK](#notify_slack), [SLACK_URL](#slack_url), [SLACK_ROOM](#slack_room)  |
| [HipChat](#ldap) | [NOTIFY_HIPCHAT](#notify_hipchat), [HIPCHAT_API](#hipchat_api), [HIPCHAT_TOKEN](#hipchat_token), [HIPCHAT_ROOM](#hipchat_room) |

### Core

##### HTTP_PORT

* **Type**: number
* **Default**: 80

Listen for HTTP requests on this port.

### Consul

##### CONSUL_API_URL

* **Type**: URL
* **Default**: http://consul.service.consul:8500

URL where Consul HTTP API is reachable.

> Remember you cannot simply put `http://localhost:8500` here, as Docker container has different perspective on "localhost". If your local Consul agent listens only on `localhost`, you need to make it also listen on `docker0` interface and use it's IP address.

##### CONSUL_ACL_TOKEN

* **Type**: string
* **Default**: (empty)

Consul ACL token, if used and required.

### Security

##### CRYPT_KEY

* **Type**: string
* **Default**: (empty, use built-in)

Encryption key for handling in-code encryption, uses built-in one by default.

##### HTTP_ALLOW

* **Type**: Network mask
* **Default**: 0.0.0.0/0
* **Example**: 192.168.0.0/24

Network mask allowed to view the web interface, allows everyone by default.

#### HTTPs

##### HTTPS_PORT

* **Type**: number
* **Default**: 443

Listen for HTTPs requests on this port.

> For HTTPs to work you need to place `https/cert.pem` and `https/key.pem` certificate files in `/etc/consul-conf` directory.

### Authorization

> Session cookie has no lifetime set meaning you will be logged in as long as browser tab is opened.

#### Basic

##### BASIC_AUTH

* **Type**: string
* **Default**: (empty)
* **Example**: admin:admin

Use basic plain `username:password` convention to enable basic authorization.

#### LDAP

##### LDAP_ENABLED

* **Type**: boolean
* **Default**: false

Whether to use LDAP in log in form.

##### LDAP_URL

* **Type**: URL
* **Default**: (empty)
* **Example**: ldaps://ldap-server.com:636

LDAP URL starting from `ldap://` or `ldaps://`.

##### LDAP_ATTR_UID

* **Type**: string
* **Default**: uid

Name of the field containing user name in LDAP directory.

##### LDAP_BIND_DN

* **Type**: string
* **Default**: (empty)
* **Example**: ou=users,ou=team,dc=company

LDAP bind distinguished name (DN).

### Notifications

#### Slack

##### NOTIFY_SLACK

* **Type**: boolean
* **Default**: false

Whether to send Slack notifications.

##### SLACK_ROOM

* **Type**: string
* **Default**: (empty)
* **Example**: consul-conf

Slack room name.

##### SLACK_URL

* **Type**: URL
* **Default**: (empty)
* **Example**: https://hooks.slack.com/services/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

Slack "Incoming WebHook" URL.

#### HipChat

##### NOTIFY_HIPCHAT

* **Type**: boolean
* **Default**: false

Whether to send HipChat notifications.

##### HIPCHAT_API

* **Type**: URL
* **Default**: (empty)
* **Example**: https://hipchat.server.com

Base URL of your HipChat API without trailing slash.

##### HIPCHAT_ROOM

* **Type**: number
* **Default**: (empty)

HipChat room ID.

##### HIPCHAT_TOKEN

* **Type**: string
* **Default**: (empty)

HipChat room notification token.

### Default configuration file

```bash
HTTP_PORT=8080
HTTPS_PORT=443
HTTP_ALLOW=0.0.0.0/0
CRYPT_KEY=

# Consul
CONSUL_API_URL=http://consul.service.consul:8500
CONSUL_ACL_TOKEN=

# Authorization
BASIC_AUTH=
LDAP_ENABLED=0
LDAP_URL=
LDAP_ATTR_UID=
LDAP_BIND_DN=

# Notifications
NOTIFY_SLACK=0
SLACK_ROOM=
SLACK_URL=

NOTIFY_HIPCHAT=0
HIPCHAT_API=
HIPCHAT_ROOM=
HIPCHAT_TOKEN=
```