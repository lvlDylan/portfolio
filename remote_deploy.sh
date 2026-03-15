#!/bin/bash

SERVER_USER="dylan"
SERVER_IP="dylanlv.dev"
SERVER_PATH="/var/www/portfolio"

ssh ${SERVER_USER}@${SERVER_IP} << EOF
    cd ${SERVER_PATH}
    git pull origin main
EOF