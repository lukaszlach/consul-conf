FROM debian:stretch-slim AS build
ENV TINI_VERSION=v0.16.1

RUN apt-get update && \
    apt-get install -y composer make unzip
WORKDIR /consul-conf
ADD . .
RUN cd /consul-conf && \
    make -f Makefile.docker
ADD https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini /consul-conf/bin/tini
RUN chmod +x /consul-conf/bin/*

###

FROM debian:stretch-slim
ARG VERSION=dev
ARG BUILD_ID=dev
ENV CONSUL_CONF_VERSION=${VERSION} \
    CONSUL_CONF_BUILD=${BUILD_ID} \
    PHP_PHALCON_VERSION=3.3 \
    NGINX_VERSION=1.10

RUN apt-get update && \
    apt-get install -y curl && \
    curl -s "https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh" | bash && \
    apt-get install -y \
        jq netcat \
        nginx=${NGINX_VERSION}* \
        php7.0-fpm php7.0-json php7.0-mbstring \
        php7.0-phalcon=${PHP_PHALCON_VERSION}* && \
    rm -rf /var/lib/apt/lists/* && \
    ln -fs /consul-conf/bin/cmd-kv-set.sh /bin/kv-set && \
    ln -fs /consul-conf/bin/cmd-kv-ping.sh /bin/kv-ping
EXPOSE 80/tcp 443/tcp
HEALTHCHECK --interval=1m --timeout=10s \
    CMD /consul-conf/bin/docker-health-check.sh || exit 1
ENTRYPOINT ["/consul-conf/bin/tini", "--", "/consul-conf/bin/docker-entrypoint.sh"]
CMD ["consul-conf"]
VOLUME ["/etc/consul-conf"]
WORKDIR /consul-conf
COPY --from=build /consul-conf /consul-conf