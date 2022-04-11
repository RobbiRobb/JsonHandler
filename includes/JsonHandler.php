<?php
/**
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License as
* published by the Free Software Foundation; either version 2 of
* the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
* General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software Foundation,
* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

class JsonHandler {
	
	/**
	* {{#json: json-page -> property [-> properties][|arguments]}}
	* tries to process json expression and handels errors
	*
	* @param Parser $parser
	* @param PPFrame $frame
	* @param array $args
	*/
	public static function json(Parser $parser, PPFrame $frame, array $args) {
		try {
			return self::processJson($parser, $frame, $args);
		} catch(JsonHandlerException $e) {
			return '<strong class="error">' . htmlspecialchars($e->getUserFriendlyMessage()) . '</strong>';
		}
	}
	
	/**
	* processes expression on json file
	*
	* @param Parser $parser
	* @param PPFrame $frame
	* @param array $args
	*/
	private static function processJson(Parser $parser, PPFrame $frame, array $args) {
		$args = explode("->", $frame->expand($args[0]));
		$title = trim($args[0]);
		
		try {
			$content = JsonHandlerContent::getContent($title, $parser);
		} catch(Exception $e) {
			throw new JsonHandlerException("jsonhandler_page_not_found", $title);
		}
		
		if(!$content) {
			throw new JsonHandlerException("jsonhandler_too_many_expensive_functions", $title);
		}
		
		$json = json_decode($content);
		$trace = $title;
		$args = array_slice($args, 1);
		
		if(empty($args)) {
			throw new JsonHandlerException("jsonhandler_no_properties", $title);
		}
		
		if($json === null) {
			throw new JsonHandlerException("jsonhandler_error_decoding_page", $title);
		}
		
		foreach($args as $arg) {
			$arg = trim($arg);
			$trace .= "->" . $arg;
			$suppressErors = false;
			$isNullable = false;
			
			if($arg === "*") {
				throw new JsonHandlerException("jsonhandler_debug", [$trace, $parser->internalParse(json_encode($json))]);
			}
			if(substr($arg, 0, 1) === "@") {
				$arg = substr($arg, 1);
				$suppressErors = true;
			}
			if(substr($arg, 0, 1) === "?") {
				$arg = substr($arg, 1);
				$isNullable = true;
			}
			
			if(is_array($json)) {
				if(isset($json[$arg])) {
					$json = $json[$arg];
				} else {
					if($suppressErors) {
						return "";
					}
					throw new JsonHandlerException("jsonhandler_could_not_access_property", [$arg, $trace]);
				}
			} else if(is_object($json)) {
				if(property_exists($json, $arg)) {
					$json = $json->$arg;
				} else {
					if($suppressErors) {
						return "";
					}
					throw new JsonHandlerException("jsonhandler_could_not_access_property", [$arg, $trace]);
				}
			} else {
				if($suppressErors) {
					return "";
				}
				if($isNullable) {
					return $parser->internalParse($json);
				}
				throw new JsonHandlerException("jsonhandler_could_not_access_property", [$arg, $trace]);
			}
		}
		
		if(is_array($json) || is_object($json)) {
			throw new JsonHandlerException("jsonhandler_not_string", $trace);
		} else {
			return $parser->internalParse($json);
		}
	}
}

?>