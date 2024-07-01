function showToast(content) {
    document.getElementById('toastBody').innerHTML = content;
    var toast = new bootstrap.Toast(document.getElementById('testToast'));
    toast.show();
}

function showToastCode(content) {
    document.getElementById('toastBody-code').innerHTML = content;
    var toastCode = new bootstrap.Toast(document.getElementById('testToast-code'));
    toastCode.show();
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
    const relativePath = 'auto-deploy/deploy.php';
    
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

function copySecretValue() {
    // Obtém o valor do input escondido
    const secretValue = document.getElementById('webhook_secret').value;

    // Usa a API de Clipboard para copiar o texto
    navigator.clipboard.writeText(secretValue).then(function() {
        
        document.getElementById('toastBody').textContent = 'Secret copiado para área de transferência';

        const toastElement = document.getElementById('testToast');
        const toast = new bootstrap.Toast(toastElement);

        toast.show();
    }).catch(err => {
        console.error('Erro ao copiar Payload URL: ', err);
    });
}

function ToastDeployView() {
    var toastElement = document.getElementById('testToast-code');
    var closeButton = toastElement.querySelector('.btn-close');        

    // Marca o botão como não clicado inicialmente
    closeButton.dataset.clicked = "false";

    // Adiciona um evento de clique ao botão de fechar para marcar como clicado
    closeButton.addEventListener('click', function() {
        closeButton.dataset.clicked = "true";
    });

    // Intercepta o evento de fechamento e permite apenas se o botão de fechar foi clicado
    toastElement.addEventListener('hide.bs.toast', function (event) {
        if (closeButton.dataset.clicked === "false") {
            event.preventDefault();  // Impede que o toast feche se o botão não foi clicado
        }
    });

    // Reseta o estado do botão após o fechamento
    toastElement.addEventListener('hidden.bs.toast', function () {
        closeButton.dataset.clicked = "false";  // Reseta para o próximo uso
    });
};


function deployNow() {
        fetch('/manual-deploy.php')
        .then(response => response.text())
        .then(data => showToastCode(data))
        .catch(error => showToastCode("Erro ao fazer a solicitação: " + error));
}
