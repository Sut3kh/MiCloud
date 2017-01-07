#!/bin/bash
##
# Start named service in foreground.
#
# Based on /usr/lib/systemd/system/named.service
##

# Failure is not an option.
set -e

# Setup env.
source /etc/sysconfig/named
export KRB5_KTNAME=/etc/named.keytab

# Check zone file config first
/usr/sbin/named-checkconf -z /etc/named.conf

# Run in foreground
/usr/sbin/named -g -u named $OPTIONS

