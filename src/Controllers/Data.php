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
 * Data
 * Class responsible of storing, checking and modifying program data (files and 
 * directories). This class must be instanciated as a Singleton with the function 
 * "instanciate()", since it stores several critical static values (mostly program
 * absolute paths).
 */

namespace PHPStreamsAggregator;

use PHPStreamsAggregator\Constants as Con;

/**
 * Data
 */
class Data{

    /** @var string - Absolute path of the program directory **/
    static $ABSOLUTE_PATH = null;

    /** @var string - Absolute path of the program temporary data directory **/
    static $TEMP_ABSOLUTE_PATH = null;

        /** @var string - Absolute path of the program logs directory **/
    static $LOGS_ABSOLUTE_PATH = null;

    /**
     * Remove a directory and its content recursively.
     * @param    string   - The directory to delete.
     * @param    boolean  - Delete folder too.
     * @param    boolean  - Verbose mode on (display details).
     * @returns  integer  - The total number of files deleted.
     */
    static public function recursiveRemoveDirectory($directory, $deleteFolder, $verbose = false)
    {
        $count = 0;
        foreach(glob($directory . DIRECTORY_SEPARATOR . "*") as $file)
        {

            if(is_dir($file)) { 
                $count += self::recursiveRemoveDirectory($file, true, $verbose);
            } else {

                if($verbose){
                    echo "Delete file " . $file . " > ";
                }

                $unlinked = unlink($file);
                if($unlinked){
                    $count++;
                }

                if($verbose){
                    $done = ($unlinked) ? "Done!" : "Failed.";
                    echo $done . PHP_EOL;
                }

            }
        }

        if($deleteFolder){
            
            if($verbose){
                echo "Delete dir  " . $file . " > ";
            }

            $rmdired = rmdir($directory);

            if($verbose){
                $done = ($rmdired) ? "Done!" : "Failed.";
                echo $done . PHP_EOL;
            }

        }

        return $count;

    }

    /**
     * Clear all program's temporary data.
     * @param &ContextFactory - The context
     */
    static public function clearAllTmp(&$context)
    {

        $verbose = $context->getOptions()->getIsVerbose();
        $tempDir = $context->getTempDirectory();

        if($verbose){
            echo "Clear temporary data:" . PHP_EOL;
        }
        else{
            echo "Clear temporary data... ";
        }

        $tempDirFiles = $tempDir . DIRECTORY_SEPARATOR . Con::TEMP_FILES_DIR_NAME;

        if(file_exists($tempDirFiles) && is_dir($tempDirFiles)){
            self::recursiveRemoveDirectory($tempDirFiles, false, $verbose);
        }

        $stateDirState = $tempDir . DIRECTORY_SEPARATOR . Con::TEMP_STATE_DIR_NAME;

        if(file_exists($stateDirState) && is_dir($stateDirState)){
            self::recursiveRemoveDirectory($stateDirState, false, $verbose);
        }

        if(!$verbose){
            echo "Done!" . PHP_EOL;
        }
        else{
            echo "Clear finished..." . PHP_EOL;
        }

    }

    /**
	 * Delete all temporary downloaded files from an instance of StreamsList.
     * @param   &ContextFactory  - The context
	 * @returns integer          - The number of deleted files.
	 */
    static public function clearAllDownloadListTemp(&$context)
    {

        $verbose = $context->getOptions()->getIsVerbose();
        $tempDir = $context->getTempDirectory();
        $streamsList = $context->getStreamsList();
        $streamsState = $context->getStreamsState();

        $count = 0;

        if($verbose){
            echo "Clear temporary data:" . PHP_EOL;
        }
        else{
            echo "Clear temporary data... ";
        }

        if($streamsList->size() > 0){

            $tempDirFilesFile = $tempDir . DIRECTORY_SEPARATOR .
            Con::TEMP_FILES_DIR_NAME . DIRECTORY_SEPARATOR . $streamsList->getFileName();

            if(file_exists($tempDirFilesFile) && is_dir($tempDirFilesFile)){
                $count += self::recursiveRemoveDirectory($tempDirFilesFile, true, $verbose);
            }

        }
        $streamsState->deleteFile();

        if(!$verbose){
            echo "Done!" . PHP_EOL;
        }
        else{
            echo "Clear finished..." . PHP_EOL;
        }

        return $count;

    }

	/**
	 * Delete all temporary downloaded files from an instance of StreamsList.
     * @param    &ContextFactory  - The context
	 * @param    string           - Original/source path
     * @param    string           - New/target path
     * @returns  boolean          - True in case of success, or False.
	 */
	static public function transfertStream(&$context, &$from, &$to)
    {
        return rename($from, $to);
	}

