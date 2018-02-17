DOCKER_IMAGE_NAME        := consul-conf
DOCKER_IO_IMAGE_NAME     := lukaszlach/consul-conf
DOCKER_LOCAL_IMAGE_NAME  := registry-gitlab.llach.pl/llach/consul-conf
DOCKER_IMAGE_TAG         ?= latest
VERSION                  ?= $(shell cat .version)
DOCKER_VERSION_IMAGE_TAG ?= $(VERSION)
BUILD_ID                 ?= dev

.PHONY: build compose-build push local-push version-push cli start stop restart kill logs run docs

build:
	docker build --build-arg VERSION=${VERSION} --build-arg BUILD_ID=${BUILD_ID} -t ${DOCKER_IMAGE_NAME}:${DOCKER_IMAGE_TAG} .

compose-build:
	docker-compose build --pull
	docker tag consul-conf:latest ${DOCKER_IMAGE_NAME}:${DOCKER_IMAGE_TAG}

local-push:
	docker tag $(DOCKER_IMAGE_NAME):$(DOCKER_IMAGE_TAG) $(DOCKER_LOCAL_IMAGE_NAME):$(DOCKER_IMAGE_TAG)
	docker push $(DOCKER_LOCAL_IMAGE_NAME):$(DOCKER_IMAGE_TAG)

version-push:
	docker tag $(DOCKER_IMAGE_NAME):$(DOCKER_IMAGE_TAG) $(DOCKER_IO_IMAGE_NAME):$(DOCKER_VERSION_IMAGE_TAG)
	docker push ${DOCKER_IO_IMAGE_NAME}:${DOCKER_VERSION_IMAGE_TAG}

push:
	docker tag $(DOCKER_IMAGE_NAME):$(DOCKER_IMAGE_TAG) $(DOCKER_IO_IMAGE_NAME):$(DOCKER_IMAGE_TAG)
	docker push ${DOCKER_IO_IMAGE_NAME}:${DOCKER_IMAGE_TAG}

run:
	docker-compose -f docker-compose.yml -f docker-compose-dev.yml run --rm consul-conf

cli:
	docker run --rm --entrypoint "" -it ${DOCKER_IMAGE_NAME}:${DOCKER_IMAGE_TAG} bash

###

start:
	docker-compose -f docker-compose.yml -f docker-compose-dev.yml up -d --force-recreate

stop:
	docker-compose stop

restart: start

kill:
	docker-compose kill

logs:
	docker-compose logs -f

###

docs:
	cat docs/CONFIGURE.md | grep -e '^##### ' | sed 's/^##### //g' | sort | sed 's/\(.*\)/* [\1](#\1)/g'