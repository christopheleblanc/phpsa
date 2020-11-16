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
 * ProcessInitRunners
 * Top level class used to process the initialization of the plugins/classes of type
 * "Runner".
 */

namespace PHPStreamsAggregator\Controllers;

use PHPStreamsAggregator\Constants as Con;
use PHPStreamsAggregator\ParserTypes;
use PHPStreamsAggregator\Controllers\PluginsManager;

/**
 * ProcessInitRunners
 */
class ProcessInitRunners extends AstractProcessEvents{

	/**
	 * Initialize plugins/classes of type "Runner"
     * @param   &ContextFactory  - The context
     * @param   &PluginsManager  - The plugins
     * @return  boolean          - True in case of success, or false.
	 */
    public function init(&$context, &$plugins)
    {
        foreach($plugins->getRunnersClassNames() as $className){
            $realClassname = Con::RUNNERS_NAMESPACE . $className;
            try{
                $this->initSingle($context, $plugins, $className, $realClassname);
            }
            catch(\Exception $ex){
                $str = 'An Exception has been thrown during the instanciation of class "' . $realClassname . '".';
                if(strlen($ex->getMessage()) > 0){
                    $str = ' Message: ' . $ex->getMessage();
                }
                else{
                    $str = ' No message...';
                }
                $this->errorText = $str;
                return false;
            }
        }
        $this->setIsComplete();
        return true;
    }

	/**
	 * Initialize a single plugin/class of type "Runner"
     * @param   &ContextFactory  - The context
     * @param   &PluginsManager  - The plugins
     * @param   &string          - The class name
     * @param   &string          - The real class name (with namespace)
	 */
    public function initSingle(&$context, &$plugins, &$className, &$realClassname)
    {
            $instance = new $realClassname($context);

            $funcName = "onInit";
            if(method_exists($instance, $funcName)){
                $instance->$funcName($context);
            }

            $eventFuncName = PluginsManager::EVENT_FUNC_NAME;
            if(method_exists($instance, $eventFuncName)){
                $instance->$eventFuncName($context, "init");
            }

            $plugins->getRunners()[$className] = &$instance;

    }

}