<?php

namespace Geocuba\AdminBundle\Util;

/**
 * Class to validate params
 */
class Validar
{
	private static $MIN_INT = -2147483647;
	private static $MAX_INT = 2147483647;
	private static $MAX_STR_LEN = 1048576;

	/**
	 * Validate that a value is an integer
	 * @Param Type mixed [value] : Reference to the value to validate
	 * @Param Type integer [min_value] : Min value of the integer
	 * @Param Type integer [max_value] : Max value of the integer
	 * @Return bool
	 */
	public static function IsInt(&$value, $min_value = null, $max_value = null)
	{
		$_val = strval($value);

		if(is_null($min_value))
			$min_value = self::$MIN_INT;
		if(is_null($max_value))
			$max_value = self::$MAX_INT;

		// Validate the length of the input
		if(strlen($_val) > 11 || strlen($_val) == 0)
			return false;

		// Validate that the value is numeric
		if(!is_numeric($_val))
			return false;

		// Validate that the number have not piriot,
		// it mean that is not float
		if(strpos($_val, '.') !== false)
			return false;

		// Get the value
		$_int_val = intval($_val);

		// Validate that the number is in range
		if($_int_val < $min_value || $_int_val > $max_value)
			return false;

		// Get the string of the value
		$_str_val = strval($_int_val);
		// Validate for overfload
		if($_val != $_str_val)
			return false;

		return true;

	}

	/**
	 * Function to convert a value into an integer and validate it
	 * @Param Type mixed [value] : Reference to the value to validate
	 * @Param Type integer [min_value] : Min value of the integer
	 * @Param Type integer [max_value] : Max value of the integer
	 * @Return bool
	 */
	public static function ToInt(&$value, $min_value = null, $max_value = null)
	{
		if(self::IsInt($value, $min_value, $max_value))
		{
			settype($value, 'integer');
			return true;
		}
		else
			return false;
	}

	/**
	 * Validate that a value is a float
	 * @Param Type mixed [value] : Reference to the value to validate
	 * @Param Type integer [min_value] : Min value of the float
	 * @Param Type integer [max_value] : Max value of the float
	 * @Return bool
	 */
	public static function IsFloat(&$value, $min_value = null, $max_value = null)
	{
		$_val = strval($value);

		if(is_null($min_value))
			$min_value = self::$MIN_INT;
		if(is_null($max_value))
			$max_value = self::$MAX_INT;

		// Validate the length of the input
		if(strlen($_val) > 32 || strlen($_val) == 0)
			return false;

		// Validate that the value is numeric
		if(!is_numeric($_val))
			return false;

		// Get the value
		$_float_val = floatval($_val);

		// Validate that the number is in range
		if($_float_val < $min_value || $_float_val > $max_value)
			return false;

		return true;
	}

	/**
	 * Function to convert a value into a float and validate it
	 * @Param Type mixed [value] : Reference to the value to validate
	 * @Param Type integer [min_value] : Min value of the float
	 * @Param Type integer [max_value] : Max value of the float
	 * @Return bool
	 */
	public static function ToFloat(&$value, $min_value = null, $max_value = null)
	{
		if(self::IsFloat($value, $min_value, $max_value))
		{
			settype($value, 'float');
			return true;
		}
		else
			return false;
	}

	/**
	 * Function to validate that a value is a date
	 * @Param Type mixed [value] : Reference to the value to validate
	 * @Param Type integer [min_value] : Min date
	 * @Param Type integer [max_value] : Max date
	 * @Return bool
	 */
	public static function IsDate(&$value, $min_value = null, $max_value = null)
	{
		$_val = strval($value);

		// Check the length of the string
		if( strlen($_val) > 64 || strlen($_val) == 0)
			return false;

		// Try to convert into date
		$_date = strtotime($_val);
		if($_date === -1)
			return false;

		// Compare with the min value
		if(!is_null($min_value))
			if($_date < strtotime($min_value))
				return false;

		// Compare with the max value
		if(!is_null($max_value))
			if($_date > strtotime($max_value))
				return false;

		return true;
	}

