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
 * Autoload file.
 */

/** Constants **/
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Constants.php');

/** Controllers **/
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'AstractProcess.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'AstractProcessEvents.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ConfigLoader.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ContextFactory.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Data.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'DataChecker.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'DirectoryParser.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'DisplayModes.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ErrorLogParser.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'FileDownload.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'FileDownloadURL.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'FileDownloadFTP.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'FileDownloadPath.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'FileStringParser.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'LogManager.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'LogManagerCompletion.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'LogManagerError.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'MaximumDelayParser.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'MultiClassNamesParser.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Options.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'OutputFileFactory.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'PHPStreamsAggregator.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessInitRunners.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessLoadProgramData.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessLoadStreamsListData.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessUpdate.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessGetGroupsToUpdate.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessMaker.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessMix.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessParseAll.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessParseStream.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessValidator.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'StateViewer.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'ProcessParseStreamCheck.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'StreamsListLoader.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'StreamsListStateComparator.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'StreamsListStateDAO.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Texts.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Update.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'UpdateOptionsParser.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'AbstractMaker.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'AbstractMixer.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'AbstractParser.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'AbstractRunner.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'AbstractValidator.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'ParserTypes.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'PluginsManager.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . 'Plugins' . DIRECTORY_SEPARATOR . 'BasicMixer.php');

/** Library **/
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Library' .     DIRECTORY_SEPARATOR . 'Files.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Library' .     DIRECTORY_SEPARATOR . 'Text.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Library' .     DIRECTORY_SEPARATOR . 'Xml.php');

/** Models **/
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'Config.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'Context.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'DateTimeNumeric.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'ErrorLogError.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'FileData.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'OutputFile.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'ParsedGroup.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'ParsedList.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'ParsedStream.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'Stream.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamsGroup.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamsList.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamFTP.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamURL.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamPath.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamTypes.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamsListCompleteState.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamsListState.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamsGroupState.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamState.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'StreamStates.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'TestReport.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'UpdateOption.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'UpdateOptionEach.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'UpdateOptionEvery.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'UpdateOptionEveryTimeTypes.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'UpdateOptionHour.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'UpdateOptionTypes.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Models' .      DIRECTORY_SEPARATOR . 'UpdatingGroup.php');