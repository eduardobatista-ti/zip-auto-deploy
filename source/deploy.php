<?php
// Configurações
$repoUrl = 'https://github.com/eduardobatista-ti/cdn-zipcloud.git';
$deployDir = '/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/ziper';
$tempDir = '/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/temp';
$logFile = '/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/logs/deploy.log';
$secret = '1'; // Defina um token secreto forte

// Função para logs
function logMessage($message) {
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    $logMessage = "[$timestamp] $message\n";
    
    // Abre o arquivo de log para escrita (append)
    $logHandle = fopen($logFile, 'a');
    if ($logHandle) {
        fwrite($logHandle, $logMessage);
        fclose($logHandle);
    } else {
        error_log("Não foi possível abrir o arquivo de log para escrita: $logFile");
    }
}

// Função para executar comando shell script e lidar com erros
function execShellScript($scriptPath, $args) {
    $argStr = '';
    foreach ($args as $key => $value) {
        $argStr .= "$key=\"$value\" ";
    }
    $output = [];
    $returnVar = 0;
    exec("bash $scriptPath $argStr 2>&1", $output, $returnVar); // Captura STDERR também
    $output = implode("\n", $output);
    logMessage("Executando script shell $scriptPath com argumentos: $argStr");
    logMessage("Saída do script shell:\n$output");
    if ($returnVar !== 0) {
        logMessage("Erro ao executar script shell $scriptPath");
        logMessage("Código de retorno: $returnVar");
        return false; // Retorna false para indicar falha na execução do script
    }
    return true; // Retorna true para indicar sucesso na execução do script
}

// Verificar se o script foi chamado pelo GitHub com o token correto
$payload = file_get_contents('php://input');
$headerSignature = $_SERVER['HTTP_X_HUB_SIGNATURE'] ?? '';
$signature = 'sha1=' . hash_hmac('sha1', $payload, $secret);

if (!hash_equals($signature, $headerSignature)) {
    http_response_code(403);
    logMessage('Forbidden: token inválido.');
    exit('Forbidden');
}

logMessage('Iniciando o deploy');

// Verificar se outro deploy está em andamento
$lockFile = $deployDir . '/deploy.lock';
if (file_exists($lockFile)) {
    logMessage('Deploy já está em andamento.');
    exit('Deploy em andamento');
}

// Criar um arquivo de lock para evitar deploys simultâneos
file_put_contents($lockFile, '');

// Caminhos para os scripts shell separados
$backupScriptPath = __DIR__ . '/scripts/backup_script.sh';
$cloneScriptPath = __DIR__ . '/scripts/clone_script.sh';
$renameScriptPath = __DIR__ . '/scripts/rename_script.sh';
$cleanupScriptPath = __DIR__ . '/scripts/cleanup_script.sh';

// Executar os scripts shell na ordem desejada, passando as variáveis como argumentos
if (!execShellScript($backupScriptPath, [
    'deployDir' => $deployDir,
    'tempDir' => $tempDir,
    'logFile' => $logFile,
])) {
    logMessage("Erro durante o backup. Verifique os logs para mais detalhes.");
    http_response_code(500);
    exit('Erro durante o backup. Verifique os logs para mais detalhes.');
}

if (!execShellScript($cloneScriptPath, [
    'repoUrl' => $repoUrl,
    'tempDir' => $tempDir,
    'logFile' => $logFile,
])) {
    logMessage("Erro ao clonar o repositório Git. Verifique os logs para mais detalhes.");
    http_response_code(500);
    exit('Erro ao clonar o repositório Git. Verifique os logs para mais detalhes.');
}

if (!execShellScript($renameScriptPath, [
    'tempDir' => $tempDir,
    'deployDir' => $deployDir,
    'logFile' => $logFile,
])) {
    logMessage("Erro ao renomear o diretório clonado. Verifique os logs para mais detalhes.");
    http_response_code(500);
    exit('Erro ao renomear o diretório clonado. Verifique os logs para mais detalhes.');
}

if (!execShellScript($cleanupScriptPath, [
    'tempDir' => $tempDir,
    'lockFile' => $lockFile,
    'logFile' => $logFile,
])) {
    logMessage("Erro durante a limpeza pós-deploy. Verifique os logs para mais detalhes.");
    http_response_code(500);
    exit('Erro durante a limpeza pós-deploy. Verifique os logs para mais detalhes.');
}

// Remover o arquivo de lock
unlink($lockFile);

logMessage('Deploy concluído com sucesso!');
echo "Deploy concluído com sucesso!";
?>
