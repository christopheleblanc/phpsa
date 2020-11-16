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
 * DemoMoviesParser4
 * Demonstration class of type "Parser" used to parse CSV file source 4 from
 * demonstration program "demo1.xml".
 */

namespace PHPStreamsAggregator\Plugins\Parsers;

use PHPStreamsAggregator\AbstractParser;
use PHPStreamsAggregator\ParserTypes;

/**
 * DemoMoviesParser4
 */
class DemoMoviesParser4 extends AbstractParser{

	/** 
	 * Type of this parser.
	 * Note: This variable is required for the parser to be recognized by the program.
	 * WARNING: THIS MUST BE STATIC.
     * @see PHPStreamsAggregator\ParserTypes
	 * @var integer Type of this parser.
	 */
	static public $type = ParserTypes::STRCSV;

	/**
	 * Parse CSV data from string.
	 * Note: This function is automatically called by the program.
     * @param &Context   - The context
	 * @param &Object    - The data containing the entities (JSON Object).
	 * @param &Object[]  - The array in which the parsed objects must be added.
	 */
	public function parse(&$context, &$content, &$entities)
    {

        $total = 0;

        // Read the CSV data line by line
        $lines = explode(PHP_EOL, $content);

        if(count($lines) == 0){

            // Stop the execution of the function and consider the file as not
            // correct if there is no line.

            // Additionally, we can return a custom error text by defining the
            // member variable "$errorText" before calling "return".
            $this->errorText = "CSV data is empty.";

            return;
 
        }

        foreach ($lines as $line) {

            // Get CSV data
            $data = str_getcsv($line, ";");

            // Check if the array for this line contains two values as expected.
            if(count($data) == 2){

                // Add the entity to the array
                $entities[] = [
                    "number" => intval($data[0]),
                    "note" => intval($data[1])
                ];

            }

        }

        // Define the process as complete
        $this->setIsComplete();

	}

}
