<?php

namespace Tequilarapido\NodeBridge;

class Response
{
    /** @var string */
    protected $output;

    public function __construct($output)
    {
        $this->output = $output;
    }

    public function output()
    {
        return $this->output;
    }

    public function json()
    {
        return json_decode($this->output);
    }

}