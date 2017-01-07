## Install

- Install docker-compose, docker-machine, docker & virtualbox (or docker toolbox)
- Create docker-machine default (or run docker quickstart)
- run ./install.sh

## Test

Get the IP of the bridged network adapter:

```
ip=$(
  docker-machine ssh default \
    ip -4 addr show dev eth2 scope global | sed 's#/.*##' |
      grep -o '[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}'\
)
```

### Test DNS

`dig @$ip google.com`

## TODO

- Fixed IP for docker machine
- Pi-hole
- DHCP
- maybe chroot bind (yum install bind-chroot)

