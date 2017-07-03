## Install

- Install docker-compose, run `docker-compose up -d`

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
- Make IP configurable
