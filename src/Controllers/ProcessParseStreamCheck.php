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
 * ProcessParseStreamCheck
 * Class intended to check a parsing process without searching any parsed objects.
 * This is used as a protection layer to check if a new stream data is valid and
 * if the "Parser" will be able to parse it.
 *
 * This class is responsible for the execution of the following process, while
 * reporting any errors/Exception:
 * - Create an instance of "Parser"
 * - Call the member method "init()"
 * - Call the member method "parse()"
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\ParserTypes;

/**
 * ProcessParseStreamCheck
 */
class ProcessParseStreamCheck{

	/** @var boolean The two arrays of nodes are equal. */
	private $errorText;

	/**
	 * Constructor
	 */
	public function __construct()
    {
		$this->errorText = null;
	}

	/**
	 * Check if a "Parser" will complete its process on a given file.
     * This can be used to check if a "Parser" or if the file are corrects.
     * @param    &ContextFactory  - The context
     * @param    &Stream          - The stream nodes
     * @param    string           - The file path
     * @returns  boolean          - True if the parsing test is complete, or False.
	 */
    public function check(&$contextFactory, &$downloadNode, $filePath)
    {
        $parsable = true;

        $parserClassname = $downloadNode->getParserName();

        $context = $contextFactory->getContext();

        if($contextFactory->getPlugins()->parserIsLoaded($parserClassname)){

            $realClassname = Con::PARSERS_NAMESPACE . $parserClassname;
            $parser = new $realClassname($context);

            if(method_exists($parser, "init")){
                $parser->init($context);
            }

            $parsedObjects = []; // fake

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

            // Use the method "isComplete" of the parser to check if the parsing process was successful.
            if($parser->getIsComplete()){
                return true;
            }
            else{
                $this->errorText = $parser->getErrorText();
                return false;
            }

        }
        else{
            return false;
        }

    }

	/**
	 * Get the error text.
	 *
	 * @return string True if the two lists are equal, or False.
	 */
	public function getErrorText()
    {
		return $this->errorText;
	}

}