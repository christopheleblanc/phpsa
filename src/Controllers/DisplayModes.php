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
 * DisplayModes
 * Class storing several functions used to display informations on screen when
 * the user is running special modes ("infos", "help", "test").
 */

namespace PHPStreamsAggregator;

use PHPStreamsAggregator\Constants as Con;

/**
 * DisplayModes
 */
class DisplayModes{

    /**
     * Display program informations
     */
    static public function displayInfos()
    {
        echo "Program informations:" . PHP_EOL;
        echo "Program name : " . Con::APP_NAME . PHP_EOL;
        echo "Version      : " . Con::APP_VERSION . PHP_EOL;
        echo "Release date : " . Con::APP_RELEASE_DATE . PHP_EOL;
        echo "Author       : " . "Christophe Leblanc" . PHP_EOL;
        echo "Website      : " . Con::APP_WEB_SITE . PHP_EOL;
        echo "License      : " . "GNU General Public License version 3" . PHP_EOL;
    }

    /**
     * Display help
     */
    static public function displayHelp()
    {

        echo "Program help:" . PHP_EOL;

        echo PHP_EOL;

        echo "Command line parameters:" . PHP_EOL;
        echo "-c --clear   : " . "Clear temporary data." . PHP_EOL;
        echo "               " . "( Note that if the \"list\" parameter is defined, only temporary" . PHP_EOL;
        echo "               " . "files used by this list will be deleted. Otherwise, all" . PHP_EOL;
        echo "               " . "temporary files will be deleted. )" . PHP_EOL;
        echo "-h --help    : " . "Display help." . PHP_EOL;
        echo "-i --infos   : " . "Display informations about the app." . PHP_EOL;
        echo "-l --list    : " . "Define a streams list file path/name." . PHP_EOL;
        echo "               " . "( Can be an absolute or relative path, or the name of a file" . PHP_EOL;
        echo "               " . "placed in the configs folder. )" . PHP_EOL;
        echo "-t --test    : " . "Test the validity of the streams list." . PHP_EOL;
        echo "-u --update  : " . "Force update." . PHP_EOL;
        echo "-v --verbose : " . "Display detailed output ( for diagnostic purposes )." . PHP_EOL;

        echo PHP_EOL;

        echo "To learn more, visit the official documentation at:" . PHP_EOL;
        echo Con::APP_WEB_DOCUMENTATION . PHP_EOL;

    }

    /**
     * Display the results of "test" mode.
     * @param &TestReport - An instance of TestReport.
     */
    static public function displayTestResults(&$testReport)
    {
        echo 'Test streams list file... ';
        if($testReport->getStreamsListResult()){
            echo 'OK!' . PHP_EOL;
        }
        else{
            echo 'ERROR:' . PHP_EOL;
            echo '- ' . $testReport->getStreamsListError() . PHP_EOL;
        }
        echo 'Test plugins... ';
        if($testReport->getPluginsResult()){
            echo 'OK!' . PHP_EOL;
        }
        else{
            echo 'ERROR:' . PHP_EOL;
            echo '- ' . $testReport->getPluginsError() . PHP_EOL;
        }
        if($testReport->isValidated()){
            echo 'Program is ready to process with this streams list!' . PHP_EOL;
        }
        else{
            echo 'Program is not ready to process with this streams list.' . PHP_EOL;
        }
    }
}