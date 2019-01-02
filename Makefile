run:
	php -f ./cli/worker.php
	
monitor:
	@echo "If you have enabled the monitor in the config, you can now view it by opening the index.html page in the web folder locally.\n"
	php -f ./cli/websocketServer.php
	
test:
	./vendor/phpunit/phpunit/phpunit --coverage-html tmp/coverage/
	
phpcs:
	./vendor/bin/phpcs
	
phpcbf:
	./vendor/bin/phpcbf