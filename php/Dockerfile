FROM php:7.3-apache

RUN docker-php-ext-install mysqli

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN apt-get update
RUN apt-get -y install python3
RUN apt-get -y install python3-pip
RUN apt-get -y install git
RUN cd /opt
RUN git clone https://github.com/os2kitos/kitos_tools.git /opt/kitos_tools
RUN pip3 install -r /opt/kitos_tools/requirements.txt
RUN pip3 install -r /opt/kitos_tools/exporters/requirements.txt
RUN pip3 install /opt/kitos_tools/kitos_tools


ADD ./python/settings.json /opt/kitos_tools/settings/
ADD ./python/import_to_mysql.sh /opt/kitos_tools
RUN chmod a+x /opt/kitos_tools/import_to_mysql.sh
COPY ./src/ /var/www/html
