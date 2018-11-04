#!/bin/bash

if ! [[ ${PWD} =~ .*\/digitalpilgrim$ ]]
	then
		echo 'Script should be run from within the digital pilgrim folder'
		exit 1
fi

if [ $# -eq 0 ]
	then
		echo 'Missing argument: Url'
fi

echo 'Resetting fileStorage ...'
URL=$1
printf $URL > fileStorage/currentDomain
printf '[]' > fileStorage/destinations
printf '[]' > fileStorage/domainHistory
printf '[]' > fileStorage/scraperHistory
printf [\"$URL\"] | sed 's/\//\\\//g' > fileStorage/scraperQueue