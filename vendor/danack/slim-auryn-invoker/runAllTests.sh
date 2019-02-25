#!/usr/bin/env bash

set -e

sh runCodeSniffer.sh
sh runPhpStan.sh
sh runPhpUnit.sh


