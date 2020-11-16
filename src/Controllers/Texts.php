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
 * Texts
 * Namespace containing several function to generate standard texts
 * used in many parts of the program.
 */

namespace PHPStreamsAggregator\Texts;

/**
 * Creates a text for a parsing error.
 * @param string[] The ids/names of the downloads which were not parsed.
 * @return string The text.
 */
function parseError(&$names){
	
	$text = "";
	$length = count($names);
	$last = $length - 1;
	for($i = 0; $i < $length; $i++){
		$text .= $names[$i];
		if($i < $last){
			$text .= ",";
		}
	}
	
	return "Parse error on " . $text . ".";
	
}

/**
 * Creates a text for an exception thrown when trying to download a file.
 * @param Exception The exception thrown when trying to download a file.
 * @param string The URL of the file which were not downloaded.
 * @return string The text.
 */
function downloadURLException(&$exception, $url){
	
	$cause = null;
				
	switch($exception->getMessage()){
		case "DOWNLOAD_ERROR_404":{
			$cause = "Error 404.";
		}break;
		case "DOWNLOAD_NO_LAST_MODIFIED":{
			$cause = "Parsing headers failed.";
		}break;
		case "DOWNLOAD_FAILED":{
			$cause = "Download failed.";
		}break;
		case "DOWNLOAD_FILE_SAVE_FAILED":{
			$cause = "File save has failed.";
		}break;
		default:{
			$cause = get_class($exception);
		}break;
	}
	
	return "Download failed on URL \"" . $url . "\". Cause: " . $cause;
	
}

/**
 * Creates a text for an exception thrown when trying to download a file.
 * @param Exception The exception thrown when trying to download a file.
 * @param string The URL of the file which were not downloaded.
 * @return string The text.
 */
function downloadFTPException(&$exception, $ftpAddress){
	
	$cause = null;
				
	switch($exception->getMessage()){
		case "FTP_CONNECTION_FAILED":{
			$cause = "Connection failed.";
		}break;
		case "FTP_LOGIN_ERROR":{
			$cause = "Login error.";
		}break;
		case "FTP_SCAN_ERROR":{
			$cause = "Directory scan failed.";
		}break;
		case "FTP_SCAN_FILE_NOT_FOUND":{
			$cause = "File to download not found when scanning the directory.";
		}break;
		case "FILE_NO_NAME":{
			$cause = "File to download has no name.";
		}break;
		case "DOWNLOAD_FAILED":{
			$cause = "Download failed.";
		}break;
		case "FILE_NOT_FOUND":{
			$cause = "File not found.";
		}break;
		default:{
			$cause = get_class($exception);
		}break;
	}
	
	return "Download failed on FTP \"" . $ftpAddress . "\". Cause: " . $cause;
	
}

/**
 * Creates a text for an exception thrown when trying to download a local file.
 * @param Exception The exception thrown when trying to download a file.
 * @param string The absolute path of the file which were not downloaded.
 * @return string The text.
 */
function downloadPathException(&$exception, $path){
	
	$cause = null;
				
	switch($exception->getMessage()){
		case "DOWNLOAD_FAILED":{
			$cause = "File does not exists.";
		}break;
		case "DOWNLOAD_FILE_SAVE_FAILED":{
			$cause = "File save has failed.";
		}break;
		default:{
			$cause = get_class($exception);
		}break;
	}
	
	return "Download failed on local Path \"" . $path . "\". Cause: " . $cause;
	
}

/**
 * Creates a text for a file deletion error.
 * @param string The path of the file which was not deleted.
 * @return string The text.
 */
function unlinkError(&$filePath){
	return "Temporary file \"" . $filePath . "\" could not be deleted.";
}

/**
 * Creates a text for an error occurred when including the class file.
 * @param string The name of the class.
 * @return string The text.
 */
function loadPluginIncludeFailed(&$className){
	return "Error while loading plugin \"" . $className . "\". Function \"require_once()\" has failed.";
}

