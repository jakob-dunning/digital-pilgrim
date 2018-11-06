run:
	php -f cli/worker.php
	
test:
	vendor/phpunit/phpunit/phpunit --coverage-html tmp/coverage/
	
monitor:
	cd web
	php -S localhost:8000
	cd ..