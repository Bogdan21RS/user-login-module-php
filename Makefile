.PHONY : main build-image build-container start test shell stop clean
main: build-image build-container

build-image:
	docker build -t user-login-module-php .

build-container:
	docker run -dt --name user-login-module-php -v .:/user-login-module-php user-login-module-php
	docker exec user-login-module-php composer update
	docker exec user-login-module-php composer install

start:
	docker start user-login-module-php

test: start
	docker exec user-login-module-php ./vendor/bin/phpunit tests/$(target)

shell: start
	docker exec -it user-login-module-php /bin/bash

stop:
	docker stop user-login-module-php

clean: stop
	docker rm user-login-module-php
	rm -rf vendor
