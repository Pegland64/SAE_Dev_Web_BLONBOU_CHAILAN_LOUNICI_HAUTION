<?php

namespace nrv\net\render;

interface Renderer
{
    public function render(int $type) : string;
    const COMPACT = 1;
    const FULL = 2;
}