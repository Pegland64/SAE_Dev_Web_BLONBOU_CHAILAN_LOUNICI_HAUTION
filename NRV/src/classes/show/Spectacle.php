<?php

namespace nrv\net\show;

use nrv\net\exception\InvalidPropertyNameException;

class Spectacle
{
    private $title;
    private $artist;
    private $description;
    private $style;
    private $duration;
    private $image;
    private $extrait;

    public function __construct($title, $artist, $description, $style, $duration, $image, $extrait)
    {
        $this->title = $title;
        $this->artist = $artist;
        $this->description = $description;
        $this->style = $style;
        $this->duration = $duration;
        $this->image = $image;
        $this->extrait = $extrait;
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        } else {
            throw new InvalidPropertyNameException($property);
        }
    }
}