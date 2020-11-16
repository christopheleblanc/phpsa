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
 * ProcessMaker
 * Top level class used by the program to finalize the data process using plugin
 * class of type "Maker".
 *
 * A plugin of type "Maker" is intended to finalize the update/parse process.
 * In most case, it will be used to save the parsed data to an output file, to
 * add them to a database, send them by email or execute an other program.
 *
 * Data flow and plugins representation:
 *
 * Parse data from all streams  > Process/Mix/Aggregate all data > Make
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * [PARSE]        |
 * [PARSE]        |------------ > [MIX] ------------------------ > [MAKE]
 * [PARSE]        |
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 *
 * This class is responsible for the execution of the following process, while
 * reporting any errors/Exception:
 * - Create an instance of "Maker"
 * - Call the member method "init()"
 * - Call the member method "make()"
 * - Release memory
 *
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Data;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\AstractProcessEvents;

/**
 * ProcessMaker
 */
class ProcessMaker extends AstractProcessEvents{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Instanciate and run a plugin/class of type "Maker".
     * @param ContextFactory - The context factory.
     * @param FileData       - Output file data
     * @param &Array         - Array containing parsed objects (from "Mixer")
     */
    public function make(&$contextFactory, &$outputFile, &$mixedObjects)
    {

        // Check and create output file directory
        //

        $outputFilepath = $outputFile->getFilePath() . DIRECTORY_SEPARATOR . $outputFile->getFileName();
        if(!file_exists($outputFile->getFilePath())){
            if($outputFile->isPath()){
                $errStr1 = 'Error while trying to create directory "' . $outputFile->getFilePath() . '".';
                try{
                    if(!Data::makeDirectories($outputFile->getFilePath())){
                        $this->errorText = $errStr1 . ' Program stopped.';
                        return false;
                    }
                }
                catch(\Exception $ex){
                    $this->errorText = $errStr1 . ' Function "mkdir()" thrown an Exception. Message: "' .
                    $ex->getMessage() . '". Program stopped.';
                    return false;
                }
            }
        }


        // Call "Maker" plugin/class
        //

        $context = $contextFactory->getContext(); // Create a Context object

        $className = $contextFactory->getStreamsList()->getMakerClassName();
        $realClassName = Con::MAKERS_NAMESPACE . $className;
        $maker = new $realClassName($context);

        if(method_exists($maker, "init")){
            $maker->init($context);
        }

        $made = $maker->make(
            $context,
            $mixedObjects
        );

        $this->alerts = $context->getAlerts();
        $this->events = $context->getEvents();

        if($maker->getIsComplete()){
            $this->setIsComplete();
            return true;
        }
        else{
            $this->errorText = Texts\savingMakerError($className, $maker);
            return false;
        }

        unset($maker);

    }

}