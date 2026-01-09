<?php

/**
 * Testes Unitários para Models do Módulo Ministério
 * 
 * Este arquivo contém testes completos para validar
 * a funcionalidade dos models implementados
 */

require_once __DIR__ . '/../models/MinisterioModel.php';
require_once __DIR__ . '/../models/Mensagem.php';
require_once __DIR__ . '/../config.php';

class MinisterioTests
{
    private $testResults = [];
    private $pdo;
    
    public function __construct()
    {
        // Conectar ao banco de dados
        $this->pdo = new PDO(
            'mysql:host=localhost;dbname=autonomo;charset=utf8mb4',
            'heriberto',
            '0631'
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * Executar todos os testes
     */
    public function runAllTests()
    {
        echo "🧪 Iniciando Testes do Módulo Ministério\n";
        echo "========================================\n\n";
        
        // Testes de Conexão
        $this->testDatabaseConnection();
        
        // Testes de Models
        $this->testMinisterioModel();
        $this->testMensagemModel();
        
        // Testes de Segurança
        $this->testSecurity();
        
        // Testes de Validação
        $this->testValidation();
        
        // Exibir resultados
        $this->displayResults();
    }
    
    /**
     * Testar conexão com banco de dados
     */
    private function testDatabaseConnection()
    {
        echo "📊 Testando Conexão com Banco de Dados...\n";
        
        try {
            // Testar conexão básica
            $stmt = $this->pdo->query("SELECT 1");
            $result = $stmt->fetch();
            $this->assert($result !== false, "Conexão básica com banco de dados");
            
            // Testar se tabelas existem
            $tables = ['ministerios', 'ministerio_membros', 'ministerio_mensagens'];
            foreach ($tables as $table) {
                $stmt = $this->pdo->query("SHOW TABLES LIKE '$table'");
                $this->assert($stmt->rowCount() > 0, "Tabela '$table' existe");
            }
            
            // Testar estrutura da tabela ministerios
            $stmt = $this->pdo->query("DESCRIBE ministerios");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $requiredColumns = ['id', 'nome', 'descricao', 'lider_id', 'ativo'];
            foreach ($requiredColumns as $column) {
                $this->assert(in_array($column, $columns), "Coluna '$column' existe em ministerios");
            }
            
        } catch (Exception $e) {
            $this->assert(false, "Erro na conexão: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Testar MinisterioModel
     */
    private function testMinisterioModel()
    {
        echo "🏢 Testando MinisterioModel...\n";
        
        try {
            // Testar listagem
            $ministerios = MinisterioModel::list();
            $this->assert(is_array($ministerios), "MinisterioModel::list() retorna array");
            
            // Testar criação
            $testData = [
                'nome' => 'Ministério Teste Unitário',
                'descricao' => 'Descrição do ministério de teste',
                'lider_id' => 1,
                'ativo' => 1
            ];
            
            $id = MinisterioModel::create($testData);
            $this->assert($id > 0, "MinisterioModel::create() retorna ID válido");
            
            // Testar busca por ID
            $ministerio = MinisterioModel::findById($id);
            $this->assert($ministerio !== false, "MinisterioModel::findById() encontra ministério criado");
            $this->assert($ministerio['nome'] === $testData['nome'], "Dados do ministério correspondem");
            
            // Testar atualização
            $updateData = ['nome' => 'Ministério Teste Atualizado'];
            $result = MinisterioModel::update($id, $updateData);
            $this->assert($result === true, "MinisterioModel::update() atualiza com sucesso");
            
            // Verificar atualização
            $updated = MinisterioModel::findById($id);
            $this->assert($updated['nome'] === $updateData['nome'], "Atualização refletida no banco");
            
            // Testar adicionar membro
            $memberResult = MinisterioModel::addMember($id, 1, 'Membro Teste');
            $this->assert($memberResult === true, "MinisterioModel::addMember() adiciona membro");
            
            // Testar listar membros
            $members = MinisterioModel::listMembers($id);
            $this->assert(is_array($members), "MinisterioModel::listMembers() retorna array");
            $this->assert(count($members) > 0, "Membro adicionado aparece na listagem");
            
            // Testar remoção de membro
            $removeResult = MinisterioModel::removeMember($id, 1);
            $this->assert($removeResult === true, "MinisterioModel::removeMember() remove membro");
            
            // Testar exclusão (soft delete)
            $deleteResult = MinisterioModel::delete($id);
            $this->assert($deleteResult === true, "MinisterioModel::delete() exclui com sucesso");
            
            // Verificar soft delete
            $deleted = MinisterioModel::findById($id);
            $this->assert($deleted['ativo'] == 0, "Soft delete funciona corretamente");
            
            // Limpar dados de teste
            $this->pdo->exec("DELETE FROM ministerio_membros WHERE ministerio_id = $id");
            $this->pdo->exec("DELETE FROM ministerios WHERE id = $id");
            
        } catch (Exception $e) {
            $this->assert(false, "Erro em MinisterioModel: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Testar MensagemModel
     */
    private function testMensagemModel()
    {
        echo "📧 Testando MensagemModel...\n";
        
        try {
            // Testar listagem
            $mensagens = MensagemModel::list();
            $this->assert(is_array($mensagens), "MensagemModel::list() retorna array");
            
            // Testar criação
            $testData = [
                'ministerio_id' => 1,
                'assunto' => 'Mensagem Teste Unitário',
                'conteudo' => 'Conteúdo da mensagem de teste',
                'canal' => 'email',
                'tipo' => 'geral',
                'status' => 'rascunho',
                'criado_por' => 1
            ];
            
            $id = MensagemModel::create($testData);
            $this->assert($id > 0, "MensagemModel::create() retorna ID válido");
            
            // Testar busca por ID
            $mensagem = MensagemModel::findById($id);
            $this->assert($mensagem !== false, "MensagemModel::findById() encontra mensagem criada");
            $this->assert($mensagem['assunto'] === $testData['assunto'], "Dados da mensagem correspondem");
            
            // Testar atualização
            $updateData = ['assunto' => 'Mensagem Teste Atualizada'];
            $result = MensagemModel::update($id, $updateData);
            $this->assert($result === true, "MensagemModel::update() atualiza com sucesso");
            
            // Testar listagem de destinatários
            $destinatarios = MensagemModel::listRecipients(1);
            $this->assert(is_array($destinatarios), "MensagemModel::listRecipients() retorna array");
            
            // Testar preview
            $preview = MensagemModel::generatePreview($testData['conteudo'], 'email');
            $this->assert(is_string($preview), "MensagemModel::generatePreview() retorna string");
            $this->assert(strpos($preview, $testData['conteudo']) !== false, "Preview contém conteúdo original");
            
            // Testar cancelamento
            $cancelResult = MensagemModel::cancel($id);
            $this->assert($cancelResult === true, "MensagemModel::cancel() cancela com sucesso");
            
            // Testar exclusão
            $deleteResult = MensagemModel::delete($id);
            $this->assert($deleteResult === true, "MensagemModel::delete() exclui com sucesso");
            
        } catch (Exception $e) {
            $this->assert(false, "Erro em MensagemModel: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Testar segurança
     */
    private function testSecurity()
    {
        echo "🔐 Testando Segurança...\n";
        
        try {
            // Testar SQL Injection
            $maliciousInput = "'; DROP TABLE ministerios; --";
            $testData = [
                'nome' => $maliciousInput,
                'descricao' => 'Teste de segurança',
                'lider_id' => 1
            ];
            
            // Tentar criar com input malicioso
            $id = MinisterioModel::create($testData);
            $this->assert($id > 0, "Sistema resiste a SQL Injection básica");
            
            // Verificar se tabela ainda existe
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'ministerios'");
            $this->assert($stmt->rowCount() > 0, "Tabela ministerios não foi afetada");
            
            // Limpar
            $this->pdo->exec("DELETE FROM ministerios WHERE id = $id");
            
            // Testar XSS
            $xssInput = '<script>alert("XSS")</script>';
            $testData = [
                'nome' => $xssInput,
                'descricao' => 'Teste XSS',
                'lider_id' => 1
            ];
            
            $id = MinisterioModel::create($testData);
            $ministerio = MinisterioModel::findById($id);
            
            // Verificar se tags HTML estão presentes (não escapamos no nível do model)
            $this->assert(
                strpos($ministerio['nome'], '<script>') !== false,
                "Model armazena HTML como está (escaping é responsabilidade da view)"
            );
            
            // Limpar
            $this->pdo->exec("DELETE FROM ministerios WHERE id = $id");
            
        } catch (Exception $e) {
            $this->assert(false, "Erro em testes de segurança: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Testar validação
     */
    private function testValidation()
    {
        echo "✅ Testando Validação...\n";
        
        try {
            // Testar validação de campos obrigatórios
            $invalidData = []; // Dados vazios
            
            try {
                MinisterioModel::create($invalidData);
                $this->assert(false, "Sistema deve rejeitar dados vazios");
            } catch (Exception $e) {
                $this->assert(true, "Sistema rejeita dados vazios corretamente");
            }
            
            // Testar validação de tipos
            $invalidTypes = [
                'nome' => 123, // Deve ser string
                'lider_id' => 'texto', // Deve ser número
                'ativo' => 'sim' // Deve ser boolean/int
            ];
            
            try {
                MinisterioModel::create($invalidTypes);
                $this->assert(false, "Sistema deve rejeitar tipos inválidos");
            } catch (Exception $e) {
                $this->assert(true, "Sistema rejeita tipos inválidos corretamente");
            }
            
            // Testar validação de comprimento
            $longName = str_repeat('a', 300); // Mais que 255 caracteres
            $testData = [
                'nome' => $longName,
                'descricao' => 'Teste',
                'lider_id' => 1
            ];
            
            try {
                MinisterioModel::create($testData);
                $this->assert(false, "Sistema deve rejeitar nomes muito longos");
            } catch (Exception $e) {
                $this->assert(true, "Sistema rejeita nomes muito longos corretamente");
            }
            
        } catch (Exception $e) {
            $this->assert(false, "Erro em testes de validação: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    /**
     * Adicionar resultado de teste
     */
    private function assert($condition, $description)
    {
        $status = $condition ? "✅ PASSOU" : "❌ FALHOU";
        echo "  $status - $description\n";
        
        $this->testResults[] = [
            'description' => $description,
            'passed' => $condition,
            'status' => $status
        ];
    }
    
    /**
     * Exibir resultados finais
     */
    private function displayResults()
    {
        echo "========================================\n";
        echo "📊 Resultados dos Testes\n";
        echo "========================================\n";
        
        $total = count($this->testResults);
        $passed = array_filter($this->testResults, fn($r) => $r['passed']);
        $passedCount = count($passed);
        $failed = $total - $passedCount;
        
        echo "Total de Testes: $total\n";
        echo "✅ Passaram: $passedCount\n";
        echo "❌ Falharam: $failed\n";
        echo "Taxa de Sucesso: " . round(($passedCount / $total) * 100, 2) . "%\n\n";
        
        if ($failed > 0) {
            echo "Testes que falharam:\n";
            foreach ($this->testResults as $result) {
                if (!$result['passed']) {
                    echo "  ❌ {$result['description']}\n";
                }
            }
        }
        
        echo "\n";
        
        if ($failed === 0) {
            echo "🎉 Todos os testes passaram! O módulo está funcionando corretamente.\n";
        } else {
            echo "⚠️  Alguns testes falharam. Verifique os erros acima.\n";
        }
    }
}

// Executar testes
if (php_sapi_name() === 'cli') {
    $tests = new MinisterioTests();
    $tests->runAllTests();
} else {
    echo "<pre>";
    $tests = new MinisterioTests();
    $tests->runAllTests();
    echo "</pre>";
}
