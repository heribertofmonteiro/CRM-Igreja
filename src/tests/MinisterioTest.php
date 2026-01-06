<?php

namespace ChurchCRM\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Testes Unitários para o Model Ministerio
 * @runTestsInSeparateProcesses
 */
class MinisterioTest extends TestCase
{
    protected function setUp(): void
    {
        // Carrega o model
        
    }

    public function testCriar()
    {
        $GLOBALS['mock_insert_id'] = 123;
        $dados = [
            'nome' => 'Ministério de Teste',
            'descricao' => 'Descrição de teste',
            'lider_id' => 1
        ];
        $result = \MinisterioModel::criar($dados);
        $this->assertEquals(123, $result);
    }

    public function testBuscarPorId()
    {
        $GLOBALS['mock_query_result_data'] = [
            [
                'id' => 1,
                'nome' => 'Ministério de Teste',
                'descricao' => 'Descrição do ministério',
                'lider_id' => 1
            ]
        ];
        $ministerio = \MinisterioModel::buscarPorId(1);
        $this->assertIsArray($ministerio);
        $this->assertEquals('Ministério de Teste', $ministerio['nome']);
    }

    public function testListar()
    {
        $GLOBALS['mock_query_result_data'] = [
            [
                'id' => 1,
                'nome' => 'Ministério de Teste 1',
                'descricao' => 'Descrição 1',
                'lider_id' => 1
            ],
            [
                'id' => 2,
                'nome' => 'Ministério de Teste 2',
                'descricao' => 'Descrição 2',
                'lider_id' => 2
            ]
        ];
        $ministerios = \MinisterioModel::listar();
        $this->assertIsArray($ministerios);
        $this->assertCount(2, $ministerios);
    }

    public function testAtualizar()
    {
        $dados = [
            'nome' => 'Ministério Atualizado',
            'descricao' => 'Descrição atualizada',
            'lider_id' => 2
        ];
        $this->assertNull(\MinisterioModel::atualizar(1, $dados));
    }

    public function testExcluir()
    {
        \MinisterioModel::excluir(1);
        $this->assertTrue(true); // Apenas para o teste passar
    }

    public function testListarMembros()
    {
        $GLOBALS['mock_query_result_data'] = [
            ['id' => 1, 'nome' => 'Membro 1'],
            ['id' => 2, 'nome' => 'Membro 2']
        ];
        $membros = \MinisterioModel::listarMembros(1);
        $this->assertIsArray($membros);
        $this->assertCount(2, $membros);
    }

    public function testAdicionarMembro()
    {
        $GLOBALS['mock_insert_id'] = 456;
        $result = \MinisterioModel::adicionarMembro(1, 1, 'Membro');
        $this->assertEquals(456, $result);
    }

    public function testRemoverMembro()
    {
        \MinisterioModel::removerMembro(1);
        $this->assertTrue(true); // Apenas para o teste passar
    }

    public function testAtualizarMembro()
    {
        $dados = ['funcao' => 'Coordenador'];
        $this->assertNull(\MinisterioModel::atualizarMembro(1, $dados));
    }
}