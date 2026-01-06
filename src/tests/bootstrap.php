<?php

// Helpers CRM_mysqli_* usados pelos models para permitir mocks em testes
function CRM_mysqli_fetch_assoc($result)
{
    // Estrutura de resultado simulada via $GLOBALS['mock_query_result_data']
    if (is_array($result) && isset($result[0]) && is_array($result[0])) {
        // Se for um array de linhas, retornar a próxima linha e remover da lista
        return array_shift($result);
    }
    if ($result instanceof ArrayIterator) {
        if ($result->valid()) {
            $row = $result->current();
            $result->next();
            return $row;
        }
        return null;
    }
    if (isset($GLOBALS['mock_query_result_data']) && is_array($GLOBALS['mock_query_result_data'])) {
        return array_shift($GLOBALS['mock_query_result_data']);
    }
    return null;
}

function CRM_mysqli_insert_id($connection)
{
    return isset($GLOBALS['mock_insert_id']) ? $GLOBALS['mock_insert_id'] : 1;
}

function CRM_mysqli_affected_rows($connection)
{
    return isset($GLOBALS['mock_affected_rows']) ? $GLOBALS['mock_affected_rows'] : 1;
}

function CRM_mysqli_real_escape_string($connection, $string)
{
    return addslashes($string);
}

// Define dummy classes to satisfy type hints
if (!class_exists('mysqli')) {
    class mysqli {}
}

// Não definimos uma classe dummy mysqli_result; os testes usam ArrayIterator

// Mock a global connection variable
$GLOBALS['cnInfoCentral'] = new mysqli();

// Mock das funções do ChurchCRM
if (!function_exists('RunQuery')) {
    function RunQuery($sql) {
        // Em testes, retornamos dados simulados de acordo com $GLOBALS
        if (isset($GLOBALS['mock_query_result_data']) && is_array($GLOBALS['mock_query_result_data'])) {
            // Retornar um iterador para simular linhas sequenciais
            return new ArrayIterator($GLOBALS['mock_query_result_data']);
        }
        // Para operações de escrita, basta retornar true
        return true;
    }
}

// Nota: não sobrescrevemos funções nativas mysqli_* para evitar conflitos

// Carrega os models necessários para os testes
require_once __DIR__ . '/../modules/ministerio/models/Ministerio.php';
require_once __DIR__ . '/../modules/ministerio/models/Reuniao.php';
require_once __DIR__ . '/../modules/ministerio/models/Mensagem.php';