	/**
	 * Function to validate that a value is a date
	 * @Param Type mixed [value] : Reference to the value to validate
	 * @Param Type string [date_format] : Format of the date to be returned
	 * @Param Type integer [min_value] : Min date
	 * @Param Type integer [max_value] : Max date
	 * @Return string
	 */
	public static function ToDate(&$value, $date_format, $min_value = null, $max_value = null)
	{
		if(self::IsDate($value, $min_value, $max_value))
		{
			$value = date($date_format, strtotime($value));
			settype($value, 'string');
			return true;
		}
		else
			return false;
	}

	/**
	 * Function to check the size of an string
	 * @Param Type mixed [value] : Reference to the value to validate
	 * @Param Type integer [min_size] : Min size of the string
	 * @Param Type integer [max_size] : Max size of the string
	 * @Return bool
	 */
	public static function CheckStringSize(&$value, $min_size = -1, $max_size = -1)
	{
		$_length = strlen($value);

		// Check min length
		if($min_size >= 0)
			if($_length < $min_size)
				return false;

		// Check max length
		if($max_size == -1 || $max_size > self::$MAX_STR_LEN)
			$max_size = self::$MAX_STR_LEN;

		if($_length > $max_size)
				return false;

		return true;
	}
	
	/**
	 * Function to check if an string is in JSON format
	 * @Param Type string [$str_value] : Reference to the value to validate
	 * @Return bool
	 */
	public static function IsJSON(&$str_value)
	{
	     $_result = @json_decode($str_value);
	     return ($_result !== NULL);
	}
	
	/**
	 * Function to convert an string in JSON format into an array or an object
	 * @Param Type string [$str_value] : Reference to the value to validate
	 * @Param Type bool [$as_array] : This parameter indicate if the result is an array(true) or an object(false)
	 * @Return bool
	 */
	public static function ToJSON(&$str_value, $as_array = false)
	{
	     $_result = @json_decode($str_value, $as_array);
	     if($_result === NULL)
		  return false;

	     $str_value = $_result;
	     return true;
	}
	
	/**
	 * Function to clean an string form an SQL injection
	 * @Param Type string [$str_value] : Reference to the value to clean
	 */
	public static function ToCleanSQL(&$str_value)
	{
	     $_clean = addslashes($str_value);
          //while(strpos($_clean, '\"') !== false)
               $_clean = str_replace('\"', '"', $_clean);
	     $str_value = $_clean;
	}

	/**
	 * Function to check if the params contains the expected
	 * @Param Type array [names_array] : Array with the name of expected parameters
	 * @Param Type array [params] : Array of parameters
	 * @Return mixed : If the function return true, then the expected parameters exists.
	 * Otherwise return an array with the params that leaks
	 */
	public static function ExistsParams($names_array, &$params)
	{
		// Get the name of all paramenters
		$params_names = array_keys($params);
		// Get the difference between the expected names and the real names
		$_diff = array_diff($names_array, $params_names);

		// If there is any element into the array, then is false
		if(count($_diff) == 0)
			return true;

		return $_diff;
	}

	/**
	 * Function to check if the params are pass by post
	 * @Param Type array [names_array] : Array with the name of expected parameters
	 * @Param Type array [params] : Array of parameters
	 * @Return mixed : If the function return true, then the expected parameters exists.
	 * Otherwise return an array with the params that leaks
	 */
	public static function PassByPost($names_array, &$params)
	{
		// Get the name of all paramenters
		$params_names = $params['<METADATA>']['POST'];
		// Get the difference between the expected names and the real names
		$_diff = array_diff($names_array, $params_names);

		// If there is any element into the array, then is false
		if(count($_diff) == 0)
			return true;

		return $_diff;
	}

	/**
	 * Function to check if the params are pass by get
	 * @Param Type array [names_array] : Array with the name of expected parameters
	 * @Param Type array [params] : Array of parameters
	 * @Return mixed : If the function return true, then the expected parameters exists.
	 * Otherwise return an array with the params that leaks
	 */
	public static function PassByGet($names_array, &$params)
	{
		// Get the name of all paramenters
		$params_names = $params['<METADATA>']['GET'];
		// Get the difference between the expected names and the real names
		$_diff = array_diff($names_array, $params_names);

		// If there is any element into the array, then is false
		if(count($_diff) == 0)
			return true;

		return $_diff;
	}
}

?>
