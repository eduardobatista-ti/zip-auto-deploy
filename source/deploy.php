<?php
// Caminho para o script shell de deploy
$deployScriptShell = '/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/source/deploy.sh';

//diretorio de log
$logFile = '/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/logs/deploy.log';

// Configurações do webhook do GitHub
$githubSecret = '1'; //insira a secret que está no webhook do github
$githubEvent = $_SERVER['HTTP_X_GITHUB_EVENT'];
$githubDelivery = $_SERVER['HTTP_X_GITHUB_DELIVERY'];
$githubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'];

//mensagens de log
function logMessage($message) {
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    $logMessage = "[$timestamp] $message\n";
    
    // Abra o arquivo de log para escrita (append)
    $logHandle = fopen($logFile, 'a');
    if ($logHandle) {
        fwrite($logHandle, $logMessage);
        fclose($logHandle);
    } else {
        error_log("Não foi possível abrir o arquivo de log para escrita: $logFile");
    }
}

// Receber o payload do GitHub
$payload = file_get_contents('php://input');

// Verificação da assinatura HMAC
$signature = 'sha256=' . hash_hmac('sha256', $payload, $githubSecret);

/*
// Comparação segura para evitar timing attacks
if (!hash_equals($githubSignature, $signature)) {
    header('HTTP/1.1 403 Forbidden');
    die("Assinatura inválida.");
}
*/

if (!hash_equals($githubSignature, $signature)) {
    http_response_code(403);
    logMessage('Forbidden: token inválido.');
    exit('Forbidden');
}

// Verificar se outro deploy está em andamento ####################################################
$lockFile = '/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/ziper' . '/deploy.lock';
if (file_exists($lockFile)) {
    logMessage('Deploy já está em andamento.');
    exit('Deploy em andamento');
}

logMessage('Iniciando o deploy');

// Criar um arquivo de lock para evitar deploys simultâneos ##################################
file_put_contents($lockFile, '');

// Verifica se o evento é um push
if ($githubEvent == 'push') {
    // Executa o script shell de deploy
    $output = shell_exec("bash $deployScriptShell 2>&1");
    
    // Log da execução do deploy
    file_put_contents('/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/logs/deploy.log', "[" . date('Y-m-d H:i:s') . "] Resultado do deploy:\n$output\n\n", FILE_APPEND);

    echo "Deploy executado com sucesso!";
} else {
    echo "Evento ignorado: $githubEvent";
}

// Remover o arquivo de lock #######################
unlink($lockFile);

?>
