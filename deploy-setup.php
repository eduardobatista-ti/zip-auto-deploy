<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber os dados do formulário
    $repoUrl = $_POST['repo_url'];
    $webhookSecret = $_POST['webhook_secret'];
    $targetDir = __DIR__;

    // Definir valores padrão para os parâmetros removidos
    $tempDir = __DIR__  . '/temp_clone';
    $logFile = __DIR__  . '/logs/deploy.log';
    $lockFile = __DIR__  . '/deploy.lock';

    // Criar o conteúdo do arquivo de configuração
    $configContent = <<<EOL
<?php
// Configurações de deploy
\$config = [
    'repo_url' => '$repoUrl',
    'webhook_secret' => '$webhookSecret',
    'target_dir' => '$targetDir',
    'temp_dir' => '$tempDir',
    'log_file' => '$logFile',
    'lock_file' => '$lockFile',
];
EOL;

    // Caminho do arquivo de configuração
    $configFile = __DIR__ . '/deploy-config.php';

    // Salvar as configurações no arquivo
    if (file_put_contents($configFile, $configContent)) {
        echo "Configuração salva com sucesso! <br> Verifique o arquivo <code>deploy-config.php</code> na raiz do projeto.";
    } else {
        echo "Erro ao salvar a configuração.";
    }
} else {
    // Mostrar o formulário de configuração
    echo '<form method="POST" action="">
        <label for="repo_url">URL do Repositório:</label><br>
        <input type="text" id="repo_url" name="repo_url" required><br><br>
        
        <label for="webhook_secret">Secret do Webhook:</label><br>
        <input type="text" id="webhook_secret" name="webhook_secret" required><br><br>
        
        <input type="submit" value="Salvar Configuração">
    </form>';
}
?>
