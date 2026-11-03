FROM php:8.4-cli

ARG UID=1000
ARG GID=1000

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/* \
    && install-php-extensions intl zip pcntl bcmath pdo_mysql pdo_pgsql mongodb

RUN groupadd --gid "${GID}" app \
    && useradd --uid "${UID}" --gid "${GID}" --create-home --shell /bin/bash app \
    && mkdir -p /app /home/app/.composer \
    && chown app:app /app /home/app/.composer

ENV COMPOSER_HOME=/home/app/.composer

USER app
WORKDIR /app

CMD ["composer", "serve"]
