<?php
// Configurações
$repoUrl = 'https://github.com/eduardobatista-ti/cdn-zipcloud.git';
$deployDir = '/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/ziper';
$tempDir = '/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/temp';
$logDir = '/home/zipcloudbr/web/cdn.zipcloud.com.br/logs';
$logFile = $logDir . '/deploy.log';
$secret = 'seu-token-secreto'; // Defina um token secreto forte

// Função para logar mensagens
function logMessage($message) {
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Função para executar comandos e lidar com erros
function execCommand($command) {
    global $logFile;
    $output = [];
    $returnVar = 0;
    exec($command, $output, $returnVar);
    logMessage("Executando comando: $command");
    if ($returnVar !== 0) {
        logMessage("Erro ao executar: $command");
        foreach ($output as $line) {
            logMessage($line);
        }
        exit($returnVar);
    }
    foreach ($output as $line) {
        logMessage($line);
    }
}

// Verificar se o script foi chamado pelo GitHub com o token correto
$payload = file_get_contents('php://input');
$signature = 'sha1=' . hash_hmac('sha1', $payload, $secret);
if (!hash_equals($signature, $_SERVER['HTTP_X_HUB_SIGNATURE'])) {
    http_response_code(403);
    logMessage('Forbidden: token inválido.');
    exit('Forbidden');
}

logMessage('Iniciando o desplante');

// Remover o diretório ziper antigo
execCommand("rm -rf $deployDir");

// Clonar o repositório no diretório temporário
execCommand("git clone $repoUrl $tempDir");

// Renomear o diretório clonado para ziper
execCommand("mv $tempDir $deployDir");

// Limpeza: Remover o diretório temporário se ele ainda existir
if (is_dir($tempDir)) {
    execCommand("rm -rf $tempDir");
}

logMessage('Deploy concluído com sucesso!');
echo "Desploy concluído com sucesso!";
?>
