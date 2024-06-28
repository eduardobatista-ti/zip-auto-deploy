


<?php
// Sistema de deploy automático
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zip AutoDeploy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets-zip/style.css">
    <script src="/source-zip/scripts.js"></script>
</head>
<body>

<div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="testToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body" id="toastBody">
                        <!-- resultado aqui -->
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>

    <header>
    <div>
        <?php include __DIR__ . '/source-zip/header.html'; ?>
    </header>
    </div>
    <main>
    <section class="box flex-right global" id="section-1">
    <div id="config">
        
        <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // dados que vou receber do formulário
    $repoUrl = $_POST['repo_url'];
    $webhookSecret = $_POST['webhook_secret'];
    $targetDir = __DIR__;

    // definir valores padrão para os parâmetros removidos
    $tempDir = __DIR__  . '/temp_clone';
    $logFile = __DIR__  . '/logs-zip/deploy.log';
    $lockFile = __DIR__  . '/deploy.lock';

    // aqui crio o arquivo de configurações
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

    // caminho do arquivo de configurações
    $configFile = __DIR__ . '/deploy-config.php';

    // salvar as configurações no arquivo
    if (file_put_contents($configFile, $configContent)) {
        echo "<h2>Configuração salva com sucesso!</h2> <br> <h4>Verifique o arquivo <code>deploy-config.php</code> na raiz do projeto.</h4>";
    } else {
        echo "Erro ao salvar a configuração.";
    }
} else {
    // formulário de configurações
    echo '<form method="POST" action="">
        <label for="repo_url" class="form-label">URL do Repositório:</label><br>
        <input type="text" class="form-control" id="repo_url" name="repo_url" required><br>
        
        <label for="webhook_secret" class="form-label">Secret do Webhook:</label><br>
        <input type="text" class="form-control" id="webhook_secret" name="webhook_secret" required><br>
        
        <input type="submit" class="btn btn-primary" value="Salvar Configuração"> 
        <button type="button" onclick="copyUrl()" class="btn btn-secondary" ><a >Copiar Payload URL</a></button>
    </form>';
}
?>


    </div>
    <div>
    <img src="/assets-zip/final-bg.png" id="bg-right" alt="">
    </div>
    </section>
    <div class="box tuto">
    <h4>Configurar o webhook no GitHub</h4>
    <p>Vá até o seu repositório, clique "Settings", depois em "Webhooks", crie um novo Webhook, e configure como na imagem abaixo:</p>
    <img src="/assets-zip/webhookgit.png" alt="">
    </div>
    </main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>