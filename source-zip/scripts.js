function showToast(content) {
    document.getElementById('toastBody').innerHTML = content;
    var toast = new bootstrap.Toast(document.getElementById('testToast'));
    toast.show();
}

function testShellExec() {
    fetch('/source-zip/test.php?type=shell_exec')
        .then(response => response.text())
        .then(data => showToast(data))
        .catch(error => showToast("Erro ao fazer a solicitação: " + error));
}

function testGit() {
    fetch('/source-zip/test.php?type=git')
        .then(response => response.text())
        .then(data => showToast(data))
        .catch(error => showToast("Erro ao fazer a solicitação: " + error));
}

function copyUrl() {
    const relativePath = 'source-zip/deploy.php';
    
    const baseUrl = window.location.origin;
    
    const fullUrl = `${baseUrl}/${relativePath}`;

    navigator.clipboard.writeText(fullUrl).then(() => {

        document.getElementById('toastBody').textContent = 'Payload URL copiado para área de transferência';

        const toastElement = document.getElementById('testToast');
        const toast = new bootstrap.Toast(toastElement);

        toast.show();
    }).catch(err => {
        console.error('Erro ao copiar Payload URL: ', err);
    });
}


function deployNow() {
        fetch('/manual-deploy.php')
        .then(response => response.text())
        .then(data => showToast(data))
        .catch(error => showToast("Erro ao fazer a solicitação: " + error));
}

function copySecret() {
    // Captura o valor do campo webhook_secret
    var secretValue = document.getElementById('webhook_secret').value;

    // Cria um elemento temporário de input para facilitar a cópia
    var tempInput = document.createElement('input');
    tempInput.value = secretValue;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    // Alerta ao usuário que a secret foi copiada (opcional)
    alert('Webhook Secret copiado para a área de transferência!');
}

function copySecretShow(){
        
    const Content = `Sua secret foi copiada`;

        document.getElementById('toastBody').textContent = '${Content}';

        showToast('dasdadasd');
}