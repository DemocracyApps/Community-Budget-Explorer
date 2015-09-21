<?php namespace DemocracyApps\GB\Import;
/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
 */

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