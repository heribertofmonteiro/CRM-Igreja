<?php
echo "=== Testando Carregamento dos Models ===\n";

// Mock das funções do ChurchCRM
if (!function_exists('RunQuery')) {
    function RunQuery($sql) {
        return null;
    }
}

if (!function_exists('mysqli_fetch_assoc')) {
    function mysqli_fetch_assoc($result) {
        return null;
    }
}

if (!function_exists('mysqli_insert_id')) {
    function mysqli_insert_id($connection) {
        return 1;
    }
}

if (!function_exists('mysqli_affected_rows')) {
    function mysqli_affected_rows($connection) {
        return 1;
    }
}

if (!function_exists('mysqli_real_escape_string')) {
    function mysqli_real_escape_string($connection, $string) {
        return addslashes($string);
    }
}

// Testa carregamento dos models
$models = [
    'Ministerio' => 'modules/ministerio/models/Ministerio.php',
    'Reuniao' => 'modules/ministerio/models/Reuniao.php',
    'Mensagem' => 'modules/ministerio/models/Mensagem.php'
];

foreach ($models as $nome => $caminho) {
    echo "\nTestando $nome...\n";
    
    if (file_exists($caminho)) {
        require_once $caminho;
        $classe = $nome . 'Model';
        
        if (class_exists($classe)) {
            echo "✓ Classe $classe encontrada\n";
            
            // Testa métodos principais
            $metodos = ['listar', 'buscarPorId', 'criar'];
            foreach ($metodos as $metodo) {
                if (method_exists($classe, $metodo)) {
                    echo "  ✓ Método $metodo existe\n";
                } else {
                    echo "  ✗ Método $metodo NÃO existe\n";
                }
            }
        } else {
            echo "✗ Classe $classe NÃO encontrada\n";
        }
    } else {
        echo "✗ Arquivo $caminho NÃO encontrado\n";
    }
}

echo "\n=== Teste concluído ===\n";