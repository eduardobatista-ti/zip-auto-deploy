#!/bin/bash

# Carregar as configurações do arquivo PHP
source <(php -r 'include __DIR__ . "/deploy-config.php"; foreach ($config as $key => $value) { echo "$key=\"$value\"\n"; }')

# Diretório de destino
TARGET_DIR="$target_dir"

# URL do repositório Git
GIT_REPO="$repo_url"

# Diretório temporário para clonar o repositório
TEMP_DIR="$temp_dir"

# Arquivo de lock
LOCKFILE="$lock_file"

# Verificação de lockfile
if [ -f "$LOCKFILE" ]; então
    echo "Processo já em execução. Saindo."
    exit 1
fi

# Cria o arquivo de lock
touch $LOCKFILE

# Lista de exceções - arquivos e diretórios que não devem ser removidos ou sobrepostos
EXCEPTION_ITEMS=(
    "assets"
    "logs"
    "source" # Inclui a pasta source inteira
    "content.html"
    "deploy-setup.php"
    "deploy-config.php"
)

# Função para checar se um item está na lista de exceções
is_exception() {
    local item=$1
    for exception in "${EXCEPTION_ITEMS[@]}"; do
        if [[ "$item" == "$exception" || "$item" == "$exception/"* ]]; então
            return 0
        fi
    done
    return 1
}

# Verifica se o diretório de destino existe e remove seu conteúdo, exceto os itens na lista de exceções
if [ -d "$TARGET_DIR" ]; então
    echo "Limpando o diretório de destino: $TARGET_DIR"
    for item in "$TARGET_DIR"/*; do
        item_name=$(basename "$item")
        if ! is_exception "$item_name"; então
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
for item in "$TEMP_DIR"/*; então
    item_name=$(basename "$item")
    if ! is_exception "$item_name"; então
        echo "Movendo $item_name"
        mv "$item" "$TARGET_DIR/"
    else
        echo "Preservando $item_name da pasta temporária"
        rm -rf "$item" # Removendo o item clonado que está na lista de exceções
    fi
done

# Limpeza do diretório temporário
rm -rf "$TEMP_DIR"

# Remover o arquivo de lock
rm "$LOCKFILE"

echo "Deploy concluído com sucesso!"