	/**
	 * Delete all temporary downloaded files from a StreamsList.
     * @param   &ContextFactory  - The context
	 * @return  integer          - The number of deleted files.
	 */
	static public function deleteDownloadListTempFiles(&$context)
    {
		$count = 0;

        $tempDirFilesFile = $context->getTempDirectory() . DIRECTORY_SEPARATOR .
        Con::TEMP_FILES_DIR_NAME . DIRECTORY_SEPARATOR . $context->getStreamsList()->getFileName();

        if(file_exists($tempDirFilesFile) && is_dir($tempDirFilesFile)){
            $count += self::recursiveRemoveDirectory($tempDirFilesFile, false, $context->getOptions()->getIsVerbose());
        }

		return $count;

	}

    /**
     * Create unexisting directories.
     * @param    string   - An absolute path.
     * @returns  boolean  - True in case of success, or False.
     */
    static public function makeDirectories(&$filePath)
    {

        $absolutePath = "";

        if(strpos($filePath, DIRECTORY_SEPARATOR) === 0){
            $absolutePath .= DIRECTORY_SEPARATOR;
        }

        $pathParts = explode(DIRECTORY_SEPARATOR, $filePath);
        $totalParts = count($pathParts);
        $lastPartIdx = $totalParts - 1;
        for($i = 0; $i < $totalParts; $i++){
            $absolutePath .= $pathParts[$i];

            if(strlen($pathParts[$i]) > 0){
                if(!file_exists($absolutePath) || !is_dir($absolutePath)){
                    try{
                        if(!mkdir($absolutePath)){
                            return false;
                        }
                    }
                    catch(\Exception $ex){
                        throw $ex;
                    }
                }

                if($i < $lastPartIdx){
                    $absolutePath .= DIRECTORY_SEPARATOR;
                }
            }
        }
        return true;

    }

    /**
     * Get the absolute path of the program
     * @returns string
     */
    static public function absolutePath()
    {
        return self::$ABSOLUTE_PATH;
    }

    /**
     * Get the absolute path of the program temporary data
     * @returns string
     */
    static public function tempAbsolutePath()
    {
        return self::$TEMP_ABSOLUTE_PATH;
    }

    /**
     * Instanciate the singleton of this class.
     */
    static public function instanciate()
    {

        // Define absolute path
        //
        if(self::$ABSOLUTE_PATH === null){
            self::$ABSOLUTE_PATH = dirname(dirname(__DIR__));
        }


        // Define temp absolute path
        //

        if(self::$TEMP_ABSOLUTE_PATH === null){
            $tempDirDefined = false;
            if(Con::TEMP_ABSOLUTE_PATH !== null){
                $tmp = trim(Con::TEMP_ABSOLUTE_PATH);
                $len = strlen($tmp);
                if($len > 0){
                    $slashpos = strpos($tmp, DIRECTORY_SEPARATOR);
                    $drivepos = strpos($tmp, ":".DIRECTORY_SEPARATOR);
                    if($slashpos === 0 || $drivepos === 1){
                        // Path is absolute
                        self::$TEMP_ABSOLUTE_PATH = $tmp;
                    }
                    else{
                        // Path is relative
                        self::$TEMP_ABSOLUTE_PATH = self::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . $tmp;
                    }
                    $tempDirDefined = true;
                }
            }

            if(!$tempDirDefined){
                self::$TEMP_ABSOLUTE_PATH = self::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::DATA_DIR_NAME .
                DIRECTORY_SEPARATOR . Con::TEMP_DIR_NAME;
            }
        }


        // Define logs absolute path
        //

        if(self::$LOGS_ABSOLUTE_PATH === null){
            $logsDirDefined = false;
            if(Con::LOGS_ABSOLUTE_PATH !== null){
                $tmp = trim(Con::LOGS_ABSOLUTE_PATH);
                $len = strlen($tmp);
                if($len > 0){
                    $slashpos = strpos($tmp, DIRECTORY_SEPARATOR);
                    $drivepos = strpos($tmp, ":".DIRECTORY_SEPARATOR);
                    if($slashpos === 0 || $drivepos === 1){
                        // Path is absolute
                        self::$LOGS_ABSOLUTE_PATH = $tmp;
                    }
                    else{
                        // Path is relative
                        self::$LOGS_ABSOLUTE_PATH = self::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . $tmp;
                    }
                    $logsDirDefined = true;
                }
            }

            if(!$logsDirDefined){
                self::$LOGS_ABSOLUTE_PATH = self::$ABSOLUTE_PATH . DIRECTORY_SEPARATOR . Con::DATA_DIR_NAME .
                DIRECTORY_SEPARATOR . Con::LOG_DIR_NAME;
            }
        }

    }

}