<?php

namespace nrv\net\render;

/**
 * Interface Renderer
 * Définit les méthodes de rendu pour différents types de contenu.
 */
interface Renderer
{
    // Constante pour le rendu compact
    const COMPACT = 1;

    // Constante pour le rendu complet
    const FULL = 2;

    /**
     * Méthode de rendu
     * @param int $type Le type de rendu (COMPACT ou FULL)
     * @return string Le contenu rendu
     */
    public function render(int $type) : string;
}