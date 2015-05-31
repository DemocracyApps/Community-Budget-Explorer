#!/bin/bash
echo "Reinstating file from origin/master"

git fetch
git checkout origin/master $1

