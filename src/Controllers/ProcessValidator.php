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
 * ProcessValidator
 * Top level class used by the program to validate process or output file.
 *
 * A plugin of type "Validator" is intended to validate the entire process
 * according to specific function, defined in class method "validate()".
 * In most case, it will be used to check if a newly generated output file
 * is valid (contains the needed data, etc...).
 * "Validator" is totally optional and content checking can be done in
 * previous steps (Ex: in "Mixer" or "Maker").
 *
 * Data flow and plugins representation:
 *
 * Parse data     > Process/Mix data     > Make        > Validate output
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 * [PARSE] |
 * [PARSE] |----- > [MIX] -------------- > [MAKE]      > [VALIDATE]
 * [PARSE] |
 * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
 *
 * This class is responsible for the execution of the following process, while
 * reporting any errors/Exception:
 * - Create an instance of "Validator"
 * - Call the member method "init()"
 * - Call the member method "validate()"
 * - Release memory
 *
 * @see AbstractValidator
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\Controllers\AstractProcessEvents;

/**
 * ProcessValidator
 */
class ProcessValidator extends AstractProcessEvents{

    /** @var boolean Defines if the outputfile is valid or not. */
    private $isValidated;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isValidated = false;
        parent::__construct();
    }

    /**
	 * Check if the main output file is valid.
     * @param   Context  - The context
     * @param   string   - The class name
     * @param   string   - The output file path
	 */
    public function validate(&$contextFactory, $className, $outputFilepath)
    {

        $this->isValidated = false;

        $streamsState = $contextFactory->getStreamsState();
        $context = $contextFactory->getContext();

        $realClassname = Con::VALIDATORS_NAMESPACE . $className;
        $validator = new $realClassname($context, $outputFilepath);

        if(method_exists($validator, "init")){
            $validator->init($context);
        }

        $this->isValidated = $validator->validate($context);

        $this->alerts = $context->getAlerts();
        $this->events = $context->getEvents();

        if(!$this->isValidated){
            $this->errorText = $validator->getErrorText();
            return;
        }

        $streamsState->setLastValidationMissing($validator->getTotalMissings());

        $this->setIsComplete();

        unset($validator);

    }

    /**
	 * Check if the output file is valid.
     * @returns boolean
	 */
    public function getIsValidated()
    {
        return $this->isValidated;
    }

}