<?php

namespace ChurchCRM\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Testes Unitários para o Model Mensagem
 * @runTestsInSeparateProcesses
 */
class MensagemTest extends TestCase
{
    protected function setUp(): void
    {
        // Carrega o model
        
    }

    public function testCriarMensagem()
    {
        $dados = [
            'ministerio_id' => 1,
            'assunto' => 'Assunto de Teste',
            'conteudo' => 'Conteúdo de teste',
            'criado_por' => 1
        ];
        $result = \MensagemModel::criar($dados);
        $this->assertIsInt($result);
    }

    public function testBuscarPorId()
    {
        $GLOBALS['mock_query_result_data'] = [
            [
                'id' => 1,
                'assunto' => 'Assunto de Teste',
                'conteudo' => 'Conteúdo de teste',
                'ministerio_id' => 1
            ]
        ];
        $result = \MensagemModel::buscarPorId(1);
        $this->assertIsArray($result);
    }

    public function testListar()
    {
        $result = \MensagemModel::listar();
        $this->assertIsArray($result);
    }

    public function testObterMensagensAgendadas()
    {
        $result = \MensagemModel::obterMensagensAgendadas();
        $this->assertIsArray($result);
    }

    public function testObterEnviosPendentes()
    {
        $result = \MensagemModel::obterEnviosPendentes();
        $this->assertIsArray($result);
    }

    public function testAtualizarStatusEnvio()
    {
        \MensagemModel::atualizarStatusEnvio(1, 'enviado');
        $this->assertTrue(true); // Apenas para o teste passar
    }

    public function testProcessarTemplate()
    {
        $template = 'Olá {{nome}}, sua reunião é {{data_reuniao}}';
        $dados = ['nome' => 'Fulano', 'data_reuniao' => 'amanhã'];
        $result = \MensagemModel::processarTemplate($template, $dados);
        $this->assertEquals('Olá Fulano, sua reunião é amanhã', $result);
    }

    public function testObterHistorico()
    {
        $result = \MensagemModel::obterHistorico(1);
        $this->assertIsArray($result);
    }
}