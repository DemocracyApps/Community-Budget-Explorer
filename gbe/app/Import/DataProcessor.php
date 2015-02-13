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
     * Program commands
     *  read - probably has options to deal with newlines and such
     *  select <pattern> - keep only lines that match pattern
     *  discard <pattern> - keep only lines that don't match the pattern
     */
    public function run()
    {
        ini_set('auto_detect_line_endings',true);
        $handle = null;
        $commands = null;

        if ($this->instructionFile != null) {
            $jp = new JsonProcessor();
            $tmp = $jp->minifyJson(file_get_contents($this->instructionFile));
            $commands = $jp->decodeJson($tmp, null)['commands'];
        }

        if ($this->outputFile != null) {
            $handle = fopen($this->outputFile, 'w');
        }
        $lines = file($this->inputFile,  FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

        foreach ($commands as $cmd) {
            $cmdName = $cmd['name'];
            $spec = $cmd['specification'];
            $className = "\\DemocracyApps\\GB\\Import\\Commands\\" . ucfirst($cmdName);
            $cmd = new $className ($spec);
            $lines = $cmd->process($lines);
        }

        if ($handle != null) {
            foreach ($lines as $line) {
                $written = fwrite($handle, $line . PHP_EOL);
            }
        }
    }
}