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
 * Constants
 * Class used to store program constants.
 */

namespace PHPStreamsAggregator;

/**
 * Constants
 */
class Constants{


    /*
     * General configuration
     */


    /**
     * The path to the temp directory of the program. Can be relative or absolute.
     * If path is relative, the path will be defined as relative to the program
     * directory. If the value is NULL, default value will be used, which is
     * "[Program directory]\data\tmp".
     * NOTE: You can also set the temporary directory in the config file. 
     * Read the documentation for more informations...
     * @const string|null
     */
    public const TEMP_ABSOLUTE_PATH = null;

    /**
     * The path to the logs directory of the program. Can be relative or absolute.
     * If path is relative, the path will be defined as relative to the program
     * directory. If the value is NULL, default value will be used, which is
     * "[Program directory]\data\logs".
     * @const string|null
     */
    public const LOGS_ABSOLUTE_PATH = null;


    /*
     * Program directories names
     */


    /** @const string The name of the data directory. */
    public const DATA_DIR_NAME = "data";

    /** @const string The name of the directory containing temporary directories. */
    public const TEMP_DIR_NAME = "tmp";

    /** @const string The name of the directory containing temporary downloaded files. */
    public const TEMP_FILES_DIR_NAME = "files";

    /** @const string The name of the directory containing temporary downloaded responses. */
    public const TEMP_FILES_RES_DIR_NAME = ".tmp";

    /** @const string The name of the directory containing temporary downloaded files. */
    public const TEMP_OUT_DIR_NAME = "out";

    /** @const string The name of the directory containing temporary state files. */
    public const TEMP_STATE_DIR_NAME = "state";

    /** @const string The name of the directory containing configuration file. */
    public const CONFIG_DIR_NAME = "config";

    /** @const string The name of the directory containing the log files. */
    public const LOG_DIR_NAME = "logs";

    /** @const string The name of the directory in which the output file must being saved. */
    public const OUTPUT_DIR_NAME = "output";

    /** @const string The name of the directory containing the plugins directories. */
    public const PLUGINS_DIR_NAME = "plugins";

    /** @const string The name of the directory containing the plugins of type "Parser". */
    public const PARSERS_DIR_NAME = "parsers";

    /** @const string The name of the directory containing the plugins of type "Maker". */
    public const MAKERS_DIR_NAME = "makers";

    /** @const string The name of the directory containing the plugins of type "Validator". */
    public const VALIDATORS_DIR_NAME = "validators";

    /** @const string The name of the directory containing the plugins of type "Mixer". */
    public const MIXERS_DIR_NAME = "mixers";

    /** @const string The name of the directory containing the plugins of type "Runner". */
    public const RUNNERS_DIR_NAME = "run";


    /*
     * Files names
     */


    /** @const string The name of the error logs file. */
    public const ERRORLOG_FILENAME = "errors.log";

    /** @const string The name of the completion logs file. */
    public const COMPLETIONLOG_FILENAME = "complete.log";

    /** @const string The name of the program configuration file. */
    public const CONFIG_FILENAME = "config.xml";


    /*
     * Plugin namespaces
     * Mainly used to allow the user to define the use of a plugin in the configuration files without
     * having to write the complete namespace, and to allow the program to instantiate the plugin class
     * with the corresponding namespace.
     */


    /** @const string The namespace of plugin classes of type "Validator" (terminated by a backslash). */
    public const VALIDATORS_NAMESPACE = "PHPStreamsAggregator\Plugins\Validators\\";

    /** @const string The namespace of plugin classes of type "Parser" (terminated by a backslash). */
    public const PARSERS_NAMESPACE = "PHPStreamsAggregator\Plugins\Parsers\\";

    /** @const string The namespace of plugin classes of type "Mixer" (terminated by a backslash). */
    public const MIXERS_NAMESPACE = "PHPStreamsAggregator\Plugins\Mixers\\";

    /** @const string The namespace of plugin classes of type "Maker" (terminated by a backslash). */
    public const MAKERS_NAMESPACE = "PHPStreamsAggregator\Plugins\Makers\\";

    /** @const string The namespace of plugin classes of type "Maker" (terminated by a backslash). */
    public const RUNNERS_NAMESPACE = "PHPStreamsAggregator\Plugins\Runners\\";


    /*
     * Default values
     */


    /** @const string The directory of the data (with a slash at the end "/"). */
    public const AUTO_LIST_ID = "auto";

    /** @const string The extension used for output file (without point "."). */
    public const OUTPUT_FILE_EXTENSION = "xml";


    /*
     * Debug
     */


    /** @const boolean Display debug messages. */
    public const TOSTRING_LVL_STR = "    ";


    /*
     * Program infos
     */


    /**
     * Application name
     * @const string
     */
    public const APP_NAME = "PHP Streams Aggregator";

    /**
     * Application version number (Number only, without "version" or "v").
     * @const string
     */
    public const APP_VERSION = "1.0.0";

    /**
     * Application current release date (ISO 8601)
     * https://www.w3.org/TR/NOTE-datetime
     * @const string
     */
    public const APP_RELEASE_DATE = "2020-11-03";

    /**
     * Application author name
     * https://www.w3.org/TR/NOTE-datetime
     * @const string
     */
    public const APP_COPYRIGHT_DATE = "2018-2020";

    /**
     * Application author name
     * https://www.w3.org/TR/NOTE-datetime
     * @const string
     */
    public const APP_AUTHOR_NAME = "Christophe Leblanc";

    /**
     * Application web site
     * @const string
     */
    public const APP_WEB_SITE = "http://github.com";

    /**
     * Application web documentation
     * @const string
     */
    public const APP_WEB_DOCUMENTATION = "http://github.com";

}