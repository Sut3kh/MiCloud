#!/bin/bash
##
# Install MiCloud on macOS.
#
# Start default docker-machine on boot and start the services in docker-compose.yml.
# Assumes docker-machine and docker-compose are installed and using Virtualbox (i.e. docker toolbox).
# The default docker-machine must have been created (i.e. docker quick start terminal ran once).
##

# Failure is not an option.
set -e

# Assume this script is one dir below.
BASEDIR=$(cd "$(dirname "$0")/.."; pwd -P)

# Start default docker-machine on boot.
cp "$BASEDIR"/macOS/com.docker.machine.default.plist ~/Library/LaunchAgents/
launchctl load ~/Library/LaunchAgents/com.docker.machine.default.plist

# Set up networking
docker-machine stop
echo "Enabling bridged network interface"
VBoxManage modifyvm default --nic3 bridged --bridgeadapter3 en0 --nictype3 82540EM --cableconnected3 on
docker-machine start

# Start and Init docker-compose services
cd "$BASEDIR"
eval $(docker-machine env)
docker-compose up -d --build

