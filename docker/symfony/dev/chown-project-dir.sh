#!/bin/sh

DEV_UID=$(id -u)
DEV_GID=$(id -g)

su-exec root addgroup -g ${DEV_GID} dev
su-exec root adduser -u ${DEV_UID} -D -G dev dev
su-exec root chown -R dev:dev /home/dev /opt

export HOME=/home/dev

echo "alias sudo='su-exec root'" >> ~/.bashrc
echo 'PS1="\`pwd\` $ "' >> ~/.bashrc

su-exec root [ ! -d /home/dev/yarn ] && su-exec root mkdir /home/dev/yarn
su-exec root chmod -R 775 /home/dev/yarn && su-exec root chown -R dev:dev /home/dev/yarn
