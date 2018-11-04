#!/bin/bash

if ! [[ ${PWD} =~ .*\/digitalpilgrim$ ]]
	then
		echo 'Script should be run from within the digital pilgrim folder'
		exit 1
fi

if [ $# -eq 0 ]
	then
		echo 'Missing argument: Start url'
		exit 1
fi

URL=$1

echo 'Creating fileStorage ...'
mkdir fileStorage
printf \"$URL\" | sed 's/\//\\\//g' > fileStorage/currentDomain.json
printf '[]' > fileStorage/destinations.json
printf '[]' > fileStorage/domainHistory.json
printf '[]' > fileStorage/scraperHistory.json
printf [\"$URL\"] | sed 's/\//\\\//g' > fileStorage/scraperQueue.json

echo 'Creating error log ...'
mkdir tmp
touch tmp/error.log

exit 0