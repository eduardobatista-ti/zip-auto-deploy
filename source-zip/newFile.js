{
    document.getElementById('toastBody').textContent = 'Secret copiada para área de transferência';
    const toastElement = document.getElementById('testToast');
    const toast = new bootstrap.Toast(toastElement);

    toast.show();
}
