#!/bin/bash

if [ $# -eq 0 ]
	then
		echo 'Missing argument: Url'
fi

URL=$1
printf $URL > fileStorage/currentDomain
printf '[]' > fileStorage/destinations
printf '[]' > fileStorage/domainHistory
printf '[]' > fileStorage/scraperHistory
printf [\"$URL\"] | sed 's/\//\\\//g' > fileStorage/scraperQueue