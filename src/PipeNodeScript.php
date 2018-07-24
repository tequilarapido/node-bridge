<?php

namespace Tequilarapido\NodeBridge;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PipeNodeScript
{
    /**
     * Full path to node script file.
     *
     * @var string the node script to run
     */
    protected $script;

    /**
     * The command to pipe to the node script.
     *
     * @var string the command to pipe to the script ($ <pipe command> | node)
     */
    protected $pipe;

    /**
     * Node executable path.
     * By default we will use `node`
     *
     * @var string
     */
    protected $nodeExecutable = 'node';

    /**
     * Sets the node script full path to run.
     *
     * @param $script
     * @return $this
     */
    public function setScript($script)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * Set node executable/binary path
     *
     * @param $nodeExecutable
     * @return $this
     */
    public function setNodeExecutable($nodeExecutable)
    {
        $this->nodeExecutable = $nodeExecutable;

        return $this;
    }

    /**
     * Sets the command to pipe to the script.
     *
     * @param string $pipe
     *
     * @return Response
     * @throws NodeException
     */
    public function pipe($pipe)
    {
        $this->pipe = $pipe;

        return $this->run();
    }

    /**
     * Sets echo pipe. Passing simple string to the node script
     * ie. echo 'a simple string' | node script.py
     *
     * @param string $string
     *
     * @return Response
     * @throws NodeException
     */
    public function echoPipe($string)
    {
        $escaped = str_replace("'", "\'", $string);

        return $this->pipe("echo '" . $escaped . "'");
    }

    /**
     * Run the script and return output.
     *
     * @return Response
     * @throws NodeException
     */
    public function run()
    {
        $this->beforeRun();

        $process = new Process($this->getCommand());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new NodeException(
                (new ProcessFailedException($process))->getMessage()
            );
        }

        return new Response($process->getOutput());
    }

    /**
     * Build the command to execute.
     *
     * @return string
     */
    public function getCommand()
    {
        return "{$this->pipe} | {$this->nodeExecutable} " . $this->script;
    }

    /**
     * Hook to be run stuff before the script execution.
     */
    protected function beforeRun()
    {
        $this->setUtf8Context();
    }

    /**
     * Fix issues where when node get executed by
     * the php process it will defaults to ascii and tries to
     * read strings in ascii, and mess everythings up.
     *
     * @see https://stackoverflow.com/a/13969829/146253
     */
    protected function setUtf8Context()
    {
        setlocale(LC_ALL, $locale = 'en_US.UTF-8');
        putenv('LC_ALL=' . $locale);
    }
}
