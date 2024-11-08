<?php
namespace nrv\net\render;

use nrv\net\show\Spectacle;

class SpectacleRenderer implements Renderer
{

    private Spectacle $spectacle;

    public function __construct(Spectacle $spectacle)
    {
        $this->spectacle = $spectacle;
    }

    public function render(int $type): string
    {
        switch ($type) {
            case Renderer::LONG:
                return $this->renderLong();
            case Renderer::SHORT:
                return $this->renderShort();
            default:
                return '';
        }
    }
    public function renderLong(): string
    {
        return "<div class='spectacle'>
            <h2>{$this->spectacle->title}</h2>
            <h3>{$this->spectacle->artist}</h3>
            <p>{$this->spectacle->description}</p>
            <p>{$this->spectacle->style}</p>
            <p>{$this->spectacle->duration}</p>
            <img src='{$this->spectacle->image}' alt='image spectacle'>
            <audio controls>
                <source src='{$this->spectacle->extrait}' type='audio/mpeg'>
                Votre navigateur ne supporte pas l'élément audio.
            </audio>
        </div>";
    }
    public function renderShort(): string
    {
        return "<div class='spectacle'>
            <h2>{$this->spectacle->title}</h2>
            <h3>{$this->spectacle->artist}</h3>
            <p>{$this->spectacle->style}</p>
            <p>{$this->spectacle->duration}</p>
            <img src='{$this->spectacle->image}' alt='image spectacle'>
        </div>";
    }
}