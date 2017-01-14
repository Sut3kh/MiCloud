## Install

**IMPORTANT:** Due to limitations with docker-machine, must be installed on root hard drive.

- Install docker-compose, docker-machine, docker & virtualbox (or docker toolbox)
- Create docker-machine default (or run docker quickstart)
- Run `./macOS/install.sh`
  - To use a host network adapter other than eth0: `./macOS/install.sh eth1`

## Test

Get the IP of the bridged network adapter:

```
ip=$(
  docker-machine ssh MiCloud \
    ip -4 addr show dev eth2 scope global | sed 's#/.*##' |
      grep -o '[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}\.[0-9]\{1,3\}'\
)
```

### Test DNS

`dig @$ip google.com`

## TODO

- Pi-hole Auto update (https://github.com/diginc/docker-pi-hole#running-pi-hole-docker)
- Add micloud & pihole command wrappers to host $PATH
- DHCP
- Make IP configurable
