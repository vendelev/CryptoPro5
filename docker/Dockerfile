# syntax=docker/dockerfile:1.3-labs

ARG PHP_VERSION="8.4.11-cli-bookworm"
ARG LOCALTIME="Europe/Moscow"

FROM php:${PHP_VERSION} AS cprocsp

# Контекст выполнения идет от корня проекта, см compose.yaml

ADD docker/linux-amd64_deb /tmp/linux-amd64_deb
RUN <<EOF bash
    set -e
    apt update
    cd /tmp/linux-amd64_deb
    sh ./install.sh
EOF

ARG LICENSE
RUN /opt/cprocsp/sbin/amd64/cpconfig -license -set ${LICENSE}

# https://gist.github.com/Barolina/9c2a36e0e7de365974b168f40e6540a3
# https://wiki.astralinux.ru/pages/viewpage.action?pageId=32833902
COPY --chmod=600 docker/keys /var/opt/cprocsp/keys/root/
RUN /opt/cprocsp/bin/amd64/csptestf -absorb -certs -autoprov


###
FROM php:${PHP_VERSION} AS php

ARG LOCALTIME

RUN <<EOF bash
    set -e
    apt-get update
    apt-get install -y tzdata ntp ntpdate
    rm /etc/localtime
    ln -s /usr/share/zoneinfo/${LOCALTIME} /etc/localtime
    echo ${LOCALTIME} > /etc/timezone
    apt-get remove -q -y ${PHPIZE_DEPS} ${BUILD_DEPENDS}
    apt-get clean autoclean
    apt-get autoremove --yes
    rm -rf /var/lib/{apt,dpkg,cache,log,lists}/*
    rm -rf /var/cache/apt/archives /tmp/* /var/tmp/*
EOF

COPY --from=cprocsp /opt/cprocsp /opt/cprocsp
COPY --from=cprocsp /etc/opt /etc/opt
COPY --from=cprocsp /var/opt /var/opt

RUN mv "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"
RUN echo "date.timezone = ${LOCALTIME}" >> "${PHP_INI_DIR}/php.ini"

WORKDIR /var/www/backend

EXPOSE 80

CMD [ "bash", "-c", "php -S 0.0.0.0:80 /var/www/backend/src/index.php" ]
