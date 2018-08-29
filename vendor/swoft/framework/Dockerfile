FROM swoft/swoft

MAINTAINER huangzhhui <huangzhwork@gmail.com>

ADD . /var/www/swoft

WORKDIR /var/www/swoft
RUN composer install \
    && composer dump-autoload -o \
    && composer clearcache

EXPOSE 80

CMD ["/bin/bash"]
