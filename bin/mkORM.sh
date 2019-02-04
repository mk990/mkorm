#!/usr/bin/env bash

DIR=
export MKORMPATH="$DIR/"
devtools=${MKORMPATH%/}
php "$devtools/mkORM" $*
