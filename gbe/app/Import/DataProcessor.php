<?php namespace DemocracyApps\GB\Import;


use DemocracyApps\GB\Services\JsonProcessor;

class DataProcessor {
    private $inputFile = null;
    private $instructionFile = null;
    private $outputFile = null;

    public function __construct ($inputFile, $instructionFile, $outputFile)
    {
        $this->inputFile = $inputFile;
        $this->instructionFile = $instructionFile;
        $this->outputFile = $outputFile;
    }

    /*
     * Program commands - see DemocracyApps\GB\Input\Commands
     *
     * TODO: We need to have the ability from any command that manipulates the output to insert the line number from the original file
     */
    public function run()
    {
        ini_set('auto_detect_line_endings',true);
        $handle = null;
        $commands = null;
        $header = null;

        if ($this->instructionFile != null) {
            $jp = new JsonProcessor();
            $tmp = $jp->minifyJson(file_get_contents($this->instructionFile));
            $config = $jp->decodeJson($tmp, true);
            $commands = $config['commands'];
            if (array_key_exists('header', $config)) $header=$config['header'];
        }
        else {
            $commands = array();
        }

        if ($this->outputFile != null) {
            $handle = fopen($this->outputFile, 'w');
        }
        $lines = file($this->inputFile,  FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
        foreach ($commands as $cmd) {
            $cmdName = $cmd['name'];
            $run = true;
            if (array_key_exists('run',$cmd) && $cmd['run'] == false) $run = false;
            if ($run) {
                $spec = $cmd['specification'];
                $className = "\\DemocracyApps\\GB\\Import\\Commands\\" . ucfirst($cmdName);
                $cmd = new $className ($spec);
                $lines = $cmd->process($lines);
            }
        }

        if ($handle != null) {
            if ($header != null) fwrite($handle, $header. PHP_EOL);
            foreach ($lines as $line) {
                $written = fwrite($handle, $line . PHP_EOL);
            }
        }
    }
}