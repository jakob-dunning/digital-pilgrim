run:
	php -f src/Cli/worker.php
	
test:
	phpunit tests --coverage-html tmp/