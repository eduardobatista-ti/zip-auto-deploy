#!/bin/bash

# Obter o diretório raiz do script atual
ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Carregar as configurações do arquivo PHP na pasta raiz
source <(php -r "include '$ROOT_DIR/deploy-config.php'; foreach (\$config as \$key => \$value) { echo \"\$key=\\\"\$value\\\"\n\"; }")

#Lockfile
LOCKFILE="$target_dir/deploy.lock"

# Diretório de destino
TARGET_DIR="$target_dir"

# URL do repositório Git
GIT_REPO="$repo_url"

# Diretório temporário para clonar o repositório
TEMP_DIR="$temp_dir"

# Lista de exceções - arquivos e diretórios que não devem ser removidos ou sobrepostos
EXCEPTION_ITEMS=(
    "logs-zip"
    "source-zip" 
    "deploy-config.php"
    ".env"
)

# Função para checar se um item está na lista de exceções
is_exception() {
    local item=$1
    for exception in "${EXCEPTION_ITEMS[@]}"; do
        if [[ "$item" == "$exception" || "$item" == "$exception/"* ]]; then
            return 0
        fi
    done
    return 1
}

# Verifica se o diretório de destino existe e remove seu conteúdo, exceto os itens na lista de exceções
if [ -d "$TARGET_DIR" ]; then
    echo "Limpando o diretório de destino: $TARGET_DIR"
    for item in "$TARGET_DIR"/*; do
        item_name=$(basename "$item")
        if ! is_exception "$item_name"; then
            echo "Removendo $item_name"
            rm -rf "$item"
        else
            echo "Preservando $item_name"
        fi
    done
fi

# Clona o repositório em um diretório temporário
echo "Clonando o repositório: $GIT_REPO"
git clone "$GIT_REPO" "$TEMP_DIR"

# Move o conteúdo clonado para o diretório de destino, exceto os itens na lista de exceções
echo "Movendo conteúdo clonado para: $TARGET_DIR"
for item in "$TEMP_DIR"/*; do
    item_name=$(basename "$item")
    if ! is_exception "$item_name"; then
        echo "Movendo $item_name"
        mv "$item" "$TARGET_DIR/"
    else
        echo "Preservando $item_name da pasta temporária"
        rm -rf "$item" # Removendo o item clonado que está na lista de exceções
    fi
done

# Limpeza do diretório temporário
rm -rf "$TEMP_DIR"

#removendo lockfile

rm -rf $LOCKFILE
echo "Lockfile removido com sucesso!"

echo "Deploy concluído com sucesso!"
