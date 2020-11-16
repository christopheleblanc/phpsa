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
 * DemoMoviesParser2
 * Demonstration class of type "Parser" used to parse XML stream source 2 from
 * demonstration program "demo1.xml".
 */

namespace PHPStreamsAggregator\Plugins\Parsers;

use PHPStreamsAggregator\AbstractParser;
use PHPStreamsAggregator\ParserTypes;

/**
 * DemoMoviesParser2
 */
class DemoMoviesParser2 extends AbstractParser{

	/**
	 * Type of this parser.
	 * Note: This variable is required for the parser to be recognized by the program.
	 * WARNING: THIS MUST BE STATIC.
     * @see PHPStreamsAggregator\ParserTypes
	 * @var integer Type of this parser.
	 */
	static public $type = ParserTypes::SIMPLEXML;

	/**
	 * Parse an instance of SimpleXMLElement.
	 * Note: This function is automatically called by the program.
     * @param &Context           - The context
	 * @param &SimpleXMLElement  - The data containing the entities (SimpleXMLElement).
	 * @param &Object[]          - The array in which the parsed objects must be added.
	 */
	public function parse(&$context, &$simpleXML, &$entities){

        if(isset($simpleXML->item)){

            foreach($simpleXML->item as $movie){

                if(isset($movie["ep"]) && isset($movie["year"]) && isset($movie["note"])){

                    // Add the entity to the array
                    $entities[] = [
                        "number" => intval($movie["ep"]),
                        "year" => intval($movie["year"]),
                        "note" => intval($movie["note"])
                    ];

                }

            }

            // Define the process as complete
            $this->setIsComplete();

        }

        // Do nothing here...
        // The stream will be considered as not correct if no node "item" was found.

	}

}
