FROM php:7.4-apache
ENV TimeZone=Asia/Shanghai
ARG user_id=1000
RUN usermod -u $user_id www-data; \
    ln -snf /usr/share/zoneinfo/$TimeZone /etc/localtime && echo $TimeZone > /etc/timezone; \
    chown -R www-data:www-data /var/www; \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY flag /flag
COPY www /var/www/html
RUN chmod 744 /flag; \
    chmod -R 755 /var/www/html/
CMD service apache2 restart && tail -F /var/log/apache2/access.log;