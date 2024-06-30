<?php

// Incluir o arquivo de configuração
include dirname(__DIR__) . '/deploy-config.php';

// Utilizar as configurações do arquivo
$deployScriptShell = dirname(__DIR__) . '/source-zip/deploy.sh';
$logFile = $config['log_file'];

// Função para mensagens de log
function logMessage($message, $logFile) {
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

// Verificar se é uma requisição POST válida com o parâmetro 'deploy'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deploy'])) {
    // Log inicial
    logMessage('Iniciando o deploy manual.', $logFile);

    // Verifica se o script de deploy existe e é executável
    if (!file_exists($deployScriptShell)) {
        http_response_code(500);
        echo "Erro: O script de deploy não foi encontrado.";
        logMessage('Erro: O script de deploy não foi encontrado.', $logFile);
        exit;
    }

    // Verifica se o script de deploy é executável
    if (!is_executable($deployScriptShell)) {
        http_response_code(500);
        echo "Erro: O script de deploy não é executável.";
        logMessage('Erro: O script de deploy não é executável.', $logFile);
        exit;
    }

    // Executa o script shell de deploy
    $output = shell_exec("bash $deployScriptShell 2>&1");

    // Verifica se a execução foi bem-sucedida
    if ($output === null) {
        http_response_code(500);
        echo "Erro ao executar o script de deploy.";
        logMessage("Erro ao executar o script de deploy.", $logFile);
        exit;
    }

    // Log da execução do deploy
    logMessage("Resultado do deploy:\n$output", $logFile);

    // Responde com sucesso e o resultado do deploy
    http_response_code(200);
    echo "Deploy executado com sucesso!<br>";
    echo nl2br($output); // nl2br para preservar as quebras de linha na saída
    exit;
} else {
    http_response_code(400);
    echo "Requisição inválida para manual-deploy.php.";
    logMessage("Requisição inválida para manual-deploy.php.", $logFile);
    exit;
}
?>
