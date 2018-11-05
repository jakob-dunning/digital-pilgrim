run:
	php -f cli/worker.php
	
test:
	vendor/phpunit/phpunit/phpunit --coverage-html tmp/coverage/