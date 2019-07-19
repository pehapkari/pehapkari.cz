#!/usr/bin/env bash

eval $(ssh-agent -s)
mkdir -p ~/.ssh
ssh-keyscan -H pehapkari.cz >> ~/.ssh/known_hosts
echo "$DEPLOY_PRIVATE_KEY" | ssh-add - > /dev/null
ssh root@pehapkari.cz "cd /projects/pehapkari.cz && ./run.sh"
