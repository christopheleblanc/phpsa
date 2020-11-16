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
 * DemoMoviesMixer
 * Demonstration class of type "Mixer" used to aggregate several streams.
 * Used for demonstration program "demo1.xml".
 * The purpose of this class is to:
 * - Define the name of the movies (since some streams only define them
 *   by a number)
 * - Store viewer's notes of different sources
 * - Store a global viewer's notes (calculated from all sources notes)
 */

namespace PHPStreamsAggregator\Plugins\Mixers;

use PHPStreamsAggregator\AbstractMixer;

/**
 * DemoMoviesMixer
 */

class DemoMoviesMixer extends AbstractMixer{

    /** @var array - An associative array containing movies titles **/
    private $titles;

	/**
	 * Initialize
	 */
    public function init(&$context)
    {

        // In the current demo, some streams to analyze do not contain the title of the movies.
        // With help of the "parsers", all the movies are identifiable by their number.
        // As we would still like to have the names of the movies in the output file, we will
        // therefore use the movie numbers to find the corresponding name.

        // Create an associative array used to find the movie title by number
        $this->titles = [
            "1" => "Star Wars - Episode I - The Phantom Menace",
            "2" => "Star Wars - Episode II - Attack of the Clones",
            "3" => "Star Wars - Episode III - Revenge of the Sith",
            "4" => "Star Wars - Episode IV - A New Hope",
            "5" => "Star Wars - Episode V - The Empire Strikes Back",
            "6" => "Star Wars - Episode VI - Return of the Jedi",
            "7" => "Star Wars - Episode VII - The Force Awakens",
            "8" => "Star Wars - Episode VIII - The Last Jedi",
            "9" => "Star Wars - Episode IX - The Rise of Skywalker"
        ];

    }

    /**
     * Mix parsed entities
     * @param &Context     - The context
     * @param &ParsedList  - Instance of ParsedList containing all groups, streams and
     *                       parsed entities
     * @returns &Object[]  - An array containing mixed objects
     */
    public function &mix(&$context, &$list)
    {

        $mixed = [];

        // Loop through the entire ParsedList without any consideration of groups, streams
        // or state of update.
        foreach($list->getChildren() as $groupKey => $group){
            foreach($group->getChildren() as $streamKey => $stream){
                foreach($stream->getChildren() as $entity){

                    $number = $entity["number"];
                    $key = (string)$number;
                    if(!array_key_exists($number, $mixed)){

                        $mixed[$key] = $entity;

                        // Change "note" to "global_note"
                        $mixed[$key]["global_note"] = $entity["note"];
                        unset($mixed[$key]["note"]);

                        // Set the name of the movie
                        if(array_key_exists($key, $this->titles)){
                            $mixed[$key]["title"]  = $this->titles[$key];
                        }

                    }
                    else{

                        // Mix notes
                        $note = ($entity["note"] + $mixed[$key]["global_note"]) / 2;
                        $mixed[$key]["global_note"] = $note;

                    }

                    // Add each source note
                    if(!isset($mixed[$key]["notes"])){
                        $mixed[$key]["notes"] = [];
                    }
                    $mixed[$key]["notes"][$stream->getId()] = $entity["note"];

                }
            }
        }

        // Define the process as complete!
        $this->setIsComplete();

        return $mixed;

    }

}