<?php
if (isset($_GET['type'])) {
    $type = $_GET['type'];

    if ($type === 'shell_exec') {
        // Teste para verificar se shell_exec está habilitado
        if (function_exists('shell_exec')) {
            echo "shell_exec está habilitado.";
        } else {
            echo "shell_exec NÃO está habilitado.";
        }
    } elseif ($type === 'git') {
        // Teste para verificar se o Git está instalado
        $output = shell_exec('git --version 2>&1');
        if (strpos($output, 'git version') !== false) {
            echo "Git está instalado: " . $output;
        } else {
            echo "Git NÃO está instalado.";
        }
    } else {
        echo "Tipo de teste inválido.";
    }
} else {
    echo "Nenhum tipo de teste especificado.";
}
