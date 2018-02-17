# Consul.conf

![Version](https://img.shields.io/badge/version-1.0-lightgrey.svg?style=flat)
[![Docker pulls](https://img.shields.io/docker/pulls/lukaszlach/consul-conf.svg?label=docker+pulls)](https://hub.docker.com/r/lukaszlach/consul-conf)
[![Donate PayPal](https://img.shields.io/badge/donate-PayPal-yellow.svg?style=flat)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=lach@php5.pl&lc=US&item_name=Consul.conf&no_note=0&cn=&curency_code=USD&bn=PP-DonationsBF:btn_donateCC_LG.gif:NonHosted)

**Consul.conf** provides responsive web interface for managing configuration of your services in [Consul](https://www.consul.io/) key-value storage through beautiful and customizable dashboards built with [Material Design for Bootstrap](https://mdbootstrap.com/) and [Phalcon Framework](https://github.com/phalcon/cphalcon). 

![](https://user-images.githubusercontent.com/5011490/36346134-0b77573a-1439-11e8-81be-722b0e89f2a6.png)

**Consul.conf** is [easy to install](#install) and [integrate](#quick-guide) as only creating one single key in Consul key-value is required to introduce your first dashboard. All already existing fields will be visible inside the web interface and can only use some customization.

- **Standalone** - distributed as a lightweight Docker image, installable with automated script.
- **[Different field types](docs/CUSTOMIZE.md#customizing-field)** - as different types of information require different types of fields, choose either [text](docs/CUSTOMIZE.md#text), [select](docs/CUSTOMIZE.md#select) or [checkbox](docs/CUSTOMIZE.md#checkbox).
- **[Look-and-feel](docs/CUSTOMIZE.md)** - [customize your dashboards](docs/CUSTOMIZE.md#customizing-dashboard) by changing color, icon or hiding unconfigured fields. Much more can be done when [customizing fields](docs/CUSTOMIZE.md#customizing-field) you can also pass default value, read-only or hidden flag.
- **Responsive** - works fine on tablet and mobile phone.
- **Stateless** - dashboards and fields are stored in Consul key-value, meaning Consul.conf can be run on your local machine or any server having access to Consul API and it will be able to fetch all your dashboards and customizations. It also works well with any [existing key structure it finds](#integrate).
- **[Event notifications](docs/CONFIGURE.md#notifications)** - send message to [Slack](docs/CONFIGURE.md#slack) or [HipChat](docs/CONFIGURE.md#hipchat) channel when event happens, for example user logs in or changes the value of a field on dashboard.
- **[Basic](docs/CONFIGURE.md#basic) and [LDAP](docs/CONFIGURE.md#ldap) authorization** support.
- **[HTTPs](docs/CONFIGURE.md#https)** support.

## Install

![](https://img.shields.io/badge/docker-17.05+-lightgrey.svg?style=flat)
![](https://img.shields.io/badge/docker--compose-3+-lightgrey.svg?style=flat)

Use automated install script that pulls Docker image, creates directory structure and configuration files and installs start/stop commands for you, by executing below command. If you prefer to do it manually - proceed with [usage instructions](#run-manually), Docker image will be downloaded automatically on first usage.

```bash
export CONSUL_CONF_VERSION=1.0
curl -L "https://raw.githubusercontent.com/lukaszlach/consul-conf/$CONSUL_CONF_VERSION/install" | bash
```

You will see "Consul.conf installed and running" message after installation is done, configuration directory `/etc/consul-conf` holds `consul-conf.conf` that allows you to modify settings. Installation process also adds `consul-conf-start` and `consul-conf-stop` management commands.

> On default settings Consul.conf listens on port 8080.

You can use the same commands to upgrade (or downgrade) Consul.conf, all your configuration values, dashboards and fields will be preserved. Just change `CONSUL_CONF_VERSION` to desired version.

Read the [Troubleshooting Guide](docs/TROUBLESHOOTING.md) in case of problems with connecting to Consul HTTP API or running the Docker image.

### Install examples

Optionally you can install example fully customized dashboards by executing below command. Script expects Consul HTTP API on `http://localhost:8500`, you can change it with `CONSUL_API_URL` environment variable.

```bash
curl -L "https://raw.githubusercontent.com/lukaszlach/consul-conf/$CONSUL_CONF_VERSION/install-examples" | bash
```

## Quick guide

Below examples expect Consul API available on `localhost:8500` and Consul.conf listening on `localhost:8080`. See all different ways you can [store new value inside Consul key-value storage](docs/CONSUL.md#storing-values-in-key-value-storage).

### Step 1. Create dashboard

```bash
# create dashboard under "project/" base path
curl -X PUT -d '{"name": "Example dashboard", "color": "deep-purple"}' \
    http://localhost:8500/v1/kv/project/.dashboard/.config
```

### Step 2. Create field

```bash
# set value for "project/key" path
curl -X PUT -d 'value' \
    http://localhost:8500/v1/kv/project/key

# create dashboard field for "project/key" path
curl -X PUT -d '{"name": "Example key", "icon": "rocket"}' \
    http://localhost:8500/v1/kv/project/.dashboard/key
```

### Step 3. See how it looks like

Visit [http://localhost:8080](http://localhost:8080), you will see a dashboard list with your newly added "Example dashboard" already available. On the dashboard itself you can see "Example key", change it's value and save in Consul key-value storage.

You can now configure your projects basing on the value of `project/key` key in Consul key-value storage and use Consul.conf to handle and propagate it's changes.

![](https://user-images.githubusercontent.com/5011490/36344842-7e7550ac-1420-11e8-86a4-843d2dce816a.png)

Dashboard list: http://localhost:8080/d

![](https://user-images.githubusercontent.com/5011490/36344862-031ef6e6-1421-11e8-9227-78c881142534.png)

Example dashboard: http://localhost:8080/d/project

## Customize

Consul.conf allows customizing dashboards and fields by setting their name, description and look-and-feel attributes like color or icon.

See separate document on how to [create and customize dashboards and fields](docs/CUSTOMIZE.md).

## Configure

Consul.conf is configured using `/etc/consul-conf/consul-conf.conf` file which is used by `docker-compose.yml` file from the same directory to start and stop the service, environment variables are passed automatically. This file has simple `FIELD=value` structure, all lines starting with `#` are ignored.

> If you are running Docker image manually, you need to pass environment variables manually as well.

See separate document for [list of currently recognized configuration options](docs/CONFIGURE.md).

## Integrate

Consul.conf works easily with any existing key structure it finds, it mainly looks for `.dashboard/.config` key when scanning for dashboards. When such key is found, even with empty JSON object inside - it's base path will be visible inside the web UI as separate dashboard.

By default all existing keys will be visible and editable using text inputs, even when no corresponding field configuration key is present in Consul key-value storage. All found nested keys are also rendered.

## Monitor

Docker container logs all important events with details - all entries include username of logged in user and his IP address. Access logs from nginx are also passed, although limited to non-GET requests. Some environment variables' values are hidden in logs, mostly these holding credentials, ACL tokens etc.

You can view logs by executing `docker logs consul_conf -f` but it is recommended to forward them to external aggregator like syslog.

Example log output:

```
[2018-02-16T20:57:45+00:00] [uid=admin, ip=172.20.0.1] Logged in
172.20.0.1 - - [16/Feb/2018:20:57:45 +0000] "POST /login HTTP/1.1" 200 5

[2018-02-16T20:58:57+00:00] [uid=admin, ip=172.20.0.1] Stored project/string-key=value
[2018-02-16T20:58:57+00:00] [uid=admin, ip=172.20.0.1] Stored project/select-key=option3
[2018-02-16T20:58:57+00:00] [uid=admin, ip=172.20.0.1] Stored project/checkbox-key=true
172.20.0.1 - - [16/Feb/2018:20:58:57 +0000] "POST /d/project/config HTTP/1.1" 200 14
```

## Troubleshooting

[Troubleshooting guide](docs/TROUBLESHOOTING.md) is available in separate document.

## Build from source

```bash
git clone https://github.com/lukaszlach/consul-conf.git consul-conf/
cd consul-conf/
# build consul-conf:dev Docker image
make DOCKER_IMAGE_TAG=dev
```

## Run manually

You can use `docker-compose-example.yml` found in this repository as a base for your configuration and call `docker-compose up -d` to run it. 

Although, if you still want to use raw `docker` command:

```bash
# minimal example
docker run -d \
    -p 8080:80 \
    -v /etc/consul-conf:/etc/consul-conf \
    -e CONSUL_API_URL=http://consul.service.consul:8500 \
    --name consul_conf \
    lukaszlach/consul-conf:latest

# complex example
docker run -d \
    -p 8080:80 \
    -v /etc/consul-conf:/etc/consul-conf \
    -e HTTPS_PORT=443 \
    -e HTTP_ALLOW=0.0.0.0/0 \
    -e CRYPT_KEY=A3oj5abcadegkfgj2Vh \
    -e CONSUL_API_URL=http://consul.service.consul:8500 \
    -e CONSUL_ACL_TOKEN=custom-acl-token \
    -e BASIC_AUTH=admin:admin \
    -e LDAP_ENABLED=1 \
    -e LDAP_URL=ldaps://ldap.service.consul:636 \
    -e LDAP_ATTR_UID=uid \
    -e LDAP_BIND_DN=ou=users,ou=team,dc=company \
    -e NOTIFY_HIPCHAT=1 \
    -e HIPCHAT_API=https://hipchat.server.com/ \
    -e HIPCHAT_ROOM=123 \
    -e HIPCHAT_TOKEN=XTlyCeYH8rFhgjA4sJ8tu8UBnYhrmFOTPr5gM3J0 \
    -e NOTIFY_SLACK=1 \
    -e SLACK_ROOM=consul-conf \
    -e SLACK_URL=https://hooks.slack.com/services/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 \
    --name consul_conf \
    lukaszlach/consul-conf:latest
```

## Licence

MIT License

Copyright (c) 2018 Łukasz Lach <llach@llach.pl>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.