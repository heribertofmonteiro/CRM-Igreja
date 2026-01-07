<?php

namespace ChurchCRM;

/**
 * Ministério Model
 * 
 * Represents a ministry in the ChurchCRM system
 * @package ChurchCRM
 */
class Ministerio
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $nome;

    /**
     * @var string|null
     */
    protected $descricao;

    /**
     * @var int
     */
    protected $lider_id;

    /**
     * @var int|null
     */
    protected $coordenador_id;

    /**
     * @var int
     */
    protected $ativo = 1;

    /**
     * @var \DateTime|null
     */
    protected $criado_em;

    /**
     * @var \DateTime|null
     */
    protected $atualizado_em;

    /**
     * @var bool
     */
    protected $isNew = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->criado_em = new \DateTime();
    }

    /**
     * Get the [id] column value.
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the [id] column value.
     * @param int $v
     * @return $this
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->isNew = false;
        }

        return $this;
    }

    /**
     * Get the [nome] column value.
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the [nome] column value.
     * @param string $v
     * @return $this
     */
    public function setNome($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->nome !== $v) {
            $this->nome = $v;
        }

        return $this;
    }

    /**
     * Get the [descricao] column value.
     * @return string|null
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Set the [descricao] column value.
     * @param string|null $v
     * @return $this
     */
    public function setDescricao($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->descricao !== $v) {
            $this->descricao = $v;
        }

        return $this;
    }

    /**
     * Get the [lider_id] column value.
     * @return int
     */
    public function getLiderId()
    {
        return $this->lider_id;
    }

    /**
     * Set the [lider_id] column value.
     * @param int $v
     * @return $this
     */
    public function setLiderId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->lider_id !== $v) {
            $this->lider_id = $v;
        }

        return $this;
    }

    /**
     * Get the [coordenador_id] column value.
     * @return int|null
     */
    public function getCoordenadorId()
    {
        return $this->coordenador_id;
    }

    /**
     * Set the [coordenador_id] column value.
     * @param int|null $v
     * @return $this
     */
    public function setCoordenadorId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->coordenador_id !== $v) {
            $this->coordenador_id = $v;
        }

        return $this;
    }

    /**
     * Get the [ativo] column value.
     * @return int
     */
    public function getAtivo()
    {
        return $this->ativo;
    }

    /**
     * Set the [ativo] column value.
     * @param int $v
     * @return $this
     */
    public function setAtivo($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->ativo !== $v) {
            $this->ativo = $v;
        }

        return $this;
    }

    /**
     * Get the [criado_em] column value.
     * @return \DateTime|null
     */
    public function getCriadoEm()
    {
        return $this->criado_em;
    }

    /**
     * Set the [criado_em] column value.
     * @param \DateTime|null $v
     * @return $this
     */
    public function setCriadoEm($v)
    {
        if ($v !== null) {
            $v = new \DateTime($v);
        }

        if ($this->criado_em !== $v) {
            $this->criado_em = $v;
        }

        return $this;
    }

    /**
     * Get the [atualizado_em] column value.
     * @return \DateTime|null
     */
    public function getAtualizadoEm()
    {
        return $this->atualizado_em;
    }

    /**
     * Set the [atualizado_em] column value.
     * @param \DateTime|null $v
     * @return $this
     */
    public function setAtualizadoEm($v)
    {
        if ($v !== null) {
            $v = new \DateTime($v);
        }

        if ($this->atualizado_em !== $v) {
            $this->atualizado_em = $v;
        }

        return $this;
    }

    /**
     * Whether the object is new.
     * @return bool
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * Get the leader name
     * @return string|null
     */
    public function getLeaderName()
    {
        return 'Líder #' . $this->getLiderId();
    }

    /**
     * Check if ministry is active
     * @return bool
     */
    public function isActive()
    {
        return $this->getAtivo() == 1;
    }

    /**
     * Get formatted creation date
     * @return string
     */
    public function getFormattedCreationDate()
    {
        return $this->getCriadoEm()->format('d/m/Y H:i');
    }

    /**
     * Get ministry status badge HTML
     * @return string
     */
    public function getStatusBadge()
    {
        if ($this->isActive()) {
            return '<span class="badge bg-success">Ativo</span>';
        } else {
            return '<span class="badge bg-secondary">Inativo</span>';
        }
    }

    /**
     * Get ministry actions HTML
     * @return string
     */
    public function getActionsHtml()
    {
        $id = $this->getId();
        $html = '<div class="btn-group" role="group">';
        $html .= '<button type="button" class="btn btn-sm btn-primary" onclick="editMinisterio(' . $id . ')">';
        $html .= '<i class="fas fa-edit"></i> Editar';
        $html .= '</button>';
        $html .= '<button type="button" class="btn btn-sm btn-info" onclick="viewMinisterio(' . $id . ')">';
        $html .= '<i class="fas fa-eye"></i> Ver';
        $html .= '</button>';
        $html .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteMinisterio(' . $id . ')">';
        $html .= '<i class="fas fa-trash"></i> Excluir';
        $html .= '</button>';
        $html .= '</div>';
        return $html;
    }

    /**
     * String representation of this object
     * @return string
     */
    public function __toString()
    {
        return $this->getNome();
    }
}
