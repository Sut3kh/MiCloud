#!/bin/bash
##
# Start the MiCloud VM with a static IP and start services.
#
# TODO: might be better to use a custom build of boot2docker instead
#       as per https://github.com/boot2docker/boot2docker/issues/129
##

# Vars.
VM="MiCloud"
IP="192.168.1.253"
SUBNET="192.168.1.0/24"
DEFAULT="192.168.1.254"

# Check if the VM is running.
VM_STATUS="$(docker-machine status ${VM} 2>&1)"

# Failure is not an option.
set -e

# Start the VM if needed.
if [ "${VM_STATUS}" != "Running" ]; then
  docker-machine start "${VM}"
  yes | docker-machine regenerate-certs "${VM}"
fi

# Set static IP on bridged network interface (should be eth2)
echo "configuring network"
docker-machine ssh "$VM" sudo ip link set eth2 up
docker-machine ssh "$VM" sudo ip addr add "$IP" dev eth2
docker-machine ssh "$VM" sudo ip route add "$SUBNET" dev eth2
# docker-machine ssh "$VM" sudo ip route add default via "$DEFAULT"
echo "MiCloud server setup on $IP"
