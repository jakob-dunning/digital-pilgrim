run:
	php -f cli/worker.php
	
test:
	vendor/phpunit/phpunit/phpunit --coverage-html tmp/coverage/

webpack:
	node node_modules/webpack-cli/bin/cli.js
	
monitor:
	@echo "If you have enabled the monitor in the config, you can now view it by opening the index.html page in the web folder.\n"
	php -f cli/websocketServer.php
	
init:
	yarn install
	node node_modules/webpack-cli/bin/cli.js