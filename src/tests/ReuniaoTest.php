<?php

namespace ChurchCRM\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Testes Unitários para o Model Reuniao
 * @runTestsInSeparateProcesses
 */
class ReuniaoTest extends TestCase
{
    protected function setUp(): void
    {
        // Carrega o model
        
    }

    public function testCriarReuniao()
    {
        $dados = [
            'ministerio_id' => 1,
            'titulo' => 'Reunião de Teste',
            'data_reuniao' => '2024-01-01 10:00:00',
            'criado_por' => 1
        ];
        $result = \ReuniaoModel::criar($dados);
        $this->assertIsInt($result);
    }

    public function testBuscarPorId()
    {
        $GLOBALS['mock_query_result_data'] = [
            [
                'id' => 1,
                'titulo' => 'Reunião de Teste',
                'ministerio_id' => 1
            ]
        ];
        $result = \ReuniaoModel::buscarPorId(1);
        $this->assertIsArray($result);
    }

    public function testListar()
    {
        $result = \ReuniaoModel::listar();
        $this->assertIsArray($result);
    }

    public function testAtualizar()
    {
        $dados = [
            'ministerio_id' => 1,
            'titulo' => 'Reunião de Teste Atualizada',
            'data_reuniao' => '2024-01-01 11:00:00',
        ];
        $result = \ReuniaoModel::atualizar(1, $dados);
        $this->assertTrue($result);
    }

    public function testExcluir()
    {
        $result = \ReuniaoModel::excluir(1);
        $this->assertTrue($result);
    }

    public function testListarParticipantes()
    {
        $result = \ReuniaoModel::listarParticipantes(1);
        $this->assertIsArray($result);
    }

    public function testConfirmarRSVP()
    {
        $result = \ReuniaoModel::confirmarRSVP('some_token');
        $this->assertIsBool($result);
    }

    public function testBuscarParticipantePorToken()
    {
        $GLOBALS['mock_query_result_data'] = [
            [
                'reuniao_id' => 1,
                'membro_id' => 1,
                'token_rsvp' => 'some_token'
            ]
        ];
        $result = \ReuniaoModel::buscarParticipantePorToken('some_token');
        $this->assertIsArray($result);
    }

    public function testObterReunioesParaLembrete()
    {
        $result = \ReuniaoModel::obterReunioesParaLembrete();
        $this->assertIsArray($result);
    }
}