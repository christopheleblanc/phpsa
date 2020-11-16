<?php
/**
 * PHP Streams Aggregator
 * Version 1.0.0
 * Author: Christophe Leblanc
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * Copyright (C) 2018 - 2020 Christophe Leblanc
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * This file is part of "PHP Streams Aggregator".
 *
 * "PHP Streams Aggregator" is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * "PHP Streams Aggregator" is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with "PHP Streams Aggregator".  If not, see
 * <https://www.gnu.org/licenses/>.
 */

/**
 * Options
 * Class used to detect and store command line options.
 * @see https://www.php.net/manual/fr/function.getopt.php
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Controllers\FileStringParser;
use PHPStreamsAggregator\Models\FileData;

/**
 * Options
 */
class Options{

    /**
     * @var boolean - Define is the program must display texts on the terminal/command prompt
     *                during the process. Default = false.
     */
    public $isVerbose;

    /**
     * @var boolean - Define if the program must being started in "infos" mode.
     */
    public $isModeInfos;

    /**
     * @var boolean - Define if the program must being started in "help" mode.
     */
    public $isModeHelp;

    /**
     * @var boolean - Define if the program must being started in "test" mode.
     */
    public $isModeTest;

    /**
     * @var boolean - Define if the program must delete temporary data.
     */
    public $isClear;

    /**
     * @var boolean - Define if the streams update should be forced. Default = false.
     */
    public $forceUpdate;

    /**
     * @var FileData|null - Instance of FileData corresponding to the streams list file, or null. Default = null.
     */
    public $downloadListFile;

    /*
     * Constructor
     */
    public function __construct()
    {

        $this->isVerbose = false;
        $this->forceUpdate = false;
        $this->isModeInfos = false;
        $this->isModeHelp = false;
        $this->isModeTest = false;
        $this->isClear = false;
        $this->downloadListFile = null;

        // Command line options
        // https://www.php.net/manual/fr/function.getopt.php

        $shortopts  = "";
        $shortopts .= "c"; // Clear
        $shortopts .= "h"; // Help
        $shortopts .= "i"; // Infos
        $shortopts .= "u"; // Force update
        $shortopts .= "t"; // Force update
        $shortopts .= "v"; // Verbose
        $shortopts .= "l:"; // Verbose

        $longopts  = array(
            "clear",
            "help",
            "infos",
            "update",
            "test",
            "verbose",
            "list:"
        );

        $options = getopt($shortopts, $longopts);

        if(isset($options) && $options !== false){

            if(array_key_exists("clear", $options) || array_key_exists("c", $options)){
                $this->isClear = true;
            }

            if(array_key_exists("help", $options) || array_key_exists("h", $options)){
                $this->isModeHelp = true;
            }

            if(array_key_exists("infos", $options) || array_key_exists("i", $options)){
                $this->isModeInfos = true;
            }

            if(array_key_exists("verbose", $options) || array_key_exists("v", $options)){
                $this->isVerbose = true;
            }

            if(array_key_exists("update", $options) || array_key_exists("u", $options)){
                $this->forceUpdate = true;
            }

            if(array_key_exists("test", $options) || array_key_exists("t", $options)){
                $this->isModeTest = true;
            }

            $listInput = null;
            if(array_key_exists("list", $options)){
                $listInput = $options["list"];
            }
            if(array_key_exists("l", $options)){
                $listInput = $options["l"];
            }

            if($listInput !== null){

                $tmp = trim((string)$listInput);

                if(strlen($tmp) > 0){

                    // Parse the value to check if value "output" is a filename, an absolute path or a relative path

                    $parser = new FileStringParser();
                    $parser->parse($tmp);

                    $absolutePath;
                    switch($parser->getValueType()){
                        case FileData::ABSOLUTE_PATH:{
                            $absolutePath = $parser->getFilePath();
                        }break;
                        case FileData::RELATIVE_PATH:{
                            $absolutePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . $parser->getFilePath();
                        }break;
                        case FileData::FILENAME:{
                            $absolutePath = Data::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::DATA_DIR_NAME .
                            DIRECTORY_SEPARATOR . Con::CONFIG_DIR_NAME;
                        }break;
                    }
                    $this->downloadListFile = new FileData(
                        $parser->getValueType(),
                        $parser->getFileName(),
                        $absolutePath
                    );

                    unset($parser);

                }

            }

        }

    }

    /**
     * If the program must delete temporary data.
     * @returns boolean
     */
    public function getIsClear()
    {
        return $this->isClear;
    }

    /**
     * If the program must display texts on the terminal/command prompt
     * during the process.
     * @returns boolean
     */
    public function getIsVerbose()
    {
        return $this->isVerbose;
    }

    /**
     * If the streams update should be forced.
     * @returns boolean
     */
    public function getForceUpdate()
    {
        return $this->forceUpdate;
    }

    /**
     * If the program must being started in "test" mode.
     * @returns boolean
     */
    public function getIsModeTest()
    {
        return $this->isModeTest;
    }

    /**
     * If the program must being started in "infos" mode.
     * @returns boolean
     */
    public function getIsModeInfos()
    {
        return $this->isModeInfos;
    }

    /**
     * If the program must being started in "infos" mode.
     * @returns boolean
     */
    public function getIsModeHelp()
    {
        return $this->isModeHelp;
    }

    /**
     * Get the DownloadListFile if defined, or null.
     * @returns &FileData|null
     */
    public function getDownloadListFile()
    {
        return $this->downloadListFile;
    }

    /**
     * Create an instance of this class and store it inside the class.
     */
    static public function create()
    {
        return new Options();
    }

}