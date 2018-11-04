run:
	php -f src/Cli/worker.php
	
test:
	vendor/phpunit/phpunit/phpunit --coverage-html tmp/coverage/