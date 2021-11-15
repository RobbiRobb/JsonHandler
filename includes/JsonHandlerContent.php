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

class JsonHandlerContent {
	
	private static $content = array();
	
	/**
	* get content of page from title. Uses array to prevent loading of same content multiple times on one page
	*
	* @param String $title
	* @throws Exception
	*/
	public static function getContent(String $title) {
		if(array_key_exists($title, self::$content)) {
			return self::$content[$title];
		} else {
			$revision = Revision::newFromTitle(Title::newFromText($title));
			
			if(is_null($revision)) {
				throw new Exception('wiki page '.$title.' not found');
			}
			
			self::$content[$title] = $revision->getContent()->getNativeData();
			
			return self::$content[$title];
		}
	}
}

?>