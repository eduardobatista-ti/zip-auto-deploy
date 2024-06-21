#!/bin/bash

tempDir="$1"
lockFile="$2"

if [ -d "$tempDir" ]; then
    rm -rf "$tempDir"
fi

rm -f "$lockFile"
