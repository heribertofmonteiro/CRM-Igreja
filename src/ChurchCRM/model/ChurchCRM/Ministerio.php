<?php

namespace ChurchCRM\model\ChurchCRM;

use ChurchCRM\model\ChurchCRM\Base\Ministerio as BaseMinisterio;

/**
 * Skeleton subclass for representing a row from the 'ministerios' table.
 *
 * @package    ChurchCRM\model\ChurchCRM
 */
class Ministerio extends BaseMinisterio
{
    /**
     * Get the leader name
     * @return string|null
     */
    public function getLeaderName()
    {
        // This would typically use a Person model relationship
        // For now, return a placeholder
        return 'Líder #' . $this->getLiderId();
    }

    /**
     * Get active members count
     * @return int
     */
    public function getActiveMembersCount()
    {
        // This would typically count from ministerio_membros table
        return 0; // Placeholder
    }

    /**
     * Get upcoming meetings count
     * @return int
     */
    public function getUpcomingMeetingsCount()
    {
        // This would typically count future meetings
        return 0; // Placeholder
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
}
