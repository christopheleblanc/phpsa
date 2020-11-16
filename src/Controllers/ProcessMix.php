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
 * ProcessMix
 * Top level class used by the program to process/mix/aggregate data coming
 * from all streams using a plugin class of type "Mixer".
 *
 * A plugin of type "Mixer" is intended to process the parsed data according
 * to specific algorythm, defined in class method "mix()".
 * In most case, it will be used to aggregate parsed data while excluding
 * out-of-date data, or to favor one source/stream over another.
 * The plugin has also the ability to stop the process or to emit an alert
 * when an error is returned or an Exception is thrown.
 * Note that the program will use the class "BasicMixer" if no specific
 * class is defined by user.
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
 * - Create an instance of "Mixer"
 * - Call the member method "init()"
 * - Call the member method "mix()"
 * - Release memory
 *
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Controllers\AstractProcessEvents;
use PHPStreamsAggregator\Controllers\BasicMixer;

/**
 * ProcessMix
 */
class ProcessMix extends AstractProcessEvents{

    /** @var Array - Array containing mixed objects. */
    private $results;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->results = [];
        parent::__construct();
    }


    /**
	 * Mix/aggregate parsed objects from all streams to one array using plugin
     * class of type "Mixer".
     * @param &ContextFactory  - The context
     * @param &ParsedList      - An instance of ParsedList containing parsed objects.
	 */
	public function mix(&$contextFactory, &$parsedList)
    {

        $className = $contextFactory->getStreamsList()->getMixerClassName();

        $context = $contextFactory->getContext();

        // Mix parsed objects
        //

        // If a "Mixer" class name has been set in configuration file, and if this class has
        // been loaded, use this class...
        if($className !== null && $contextFactory->getPlugins()->mixerIsLoaded($className)){

            // Mix objects with plugin "Mixer"
            //

            $realClassname = Con::MIXERS_NAMESPACE . $className;
            $mixer = new $realClassname($context);

            if(method_exists($mixer, "init")){
                $mixer->init($context);
            }

            $this->results = $mixer->mix(
                $context,
                $parsedList
            );

            $this->alerts = $context->getAlerts();
            $this->events = $context->getEvents();

            $errStr = null;
            $mixComplete = false;
            if(!$mixer->getIsComplete()){
                $this->errorText = $mixer->getErrorText();
                return false;
            }
            else if(!isset($this->results) || !is_array($this->results)){
                $this->errorText = 'Function "' . $className . '::mix()" did not return an array.';
                return false;
            }
            else{
                $this->setIsComplete();
                return true;
            }

            unset($mixer);

        }
        else{

            // Mix objects with standard "Mixer", which simply create an array containing
            // all parsed entities that are up to date or late. Outdated entities are omitted.

            $mixer = new BasicMixer();
            $this->results = $mixer->mix(
                $context,
                $parsedList
            );
            unset($mixer);

            $this->setIsComplete();
            return true;

        }

        return true;

    }

    /**
     * Get the results, mixed objects.
     * @returns &Array[]|Object
     */
    public function &getResults()
    {
        return $this->results;
    }

}