/**
 * Creates a text for an error occurred when testing the existence of a plugin file.
 * @param string The name of the class.
 * @return string The text.
 */
function loadPluginFileNotExists(&$className, $filePath){
	return "Error while loading plugin \"" . $className . "\". The file \"" . $filePath . "\" does not exists.";
}

/**
 * Creates a text for an error occurred when testing the existence of the static variable "$type" for a plugin of type "Parser".
 * @param string The name of the class.
 * @return string The text.
 */
function loadParserUntyped(&$className)
{
    return "Error while loading plugin \"" . $className . "\". The class does not contain a valid static variable \"\$type\".";
}

/**
 * Creates a text for an error occurred when trying to include a parse class file.
 * @param string The name of the file which was not included.
 * @param string The path of the file which was not included.
 * @return string The text.
 */
function includePluginError(&$className){
	return "Unknow error while loading \"" . $className . "\".";
}

/**
 * Creates a text for an error occurred when parsing.
 * @param string The parser class name.
 * @return string The text.
 */
function parsingParserError(&$className){
	return "Error while parsing with Parser \"" . $className . "\". The process seems to be incomplete.";
}

/**
 * Creates a text for an error occurred when no files has been parsed.
 * @param string The parser class name.
 * @return string The text.
 */
function parseZeroError(){
	return "No files to parse.";
}

/**
 * Creates a text for an error occurred when saving file.
 * @param string The maker class name.
 * @param AbstractMaker The instance of Maker class.
 * @return string The text.
 */
function savingMakerError(&$className, &$maker){
    
    $mktxt;
    if($maker->getErrorText() !== null){
        $mktxt = 'Error text: "' . $maker->getErrorText() . '"';
    }
    else{
        $mktxt = "No error text returned...";
    }
    
	return "Error while saving output file with class " . $className . ". " . $mktxt;
}

/* 
 * Get a "No error" message.
 * @return string The text.
 */
function errorNoError()
{
    return "No errors";
}

/* 
 * Get an error message when a File does not exists.
 * @return string The text.
 */
function errorFileDoesNotExists()
{
    return "File does not exists.";
}

/* 
 * Get an error message when a File does not exists.
 * @return string The text.
 */
function errorDirRootOnly($optionName)
{
    return 'Uncorrect directory for option "' . $optionName . '".';
}

/* 
 * Get an error message when a Xml reading has returned an error.
 * @return string The text.
 */
function errorXml()
{
    return "XML error.";
}

/* 
 * Get an error message when checking the validity of a value.
 * @return string The text.
 */
function errorUncorrectValue($value)
{
    return 'Uncorrect value "' . $value . '".';
}

/* 
 * Get an error message when parsing a XML node detect missing FTP
 * connexion values.
 * @param string - Login
 * @param string - Password
 * @return string The text.
 */
function errorXmlMissingFTPValues(&$login, &$password)
{
    $errs = 0;
    $values = "";
    if($login === null){
        $values .= '"ftp_login"';
        $errs++;
    }
    if($password === null){
        if($errs > 0){
            $values .= ", ";
        }
        $values .= '"ftp_password"';
        $errs++;
    }
    $valueStr = ($errs > 1) ? 'values ' . $values . ' are missing.' : 'value ' . $values . ' is missing.';
    return 'FTP Connexion ' . $valueStr;
}

/* 
 * Get an error message when checking the validity of a value.
 * @return string The text.
 */
function errorUnknow()
{
    return 'Unknow.';
}

/* 
 * Get an error message when checking the validity of a value.
 * @return string The text.
 */
function errorPluginClassNameBackslashes($value)
{
    return 'Value "' . $value . '" must not contain characters "\". Reminder: The value must be the class name, without the class namespace.';
}

/* 
 * Convert memory usage to human readable text.
 * @param Number - The amount of memory to convert (in bytes).
 * @return String - The number, in human readable format.
 */
function convertMemoryUsage($size)
{
    $unit = array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
