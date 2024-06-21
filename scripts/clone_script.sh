#!/bin/bash

repoUrl="$1"
tempDir="$2"

git clone "$repoUrl" "$tempDir"
