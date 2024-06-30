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
$deployScriptShell = dirname(__DIR__) . '/source-zip/deploy.sh';
$logFile = $config['log_file'];

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

// Função para execução manual do deploy
function manualDeploy() {
    global $deployScriptShell;
    $output = shell_exec("bash $deployScriptShell 2>&1");

    // Log da execução do deploy
    logMessage("Resultado do deploy:\n$output");

    // Exibe a mensagem de sucesso e o resultado do deploy
    echo "Deploy executado com sucesso!<br>";
    exit;
}

?>
