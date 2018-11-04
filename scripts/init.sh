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
printf $URL > fileStorage/currentDomain
printf '[]' > fileStorage/destinations
printf '[]' > fileStorage/domainHistory
printf '[]' > fileStorage/scraperHistory
printf [\"$URL\"] | sed 's/\//\\\//g' > fileStorage/scraperQueue

echo 'Creating error log ...'
mkdir tmp
touch tmp/error.log

exit 0