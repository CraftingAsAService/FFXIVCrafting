FROM debian:stable-slim
MAINTAINER Shawn Warren <shawn.warren@rackspace.com>

EXPOSE 80

RUN apt-get -qq update
RUN apt-get -qq install locales

RUN sed -e '/en_US.UTF-8/s/^# \+//' -i /etc/locale.gen && locale-gen
ENV LANG en_US.UTF-8

RUN apt-get -qq install apache2
RUN a2enmod rewrite

RUN apt-get -qq install wget
RUN mkdir -p /opt/newrelic
WORKDIR /opt/newrelic
RUN wget -q -r -nd -np -A linux.tar.gz http://download.newrelic.com/php_agent/release/
RUN tar -zxf newrelic-php5-*-linux.tar.gz --strip=1
ENV NR_INSTALL_SILENT true
RUN bash newrelic-install install
WORKDIR /
ADD config/newrelic.ini /etc/php5/apache2/conf.d/newrelic.ini
RUN apt-get -qq install php php-curl php-mbstring php-mcrypt php-mysql php-sqlite3 php-xml
RUN /usr/sbin/phpenmod curl
RUN /usr/sbin/phpenmod mbstring
RUN /usr/sbin/phpenmod mcrypt
RUN sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php/7.0/apache2/php.ini

ADD config/000-default.conf /etc/apache2/sites-available/000-default.conf
ADD . /var/www/

RUN apt-get -qq install curl unzip
RUN curl -sS https://getcomposer.org/installer | php
WORKDIR /var/www/
RUN /composer.phar install
RUN chown -R www-data.www-data /var/www/

ENTRYPOINT [ "/usr/sbin/apache2ctl", "-D", "FOREGROUND" ]
CMD [ "-k", "start" ]
