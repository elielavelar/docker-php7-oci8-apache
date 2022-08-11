FROM php:7.4.30-apache

LABEL MAINTAINER="Eliel Avelar <elielavelar@gmail.com>"

# installing required stuff
#RUN add-apt-repository ppa:ondrej/php -y \
#    && apt-get update \
#    && apt-get install -y unzip libaio-dev libmcrypt-dev git php7.4-dev --allow-unauthenticated \
#    && apt-get clean -y
RUN apt-get update \
    && apt-get install -y unzip libaio-dev libmcrypt-dev \ 
        libicu-dev libzip-dev zip \
    && apt-get clean -y

# PHP extensions
RUN \
    docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl  \
    #&& docker-php-ext-install gd2 \ 
    && docker-php-ext-install sockets \
    && docker-php-ext-install zip
    # && docker-php-ext-install mcrypt

# xdebug, if you want to debug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# PHP composer
RUN curl -sS https://getcomposer.org/installer | php --  --install-dir=/usr/bin --filename=composer

# apache configurations, mod rewrite
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

# Oracle instantclient

# copy oracle files
# ADD oracle/instantclient-basic-linux.x64-12.1.0.2.0.zip /tmp/
ADD https://download.oracle.com/otn_software/linux/instantclient/211000/instantclient-basic-linux.x64-21.1.0.0.0.zip /tmp/
# ADD oracle/instantclient-sdk-linux.x64-12.1.0.2.0.zip /tmp/
ADD https://download.oracle.com/otn_software/linux/instantclient/211000/instantclient-sdk-linux.x64-21.1.0.0.0.zip /tmp/
# ADD oracle/instantclient-sqlplus-linux.x64-12.1.0.2.0.zip /tmp/
ADD https://download.oracle.com/otn_software/linux/instantclient/211000/instantclient-sqlplus-linux.x64-21.1.0.0.0.zip /tmp/

# unzip them
RUN unzip /tmp/instantclient-basic-linux.x64-*.zip -d /usr/local/ \
    && unzip /tmp/instantclient-sdk-linux.x64-*.zip -d /usr/local/ \
    && unzip /tmp/instantclient-sqlplus-linux.x64-*.zip -d /usr/local/

# install oci8
RUN ln -s /usr/local/instantclient_*_1 /usr/local/instantclient \
    && ln -s /usr/local/instantclient/sqlplus /usr/bin/sqlplus 

RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/usr/local/instantclient \
    && docker-php-ext-install oci8 \
    && echo /usr/local/instantclient/ > /etc/ld.so.conf.d/oracle-insantclient.conf \
    && ldconfig

# Install Oracle extensions
RUN docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/usr/local/instantclient \
       && docker-php-ext-install pdo_oci \
       && docker-php-ext-enable oci8	

#RUN apt-get -y install unixodbc-dev
#RUN pecl install sqlsrv pdo_sqlsrv
#RUN printf "; priority=20\nextension=sqlsrv.so\n" > /etc/php/7.4/mods-available/sqlsrv.ini
#RUN printf "; priority=30\nextension=pdo_sqlsrv.so\n" > /etc/php/7.4/mods-available/pdo_sqlsrv.ini
#RUN phpenmod -v 7.4 sqlsrv pdo_sqlsrv
RUN service apache2 restart