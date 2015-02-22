#!/usr/bin/env bash

# you need to make sure that the app is present in the /Applications folder, spaces need to be escaped in app names
readonly EDITOR='Sublime Text 2.app'
readonly TERMINAL_APP='iTerm.app'
readonly FTP_CLIENT='FileZilla.app'
readonly SQL_APP='Sequel Pro.app'

echo 'Use devstart and devstop on command line to start and stop your development tools :)'

function devtoolsopen() {

	# check if editor needs to be installed and launched
	if [[ ! -z $EDITOR ]]; then
		echo -n 'Opening editor: '
		if [[ -d "/Applications/$EDITOR" ]]; then
			open "/Applications/$EDITOR"
		else
			echo "EDITOR '$EDITOR' NOT FOUND..."
		fi
	fi

	# check if terminal app needs to be installed and launched
	if [[ ! -z $TERMINAL_APP ]]; then
		echo -n 'Opening TERMINAL_APP: '
		if [[ -d "/Applications/$TERMINAL_APP" ]]; then
			open "/Applications/$TERMINAL_APP"
		else
			echo "TERMINAL_APP '$TERMINAL_APP' NOT FOUND..."
		fi
	fi

	# check if filezilla needs to be installed and launched
	if [[ ! -z $FTP_CLIENT ]]; then
		echo -n 'Opening FTP_CLIENT: '
		if [[ -d "/Applications/$FTP_CLIENT" ]]; then
			open "/Applications/$FTP_CLIENT"
		else
			echo "FTP_CLIENT '$FTP_CLIENT' NOT FOUND..."
		fi
	fi

	# check if sql app needs to be installed and launched
	if [[ ! -z $SQL_APP ]]; then
		echo -n 'Opening SQL_APP: '
		if [[ -d "/Applications/$SQL_APP" ]]; then
			open "/Applications/$SQL_APP"
		else
			echo "SQL_APP '$SQL_APP' NOT FOUND..."
		fi
	fi

}

function devtoolsclose() {
	echo 'Closing dev tools...'
	osascript -e "quit app '$EDITOR'"
	osascript -e "quit app '$TERMINAL_APP'"
	osascript -e "quit app '$FTP_CLIENT'"
	osascript -e "quit app '$SQL_APP'"
}

# aliases for dev env
echo "alias devstart='devtoolsopen'" >> ~/.bash_profile
echo "alias devstop='devtoolsclose'" >> ~/.bash_profile
source ~/.bash_profile
