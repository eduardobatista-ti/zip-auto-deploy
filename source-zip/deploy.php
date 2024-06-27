<?php
// Depuração: Verificar caminho absoluto

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir o arquivo de configuração

include dirname(__DIR__) . '/deploy-config.php';

// Verificar se $config foi definido corretamente
if (!isset($config)) {
    die("Erro: arquivo de configuração não carregado corretamente.");
}

// Utilizar as configurações do arquivo

// Utilizar as configurações do arquivo
$deployScriptShell = dirname(__DIR__) . '/source/deploy.sh';
$logFile = $config['log_file'];
$githubSecret = $config['webhook_secret'];

// Função para mensagens de log
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

logMessage('Iniciando o deploy');

// Receber o payload do GitHub
$payload = file_get_contents('php://input');
$githubEvent = $_SERVER['HTTP_X_GITHUB_EVENT'];
$githubDelivery = $_SERVER['HTTP_X_GITHUB_DELIVERY'];
$githubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'];

// Verificação da assinatura HMAC
$signature = 'sha256=' . hash_hmac('sha256', $payload, $githubSecret);

if (!hash_equals($githubSignature, $signature)) {
    http_response_code(403);
    logMessage('Forbidden: token inválido.');
    exit('Forbidden: Você não deveria estar aqui hehe');
}

logMessage('Iniciando o deploy');

// Criar um arquivo de lock para evitar deploys simultâneos
file_put_contents($config['lock_file'], '');

// Verifica se o evento é um push
if ($githubEvent == 'push') {
    // Executa o script shell de deploy
    $output = shell_exec("bash $deployScriptShell 2>&1");
    
    // Log da execução do deploy
    file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Resultado do deploy:\n$output\n\n", FILE_APPEND);

    echo "Deploy executado com sucesso!";
} else {
    echo "Evento ignorado: $githubEvent";
}

// Remover o arquivo de lock
unlink($config['lock_file']);
?>
