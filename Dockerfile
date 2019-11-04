# Dockerfile extending the generic PHP image with application files for a single application.
FROM gcr.io/google-appengine/php72:latest

# Allow customizing some composer flags
ARG COMPOSER_FLAGS='--no-scripts --no-dev --prefer-dist'
ENV COMPOSER_FLAGS=${COMPOSER_FLAGS}

# Install nano
RUN apt-get -y update && ACCEPT_EULA=Y apt-get install -y --allow-unauthenticated --no-install-recommends nano

# Copy the app, change the owner, execute composer, move supervisord.conf
COPY . $APP_DIR
RUN chown -R www-data.www-data $APP_DIR && \
    /build-scripts/composer.sh && \
    mv $APP_DIR/supervisord.conf /etc/supervisor/supervisord.conf

# php config
RUN  mv $APP_DIR/php.ini /opt/php72/lib/conf.d/php.ini

ENTRYPOINT ["/build-scripts/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]

# Cleanup
RUN rm composer.json composer.lock php.ini source-context.json; exit 0