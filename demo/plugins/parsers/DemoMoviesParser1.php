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
 * DemoMoviesParser1
 * Demonstration class of type "Parser" used to parse XML stream source 1 from 
 * demonstration program "demo1.xml".
 */

namespace PHPStreamsAggregator\Plugins\Parsers;

use PHPStreamsAggregator\AbstractParser;
use PHPStreamsAggregator\ParserTypes;

/**
 * DemoMoviesParser1
 */
class DemoMoviesParser1 extends AbstractParser{

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

        // In the current demo, some streams to analyze contains the title of the movies,
        // some other contain the number. Anyway, we want to be able to identify movies
        // by a unique identifier, be it name or number.
        // We choose to use the number, and we will retrieve the names of the movies
        // with the number in the "Mixer" class.

        // Create an associative array to convert movies names into movie numbers.
        $episodesNumber = [
            "Star Wars - Episode I - The Phantom Menace" => 1,
            "Star Wars - Episode II - Attack of the Clones" => 2,
            "Star Wars - Episode III - Revenge of the Sith" => 3,
            "Star Wars - Episode IV - A New Hope" => 4,
            "Star Wars - Episode V - The Empire Strikes Back" => 5,
            "Star Wars - Episode VI - Return of the Jedi" => 6,
            "Star Wars - Episode VII - The Force Awakens" => 7,
            "Star Wars - Episode VIII - The Last Jedi" => 8,
            "Star Wars - Episode IX - The Rise of Skywalker" => 9
        ];

        if(isset($simpleXML->movie)){

            foreach($simpleXML->movie as $movie){

                if(isset($movie->name) && isset($movie->year) && isset($movie->note)){

                    $movieTitle = (string)$movie->name;

                    if(array_key_exists($movieTitle, $episodesNumber)){

                        $number = $episodesNumber[$movieTitle];

                        // Add the entity to the array
                        $entities[] = [
                            "number" => $episodesNumber[$movieTitle],
                            "year" => intval($movie->year),
                            "note" => intval($movie->note)
                        ];

                    }

                }

            }

            // Define the process as complete
            $this->setIsComplete();

            // Note that we don't have to check here that all the values ​​we are looking for
            // have been found. This can be done in the "Mixer" class or  in the "Validator"
            // class. Obviously, nothing impeach to do a check right now and define the analysis 
            // as incomplete if the programmer wishes.

        }

        // Do nothing here...
        // The stream will be considered as not correct if no node "movie" was found.

	}

}
