#!/usr/bin/env bash

cd "$(dirname "$0")"

DOCKER_BUILDKIT=1 docker build --file docker/release.Dockerfile --output . .
