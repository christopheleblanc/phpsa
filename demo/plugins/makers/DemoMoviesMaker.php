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
 * DemoMoviesMaker
 * Demonstration class of type "Maker" used to store parsed entities into a XML file.
 * Used for demonstration program "demo1.xml".
 */

namespace PHPStreamsAggregator\Plugins\Makers;

use PHPStreamsAggregator\AbstractMaker;

/**
 * DemoMoviesMaker
 */
class DemoMoviesMaker extends AbstractMaker{

	/*
	* Save output file
    * @param &Context - The context
    * @param &Array   - Array containing parsed / mixed entities.
	*/
	public function make(&$context, &$array)
    {

        if($context->getOutputFilePath() !== null){

            // Create a dom document with encoding utf8
            $domtree = new \DOMDocument('1.0', 'UTF-8');

            // Create the root element of the xml tree
            $xmlRoot = $domtree->createElement("xml");

            // Append it to the document created
            $xmlRoot = $domtree->appendChild($xmlRoot);

            // Add the creation timestamp in the root node attributes
            $xmlRoot->setAttribute("date", time());

            foreach($array as $movie){

                // Create a XML Node
                $node = $domtree->createElement("movie");
                $node = $xmlRoot->appendChild($node);
                $node->setAttribute("ep", $movie["number"]);
                $node->setAttribute("title", $movie["title"]);
                $node->setAttribute("global_note", $movie["global_note"]);

                // Add sources notes
                $notes = $domtree->createElement("notes");
                $notes = $node->appendChild($notes);

                foreach($movie["notes"] as $noteKey => $noteVal){
                    $note = $domtree->createElement("note");
                    $note = $notes->appendChild($note);
                    $note->setAttribute("src", $noteKey);
                    $note->setAttribute("note", $noteVal);
                }

            }

            // Optional
            $domtree->preserveWhiteSpace = false;
            $domtree->formatOutput = true;

            // Now save the file
            $saved = @file_put_contents($context->getOutputFilePath(), $domtree->saveXML());
            if($saved === false){
                // Return a custom error text
                $this->errorText = "Output file can not be saved.";
                return;
            }

            // Declare the process as complete!
            $this->isComplete = true;

        }

	}

}
