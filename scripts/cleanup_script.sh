#!/bin/bash
if [ -d "$tempDir" ]; then
    rm -rf "$tempDir"
fi
rm -f "$lockFile"
