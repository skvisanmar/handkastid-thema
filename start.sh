#!/bin/sh
# Ræsir staðbundna WordPress-prufusíðu með Handkastið-þemanu.
# Opnaðu http://127.0.0.1:9400 þegar „Ready“ birtist. Stöðva með Ctrl+C.
# Notandi: admin / password
cd "$(dirname "$0")"
exec npx -y @wp-playground/cli@3.1.52 server \
	--port=9400 \
	--mount="$PWD/theme/handkastid:/wordpress/wp-content/themes/handkastid" \
	--mount="$PWD/local:/seed" \
	--blueprint="$PWD/local/blueprint.json" \
	--login
