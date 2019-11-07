|**Service**|**Master**|**Develop**|
|---|---|---|
|CircleCI|[![CircleCI](https://circleci.com/gh/INGV/hyp2000-ws/tree/master.svg?style=svg)](https://circleci.com/gh/INGV/hyp2000-ws/tree/master)|[![CircleCI](https://circleci.com/gh/INGV/hyp2000-ws/tree/develop.svg?style=svg)](https://circleci.com/gh/INGV/hyp2000-ws/tree/develop)|
|Version|[![Version](https://img.shields.io/badge/dynamic/yaml?label=ver&query=softwareVersion&url=https://raw.githubusercontent.com/INGV/hyp2000-ws/master/publiccode.yml)](https://github.com/INGV/hyp2000-ws/blob/master/HISTORY)|[![Version](https://img.shields.io/badge/dynamic/yaml?label=ver&query=softwareVersion&url=https://raw.githubusercontent.com/INGV/hyp2000-ws/develop/publiccode.yml)](https://github.com/INGV/hyp2000-ws/blob/develop/HISTORY)|

[![License](https://img.shields.io/github/license/INGV/hyp2000-ws.svg)](https://github.com/INGV/hyp2000-ws/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/INGV/hyp2000-ws.svg)](https://github.com/INGV/hyp2000-ws/issues)
[![Join the #general channel](https://img.shields.io/badge/Slack%20channel-%23general-blue.svg)](https://ingv-institute.slack.com/messages/CKS902Y5B)
[![Get invited](https://slack.developers.italia.it/badge.svg)](https://ingv-institute.slack.com/)

# dante

```
$ git clone https://gitlab.rm.ingv.it/caravel/dante6beta dante
$ cd dante
```

## Configure
Copy docker environment file:
```
$ cp ./Docker/env-example ./Docker/.env
```

Copy laravel environment file:
```
$ cp ./.env.example ./.env
```

Set `NGINX_HOST_HTTP_PORT` in `./Docker/.env` file.

### !!! On Linux machine and no 'root' user !!!
To run container as *linux-user* (intead of `root`), set `WORKSPACE_PUID` and `WORKSPACE_PGID` in `./Docker/.env` file with:
- `WORKSPACE_PUID` should be equal to the output of `id -u` command
- `WORKSPACE_PGID` should be equal to the output of `id -g` command

## Start dante
First, build docker images:

```
$ cd Docker
$ COMPOSE_HTTP_TIMEOUT=200 docker-compose up -d nginx redis workspace docker-in-docker
$ cd ..
```

## Configure Laravel
### !!! On Linux machine and no 'root' user !!!
```
$ cd Docker
$ docker-compose exec -T --user=laradock workspace composer install
$ docker-compose exec -T --user=laradock workspace php artisan key:generate
$ cd ..
```

### !!! Others !!!
```
$ cd Docker
$ docker-compose exec -T workspace composer install
$ docker-compose exec -T workspace php artisan key:generate
$ cd ..
```

## How to use it
When all containers are started, connect to: 
- http://<your_host>:<your_port>/

If all works, you should see a web page with OpenAPI3 specification to interact with WS.

## Test
```
$ cd Docker
$ docker-compose exec -T --user=laradock workspace bash -c "vendor/bin/phpunit -v"
```

## Thanks to
This project uses the [Laradock](https://github.com/laradock/laradock) idea to start docker containers

## Contribute
Please, feel free to contribute.

## Author
(c) 2019 Valentino Lauciani valentino.lauciani[at]ingv.it \
(c) 2019 Matteo Quintiliani matteo.quintiliani[at]ingv.it

Istituto Nazionale di Geofisica e Vulcanologia, Italia
