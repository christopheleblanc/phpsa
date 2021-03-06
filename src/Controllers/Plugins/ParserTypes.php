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
 * ParserTypes
 * Class used to enumerate and check types of parsers.
 */

namespace PHPStreamsAggregator;

/**
 * ParserTypes
 */
class ParserTypes{

    /**
     * Enumeration of types.
     */
	const DATA = 0;
	const TXT = 1;
	const SIMPLEXML = 2;
    const JSON = 3;
    const STRCSV = 4;
    const CSV = 5;

    /**
     * Associative array defining available/correct types.
     * @var Array
     */
    static private $availableTypes = null;

    /**
     * Create an array of available, valid types.
     */
    static private function createAvailableTypes()
    {
        self::$availableTypes = [
            self::DATA,
            self::TXT,
            self::SIMPLEXML,
            self::JSON,
            self::STRCSV,
            self::CSV
        ];
    }

    /**
     * Check if a type is valid
     * @param integer $type - The type
     * @returns boolean - True if the type is valid, or False.
     */
    static public function isValid($type)
    {
        if(self::$availableTypes === null){
            self::createAvailableTypes();
        }
        return in_array($type, self::$availableTypes);
    }

}