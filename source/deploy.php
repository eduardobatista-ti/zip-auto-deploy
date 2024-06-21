<?php
// Configurações
$repoUrl = 'https://github.com/usuario/repositorio.git';
$deployDir = '/var/www/html/aplicativo';
$tempDir = '/var/www/html/temp';

function execCommand($command) {
    $output = [];
    $returnVar = 0;
    exec($command, $output, $returnVar);
    if ($returnVar !== 0) {
        echo "Erro ao executar: $command\n";
        foreach ($output as $line) {
            echo $line . "\n";
        }
        exit($returnVar);
    }
}

// Remover diretório antigo
execCommand("rm -rf $deployDir");

// Clonar o repositório no diretório temporário
execCommand("git clone $repoUrl $tempDir");

// Renomear o diretório clonado
execCommand("mv $tempDir $deployDir");

echo "Desplante concluído com sucesso!";
?>
