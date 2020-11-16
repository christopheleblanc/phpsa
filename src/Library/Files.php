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
 * Namespace containing standard functions to manipulate Files.
 */

namespace PHPStreamsAggregator\Library\Files;

/**
 * Count the number of lines in a text file.
 * @param    string   - The file path
 * @returns  integer  - The number of lines
 */
function countNumberOfLines($filePath)
{

    $linecount = 0;
    $handle = fopen($filePath, "r");
    while(!feof($handle)){
      $line = fgets($handle);
      $linecount++;
    }

    fclose($handle);

    return $linecount;

}

/**
 * Get a specific line of a text file.
 * @param    string          - The file path
 * @param    integer         - The number of the line
 * @returns  string|boolean  - The text at the specified line, or False.
 */
function getLine($filePath, $lineNumber)
{
    $file = new \SplFileObject($filePath);
    if (!$file->eof()) {
         $file->seek($lineNumber);
         return $file->current(); // $contents would hold the data from line x
    }
    return false;
}

/**
 * Get several lines from a text file, from $startline to $end.
 * @param    string    - The file path
 * @param    integer   - The number of the first line
 * @param    integer   - The number of the last line
 * @returns  string[]  - An array containing the texts at the specified lines.
 */
function &getLines($filePath, $start, $end)
{

    $lines = [];

    $linecount = 0;
    $handle = fopen($filePath, "r");
    while(!feof($handle)){

      $line = fgets($handle);

      if($linecount >= $start && $linecount < $end){
          array_push($lines, $line);
      }

      $linecount++;

    }

    fclose($handle);

    return $lines;

}