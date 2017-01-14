#!/bin/bash
##
# Install MiCloud on macOS.
#
# Create and start docker-machine on boot and start the services in docker-compose.yml.
# Assumes docker-machine and docker-compose are installed and using Virtualbox (i.e. docker toolbox).
##

# Vars.
VM=MiCloud
HOST_INTERFACE="${1:-"en0"}"

# Check if the VM exists (before we set -e
VBoxManage list vms | grep \""${VM}"\" &> /dev/null
VM_EXISTS_CODE=$?

# Assume this script is one dir below.
BASEDIR=$(cd "$(dirname "$0")/.."; pwd -P)

# Failure is not an option.
set -e

# Create the vm if it doesn't exits (dumbed down from docker quickstart).
if [ $VM_EXISTS_CODE -eq 1 ]; then
  docker-machine create -d virtualbox --virtualbox-memory 2048 --virtualbox-disk-size 204800 "${VM}"
fi

# Install start script.
ln -s "$BASEDIR"/start.sh /usr/local/bin/start-micloud

# Start docker-machine on boot.
cp "$BASEDIR"/macOS/com.sut3kh.micloud.plist ~/Library/LaunchAgents/
launchctl load ~/Library/LaunchAgents/com.sut3kh.micloud.plist

# Set up networking
echo "Enabling bridged network interface"
docker-machine stop "$VM"
VBoxManage modifyvm "$VM" --nic3 bridged --bridgeadapter3 "$HOST_INTERFACE" --nictype3 82540EM --cableconnected3 on
"$BASEDIR"/start.sh

# Start and Init docker-compose services
cd "$BASEDIR"
eval $(docker-machine env "$VM")
docker-compose up -d --build
