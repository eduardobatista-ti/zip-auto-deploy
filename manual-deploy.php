<?php
// Depuração: Verificar caminho absoluto
error_reporting(E_ALL);
ini_set('display_errors', 1);

//criação do LockFile
$lockFile = __DIR__ .'/deploy.lock';

// Incluir o arquivo de configuração de forma segura
$configFile = __DIR__ . '/deploy-config.php';
if (file_exists($configFile)) {
    include $configFile;
} else {
    die("Erro: arquivo de configuração não encontrado.");
}

// Verificar se $config foi definido corretamente
if (!isset($config) || !is_array($config)) {
    die("Erro: arquivo de configuração não carregado corretamente.");
}

// Utilizar as configurações do arquivo
$deployScriptShell = __DIR__ . '/auto-deploy/deploy.sh';
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

// Verificar se o arquivo de lock já existe
if (file_exists($lockFile)) {
    logMessage("Já existe um deploy em andamento. Aguarde e tente novamente mais tarde.");
    die("Já existe um deploy em andamento. Aguarde e tente novamente mais tarde.");
}

// Criar o arquivo de lock
$lockHandle = fopen($lockFile, 'w');
    
if ($lockHandle === false || !flock($lockHandle, LOCK_EX | LOCK_NB)) {
logMessage("Não foi possível criar ou bloquear o arquivo de lock. Verifique as permissões.");
die("Não foi possível criar ou bloquear o arquivo de lock. Verifique as permissões.");
}

// Função para execução manual do deploy
function manualDeploy() {

    
    global $deployScriptShell, $logFile;
    $output = shell_exec("bash " . escapeshellarg($deployScriptShell) . " 2>&1");

    // Log da execução do deploy
    if ($output === null) {
        logMessage("Erro ao executar o script de deploy.");
        echo "Erro ao executar o deploy. Verifique o log para mais informações.";
    } else {
        logMessage("Resultado do deploy:\n$output");
        echo "Deploy executado com sucesso!<br>";
        echo nl2br($output); // Exibe o resultado do deploy
    }
    exit;
}

manualDeploy();

?>
