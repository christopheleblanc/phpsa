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
 * ProcessParseStream
 * Class used by the program to parse data from a stream using a plugin class of
 * type "Parser".
 *
 * A plugin of type "Parser" is intended to process parse data according
 * to specific algorythm, defined in class method "parse()".
 * There are different types of "Parser", intended to parse different types of data
 * (XML, JSON, CSV etc...) using different build-in PHP functions
 * ("SimpleXML", "json_decode()" etc...). If you want to use your own functions to
 * read files or want to be able to read differents types in your parser, you better
 * use type "TXT" or "DATA".
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
 * - Create an instance of "Parser"
 * - Parse data
 * - Add parsed elements/objects in the output array.
 * - Release memory
 *
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\ParserTypes;
use PHPStreamsAggregator\Texts;
use PHPStreamsAggregator\Controllers\AstractProcessEvents;

/**
 * ProcessParseStream
 */
class ProcessParseStream extends AstractProcessEvents{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Parse a stream.
     * @param   ContextFactory    - The context
     * @param   string            - The parser class name
     * @param   &Stream           - An instance of Stream.
     * @param   &Object[]         - A reference of the array intended to store parsed entities.
     * @param   &Object[]         - A reference of the array intended to store names of streams which were not parsed.
     * @returns boolean           - True if the parsing process has been complete, or False.
     */
    public function parse(&$contextFactory, $parserClassname, &$downloadNode, &$parsedObjects, &$notParsedNodes)
    {

        $filePath = $contextFactory->getTempDirectory() . DIRECTORY_SEPARATOR .
        Con::TEMP_FILES_DIR_NAME . DIRECTORY_SEPARATOR . $contextFactory->getStreamsList()->getFileName() .
        DIRECTORY_SEPARATOR . $downloadNode->getId();

        if(file_exists($filePath)){

            $context = $contextFactory->getContext();

            $realClassname = Con::PARSERS_NAMESPACE . $parserClassname;
            $parser = new $realClassname($context);

            if(method_exists($parser, "init")){
                $parser->init($context);
            }

            // This program has the ability to use differents types of parsers.

            switch($parser::$type){

                case ParserTypes::DATA:{

                    $dataToParseLoaded = true;
                    $parser->parse($context, $filePath, $parsedObjects);

                }break;
                case ParserTypes::TXT:{

                    $content = file_get_contents($filePath);
                    if($content === null || $content === false){
                        $dataToParseLoaded = false;
                    }
                    else{
                        $dataToParseLoaded = true;
                        $parser->parse($context, $content, $parsedObjects);

                    }

                    unset($content);

                }break;
                case ParserTypes::SIMPLEXML:{

                    $simpleXml = @simplexml_load_file($filePath);
                    if($simpleXml === false){
                        $dataToParseLoaded = false;
                    }
                    else{
                        $dataToParseLoaded = true;
                        $parser->parse($context, $simpleXml, $parsedObjects);

                    }

                    unset($simpleXml);// Do not forget to unset the SIMPLEXML object to release memory

                }break;
                case ParserTypes::JSON:{

                    $json = @json_decode(file_get_contents($filePath));
                    if($json === null || $json === false){
                        $dataToParseLoaded = false;
                    }
                    else{
                        $dataToParseLoaded = true;
                        $parser->parse($context, $json, $parsedObjects);

                    }

                    unset($json);// Do not forget to unset the Json object to release memory

                }break;
                case ParserTypes::STRCSV:{

                    $content = file_get_contents($filePath);
                    if($content === null || $content === false){
                        $dataToParseLoaded = false;
                    }
                    else{
                        $dataToParseLoaded = true;
                        $parser->parse($context, $content, $parsedObjects);

                    }

                    unset($content);// Do not forget to unset the Json object to release memory

                }break;
                case ParserTypes::CSV:{

                    $dataToParseLoaded = true;
                    $parser->parse($context, $filePath, $parsedObjects);

                }break;

            }

            $this->alerts = $context->getAlerts();
            $this->events = $context->getEvents();

            $errStr = null;

            // Use the method "isComplete" of the parser to check if the parsing process was successful.
            if($parser->getIsComplete()){
                $this->setIsComplete();
            }
            else{

                $notParsedNodes[] = $downloadNode->getId();

                // Display an error but do not stop the program
                $this->errorText = Texts\parsingParserError($parserClassname);

            }

            // Dispatch event
            $arguments = [
                "done" => $parser->getIsComplete(),
                "stream" => $downloadNode
            ];
            if(!$parser->getIsComplete()){
                $arguments["error_text"] = $errStr;
            }
            $contextFactory->getPlugins()->dispatch($contextFactory->getContext(), "parse", "onParse", $arguments);

        }
        else{
            // Display an error but do not stop the program
            $this->errorText = Texts\errorFileDoesNotExists();
        }

        unset($parser);

        return $this->isComplete;

    }

}