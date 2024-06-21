<?php
// Caminho para o script shell de deploy
$deployScriptShell = '/home/zipcloudbr/web/cdn.zipcloud.com.br/public_html/source/deploy.sh';

// Configurações do webhook do GitHub
$githubSecret = 'seu_secret_aqui'; // Insira o segredo configurado no GitHub
$githubEvent = $_SERVER['HTTP_X_GITHUB_EVENT'];
$githubDelivery = $_SERVER['HTTP_X_GITHUB_DELIVERY'];
$githubSignature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'];

// Receber o payload do GitHub
$payload = file_get_contents('php://input');

// Verificação da assinatura HMAC
$signature = 'sha256=' . hash_hmac('sha256', $payload, $githubSecret);

// Comparação segura para evitar timing attacks
if (!hash_equals($githubSignature, $signature)) {
    header('HTTP/1.1 403 Forbidden');
    die("Assinatura inválida.");
}

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
?>
