#!/bin/bash
backupDir="$deployDir\_backup_$(date +'%Y%m%d_%H%M%S')"
if [ -d "$deployDir" ]; then
    mv "$deployDir" "$backupDir"
fi
