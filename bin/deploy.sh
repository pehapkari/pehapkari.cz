#!/usr/bin/env bash

echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USERNAME" --password-stdin
docker pull pehapkari/pehapkari.cz:$TRAVIS_COMMIT
docker tag pehapkari/pehapkari.cz:$TRAVIS_COMMIT pehapkari/pehapkari.cz:latest
docker push pehapkari/pehapkari.cz

eval $(ssh-agent -s)
mkdir -p ~/.ssh
ssh-keyscan -H pehapkari.cz >> ~/.ssh/known_hosts
echo "$DEPLOY_PRIVATE_KEY" | ssh-add - > /dev/null
ssh root@pehapkari.cz "cd /projects/pehapkari.cz && ./run.sh"
