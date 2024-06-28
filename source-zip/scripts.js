function showToast(content) {
    document.getElementById('toastBody').innerHTML = `<pre>${content}</pre>`;
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