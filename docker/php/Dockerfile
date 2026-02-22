ARG PHP_VERSION

FROM php:${PHP_VERSION}-cli-bookworm

ARG UID
ARG GID

# Install general packages
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    nano \
    sudo \
    ripgrep \
    && rm -rf /var/lib/apt/lists/*

# Install php extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug intl zip

# Setup docker user to prevent right and access conflicts
RUN set -eux; \
    if ! getent group ${GID} >/dev/null; then groupadd -g ${GID} dockergroup; fi; \
    useradd -l -u ${UID} -g ${GID} -m docker \
    && usermod -aG sudo docker \
    && echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

# Prevent git dubious ownership
RUN git config --global --add safe.directory /app

USER docker
