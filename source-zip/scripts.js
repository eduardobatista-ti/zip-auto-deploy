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
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "/manual-deploy.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                document.getElementById("toastBody").innerHTML = xhr.responseText;
            } else {
                document.getElementById("toastBody").innerHTML = "Erro: " + xhr.status + " - " + xhr.statusText;
            }
            const toastElement = document.getElementById('testToast');
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    };
    xhr.send("deploy=true");
}


/*
function deployNow() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "/source-zip/deploy.php", true);  // Envia a solicitação para o próprio arquivo PHP
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("deploy-result").innerHTML = xhr.responseText;
        }
    };
    xhr.send("deploy=true"); // Envia um parâmetro para indicar a ação de deploy
}
*/
