<?php
class Session {

	/**
	* @param $name : string -> key in session variable
	* @return value in session variable with given key
	*/
	public static function getSession($name = null) {
		if (!empty($name)) {
			return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
		}
	}
	
	/**
	* Set session
	* @param $name : string -> key in session variable
	* @param $value
	*/
	public static function setSession($name = null, $value = null) {
		if (!empty($name) && !empty($value)) {
			$_SESSION[$name] = $value;
		}
	}	
	
	/**
	* Clear session with id or whole session
	* @param $id : string -> key in session variable
	*/
	public static function clearSession($id = null) {
		if (!empty($id) && isset($_SESSION[$id])) {
			$_SESSION[$id] = null;
			unset($_SESSION[$id]);
		} else {
			session_destroy();
		}
	}
	
}

?>