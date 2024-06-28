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