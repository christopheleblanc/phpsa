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
 * StateViewer demonstration script
 */

namespace PHPStreamsAggregator;

/** Require **/
require("src" . DIRECTORY_SEPARATOR . "autoload.php");

use PHPStreamsAggregator\StateViewer;
use PHPStreamsAggregator\Models\StreamStates;

/** Start viewer **/
$stateViewer = null;
try{
    $stateViewer = StateViewer::create();
}
catch(\Exception $ex){
    echo "Error: " . $ex->getMessage();
    die();
}


/** Example of use **/


// Retrieve some informations about the streams list file:
//

// State
$state = $stateViewer->getState();
$stateStr;
switch($state->getState()){
    case StreamStates::START:{
        $stateStr = "START";
    }break;
    case StreamStates::DOWNLOADED:{
        $stateStr = "DOWNLOADED";
    }break;
    case StreamStates::PARSED:{
        $stateStr = "PARSED";
    }break;
    case StreamStates::VALIDATED:{
        $stateStr = "VALIDATED";
    }break;
}

// Last validation time
$lastValidationTimeStr;
if($state->getLastValidationTime() > 0){
    $lastValidationTimeStr = date('Y-m-d \a\\t\ H:i:s', $state->getLastValidationTime());
}
else{
    $lastValidationTimeStr = "Unknow";
}

// Is up to date?
$upToDateStr = ($state->getIsUpToDate()) ? "TRUE" : "FALSE";


/** Display informations **/

echo "Up to date: " . $upToDateStr . nl2br(PHP_EOL);
echo "State: " . $stateStr . nl2br(PHP_EOL);
echo "Last validation time: " . $lastValidationTimeStr . nl2br(PHP_EOL);
