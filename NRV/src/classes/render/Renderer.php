<?php

namespace nrv\net\render;

interface Renderer
{
    const COMPACT = 1;
    const FULL = 2;

    public function render(int $type) : string;
}