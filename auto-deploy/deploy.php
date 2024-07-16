<?php

//criação do LockFile
$lockFile = dirname(__DIR__) .'/deploy.lock';

// Depuração: Verificar caminho absoluto

error_reporting(E_ALL);
ini_set('display_errors', 1);



include dirname(__DIR__) . '/deploy-config.php';


if (!isset($config)) {
    die("Erro: arquivo de configuração não carregado corretamente.");
}



$deployScriptShell = dirname(__DIR__) . '/auto-deploy/deploy.sh';
$logFile = $config['log_file'];
$githubSecret = $config['webhook_secret'];


function logMessage($message) {
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    $logMessage = "[$timestamp] $message\n";

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
$githubEvent = $_SERVER['HTTP_X_GITHUB_EVENT'];
$githubDelivery = $_SERVER['HTTP_X_GITHUB_DELIVERY'];
$githubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'];


$signature = 'sha256=' . hash_hmac('sha256', $payload, $githubSecret);

if (!hash_equals($githubSignature, $signature)) {
    http_response_code(403);
    logMessage('Forbidden: token inválido.');
    exit('Forbidden: Você não deveria estar aqui hehe');
}

// Verificar se o arquivo de lock já existe
if (file_exists($lockFile)) {
    logMessage("Já existe um deploy em andamento. Aguarde e tente novamente mais tarde.");
    die("Já existe um deploy em andamento. Aguarde e tente novamente mais tarde.");
}

// Criar o arquivo de lock
if ($githubEvent == 'push') {   $lockHandle = fopen($lockFile, 'w');
    if ($lockHandle === false || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
    logMessage("Não foi possível criar ou bloquear o arquivo de lock. Verifique as permissões.");
    die("Não foi possível criar ou bloquear o arquivo de lock. Verifique as permissões.");
    }
} else {
    logMessage("Não foi um evento de push.");
    die("Não foi um evento de push.");
}

logMessage('Iniciando o deploy');


if ($githubEvent == 'push') {
  
    $output = shell_exec("bash " . escapeshellarg($deployScriptShell) . " 2>&1");
    
    
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Resultado do deploy:\n$output\n\n", FILE_APPEND);

    echo "Deploy executado com sucesso!";
} else {
    echo "Evento ignorado: $githubEvent";
}



?>
