<?php

namespace nrv\net\render;

interface Renderer
{
    const LONG = 1;
    const SHORT = 2;

    public function render(int $type) : string;
}