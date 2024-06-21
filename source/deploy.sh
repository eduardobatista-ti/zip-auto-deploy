#!/bin/bash

# Diretório de destino
TARGET_DIR="/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/ziper"

# URL do repositório Git
GIT_REPO="https://github.com/eduardobatista-ti/cdn-zipcloud.git"

# Diretório temporário para clonar o repositório
TEMP_DIR="/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/temp_clone"

LOCKFILE="/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/ziper/file.lock"

# Verificação de lockfile
if [ -f "$LOCKFILE" ]; then
    echo "Processo já em execução. Saindo."
    exit 1
fi

# Cria o arquivo de lock
touch $LOCKFILE

# Verifica se o diretório de destino existe e remove se existir
if [ -d "$TARGET_DIR" ]; then
    echo "Removendo diretório existente: $TARGET_DIR"
    rm -rf "$TARGET_DIR"
fi

# Clona o repositório em um diretório temporário
echo "Clonando o repositório: $GIT_REPO"
git clone "$GIT_REPO" "$TEMP_DIR"

# Renomeia o diretório clonado para o nome desejado
echo "Renomeando diretório temporário para: $TARGET_DIR"
mv "$TEMP_DIR" "$TARGET_DIR"

# Após terminar, remova o arquivo de lock
rm "$LOCKFILE"

echo "Deploy concluído com sucesso!"
