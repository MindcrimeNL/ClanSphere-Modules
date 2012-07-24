<?php

if (!defined('MINDCRIME_CLANSPHERE_TOOLS'))
	define('MINDCRIME_CLANSPHERE_TOOLS', true);

/**
 * Encode to ClanSphere encoding (UTF-8) if possible, using iconv() (or mb_detect_encoding()) when available
 * 
 * @param	string	$input 				the string to be encoded
 * @param	string	$charsetFrom	the current character set of the string
 * @param	string	$charsetTo		optional: the character set to convert to, if not set encode to the ClanSphere encoding.
 * 
 * @return	string	the (possibly) converted string
 * 
 * @uses	iconv() if possible
 */
function cs_encode($input, $charsetFrom = 'ISO-8859-15', $charsetTo = null)
{
	global $cs_main;
	
	if (is_null($charsetTo))
	{
		$charsetTo = strtoupper($cs_main['charset']);
	}
	
	// no need to convert if the charsets are the same
	if (strtoupper($charsetTo) == strtoupper($charsetFrom))
	{
		return $input;
	}
	
	if (function_exists('iconv'))
	{
		/* use transliteral */
		$return = @iconv(strtoupper($charsetFrom), strtoupper($charsetTo).'//TRANSLIT', $input);
		if ($return !== false)
			return $return;
	}
	else
	{
		/* Uses utf8_encode/utf8_decode and mb_detect_encoding/mb_convert_encoding.
		 * To be extended in the future...
		 */
		if (strtoupper($charsetTo) == 'UTF-8')
		{
			switch (strtoupper($charsetFrom))
			{
			case 'ISO-8859-1':
//			case 'ISO-8859-15':
				return utf8_encode($input);
				break;
			default:
				if (function_exists('mb_detect_encoding'))
				{
					$encoding = mb_detect_encoding($input);
					if (is_string($encoding))
						return mb_convert_encoding($input, 'UTF-8', $encoding);
					// else, do nothing
				}
				break;
			}
		}
		if (strtoupper($charsetFrom) == 'UTF-8')
		{
			switch (strtoupper($charsetTo))
			{
			case 'ISO-8859-1':
//			case 'ISO-8859-15':
				return utf8_decode($input);
				break;
			default:
				break;
			}
		}
	}
	// if we don't know what to do, just return it
	return $input;
} // function cs_encode

/*
 * Description : A function with a very simple but powerful xor method to encrypt
 * and/or decrypt a string with an unknown key. Implicitly the key is
 * defined by the string itself in a character by character way.
 * There are 4 items to compose the unknown key for the character
 * in the algorithm
 * 1.- The ascii code of every character of the string itself
 * 2.- The position in the string of the character to encrypt
 * 3.- The length of the string that include the character
 * 4.- Any special formula added by the programmer to the algorithm
 * to calculate the key to use
 */
function cs_encrypt_decrypt($string)
{
	//Function : encrypt/decrypt a string message v.1.0 without a known key
	//Author   : Aitor Solozabal Merino (spain)
	//Email    : aitor-3@euskalnet.net
	//Date     : 01-04-2005
  $strlen = strlen($string);
  $strencrypted = '';
  for ($pos = 0; $pos < $strlen ; $pos++)
  {
		// long code of the function to explain the algoritm
		// this function can be tailored by the programmer modifyng the formula
		// to calculate the key to use for every character in the string.
		$usekey = (($strlen+$pos)+1); // (+5 or *3 or ^2)
		// after that we need a module division because can´t be greater than 255
		$usekey = (255+$usekey) % 255;
		$encryptbyte = substr($string, $pos, 1);
		$asciibyte = ord($encryptbyte);
		$xorbyte = $asciibyte ^ $usekey;  // xor operation
		$encrypted = chr($xorbyte);
		$strencrypted .= $encrypted;
		 
		//short code of  the function once explained
		// $str_encrypted_message .= chr((ord(substr($str_message, $position, 1))) ^ ((255+(($len_str_message+$position)+1)) % 255));
	}
	return $strencrypted;
} // function cs_encrypt_decrypt


/*
 * Description: String EnCrypt + DeCrypt function
 * Author: halojoy, July 2006
 *
 * @param	string	$text text to encrypt/decrytpt
 * @param	string	$text the encryption key
 *
 * @return	string the decrypted/encrypted string
 */

define('CRYPT_MODE_NONE', 0); // unencrypted
define('CRYPT_MODE_SITE', 1); // use site crypt key for crypt/decrypt
define('CRYPT_MODE_PRIV', 2); // for future use: users can send messages using a private key

function cs_crypt($text, $key= '')
{
	// return text unaltered if the key is blank
	if ($key == '')
		return $text;

	// remove the spaces in the key
	$key = str_replace(' ', '', $key);
	if (strlen($key) < 8)
		exit('key error');

	// set key length to be no more than 32 characters
	$key_len = strlen($key);
	if ($key_len > 32)
		$key_len = 32;

	$k = array(); // key array
	// fill key array with the bitwise AND of the ith key character and 0x1F
	for ($i = 0; $i < $key_len; $i++)
		$k[$i] = ord($key{$i}) & 0x1F;

	// perform encryption/decryption
	for ($i = 0; $i < strlen($text); $i++) {
		$e = ord($text{$i});
		// if the bitwise AND of this character and 0xE0 is non-zero
		// set this character to the bitwise XOR of itself
		// and the ith key element, wrapping around key length
		// else leave this character alone
		if ($e & 0xE0)
			$text{$i} = chr($e ^ $k[$i % $key_len]);
	}
	return $text;
} // function cs_crypt

/**
 * Show byte size in readable format
 */
function cs_format_bytes($bytes, $decimals = 2)
{
	if ($bytes < 1024)
	{
		return sprintf('%d Bytes', $bytes);
	}
	$bytes /= 1024.0;
	if ($bytes < 1024)
	{
		return sprintf('%.'.$decimals.'f KiB', $bytes);
	}
	$bytes /= 1024.0;
	if ($bytes < 1024)
	{
		return sprintf('%.'.$decimals.'f MiB', $bytes);
	}
	$bytes /= 1024.0;
	if ($bytes < 1024)
	{
		return sprintf('%.'.$decimals.'f GiB', $bytes);
	}
	$bytes /= 1024.0;
	return sprintf('%.'.$decimals.'f TiB', $bytes);
} // function cs_format_bytes

/**
 * Check if a subtemplate exists for the mod and action.
 * If we use cs_subtemplate(), it raises a cs_error(). We want to prevent that from happening
 *
 * @param	string	$source
 * @param	string	$mod
 * @param	string	$action
 * 
 * @return	boolean	true if the subtemplate exists (cs_subtemplate() may be called), false otherwise
 */
function cs_subtemplate_exists($mod, $action)
{
  global $cs_main;

  $cs_main['def_theme'] = empty($cs_main['def_theme']) ? 'base' : $cs_main['def_theme'];

  $target = 'themes/' . $cs_main['def_theme'] . '/' . $mod . '/' . $action . '.tpl';
  if ($cs_main['def_theme'] != 'base' and !file_exists($target))
  {
    $target = 'themes/base/' . $mod . '/' . $action . '.tpl';
  }
  if (!file_exists($target))
  {
    return false;
  }
  return true;
} // function cs_subtemplate_exists

/**
 * Check if the user is a member from the clan with ID 1, checks if the user is not deleted and active.
 * 
 * @param	int	$users_id
 * 
 * @return	boolean	returns true if the users is a member, false otherwise
 */
function cs_is_member($users_id)
{
	settype($users_id, 'integer');
	
	if ($users_id <= 0)
		return false;
	
	$where = 'm.users_id = '.$users_id.' AND us.users_delete = 0 AND us.users_active = 1 AND sq.clans_id = 1';
	$count = cs_sql_count(__FILE__, 'members m LEFT JOIN {pre}_users us ON m.users_id = us.users_id LEFT JOIN {pre}_squads sq ON m.squads_id = sq.squads_id', $where);
	
	if ($count > 0)
		return true;
	
	return false;
} // function cs_is_member

/**
 * Cut text (usefull for navlists), multibyte safe if supported in PHP
 * 
 * @param	string	$text the text to be cut
 * @param	int			$maxlength	the maximum length to accept
 * @param	string	$subst	an additional string to be added after the cut
 * @param	int			$subtract	substract this number of extra characters (for correction of $subst)
 * 
 * @return	the cut text string
 */
function cs_textcut($text, $maxlength, $subst = '...', $subtract = 3)
{
	/* prevent some stupid stuff */
	if ($maxlength < 1)
		return $text;
	if ($maxlength < $subtract)
		return $text;
	/* check for multi-byte support */
	if (function_exists('mb_strlen'))
	{
		global $cs_main;
		/* prevent any &xxx; being stripped in half */
		$realtext = html_entity_decode($text, ENT_QUOTES, $cs_main['charset']);
		if (mb_strlen($realtext, $cs_main['charset']) > $maxlength)
		{
			$text = mb_substr($realtext, 0, $maxlength - $subtract, $cs_main['charset']).$subst;
		}
	}
	else
	{
		if (strlen($text) > $maxlength)
		{
			$text = substr($text, 0, $maxlength - $subtract).$subst;
		}
	}
	return $text;
} // function cs_textcut

/**
 * Fetch the contents of a remote URL and try to
 * get it via redirects even if open_basedir or safe_mode
 * is in effect...
 */
function cs_curl_contents($url)
{
	$timeout = 3; // set to zero for no timeout
	$maxredirects = 5; // set to zero for no redirects
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
	$noredirect = false;
	$bd = ini_get('open_basedir');
	$sm = ini_get('safe_mode');
	if (!empty($bd) || !empty($sm) || $maxredirects == 0)
	{
		$noredirect = true;
	}
	if ($noredirect)
	{
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		$mr = $maxredirects;
		if ($mr > 0)
		{
			$newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

			// curl_copy_handle crashes in php < 5.2.11
			// $rch = curl_copy_handle($ch);
			$rch = curl_init();
			curl_setopt($rch, CURLOPT_URL, $url);
			curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($rch, CURLOPT_CONNECTTIMEOUT, $timeout);
			curl_setopt($rch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
			curl_setopt($rch, CURLOPT_HEADER, true);
			curl_setopt($rch, CURLOPT_NOBODY, true);
			curl_setopt($rch, CURLOPT_FOLLOWLOCATION, false);
			do {
				curl_setopt($rch, CURLOPT_URL, $newurl);
				$header = curl_exec($rch);
				if (curl_errno($rch))
					$code = -1;
				else
				{
					$code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
					if (in_array($code, array(300, 301, 302, 303, 305, 307)))
					{
						preg_match('/Location:(.*?)\n/', $header, $matches);
						$newurl = trim(array_pop($matches));
					}
					else
					{
						if ($code == 200) // only ok code
						  $code = 0;
						else if ($code >= 400)
							$code = -1;
					}
				}
			} while ($code > 0 && --$mr);
			curl_close($rch);
			if (!$mr || $code == -1)
			{
				curl_close($ch);
				return false;
			}
			curl_setopt($ch, CURLOPT_URL, $newurl); 
		}
	}
	else
	{
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
	}
	$file_contents = curl_exec($ch);
	if ($file_contents === false || empty($file_contents))
	{
		$file_contents = false;
		// echo 'CURL #'.curl_errno($ch).': '.curl_error($ch).'<br />';
	}
	curl_close($ch);
	return $file_contents;
}

/**
 * Get the image size with max size optional, but maintain aspect ratio
 * 
 * @param	string	$imagePath	path to the image
 * @param int			$maxWidth
 * @param	int			$maxHeight
 * 
 * @return	array()		array or false, the same as getimagesize()
 */
function cs_getimagesize($imagePath, $maxWidth = 0, $maxHeight = 0)
{
	$use_curl = false;
	if (strtolower(substr($imagePath, 0, 7)) == 'http://'
			|| strtolower(substr($imagePath, 0, 6) == 'ftp://'))
		$use_curl = true;
	/* use curl if available, we can set decent timeouts for it */
	if ($use_curl && function_exists('curl_version') && function_exists('gd_info'))
	{
		$file_contents = cs_curl_contents($imagePath);

		if ($file_contents === false)
			return false;
		$new_image = imagecreatefromstring($file_contents);
		if ($new_image !== false)
		{
			$size = array();
			$size[0] = imagesx($new_image);
			$size[1] = imagesy($new_image);
		}
		else
			return false;
	}
	else
		$size = getimagesize($imagePath);
	
	if ($size === false)
	{
		return false;
	}
	if ($maxWidth > 0)
	{
		if ($maxHeight > 0)
		{
			/* both matter */
			if ($size[0] > $maxWidth)
			{
				if ($size[1] > $maxHeight)
				{
					if ((1.0*$size[0] / $maxWidth ) > (1.0*$size[1] / $maxHeight))
					{
						/* width is the important factor */ 
						$size[1] = (int) ($size[1] * (1.0*$maxWidth / $size[0]));
						$size[0] = $maxWidth;
					}
					else
					{
						/* height is the important factor */ 
						$size[0] = (int) ($size[0] * (1.0*$maxHeight / $size[1]));
						$size[1] = $maxHeight;
					}
				}
				else
				{
					/* only width */
					if ($size[0] > $maxWidth)
					{
						$size[1] = (int) ($size[1] * (1.0*$maxWidth / $size[0]));
						$size[0] = $maxWidth;
					}
				}
			}
			else
			{
				if ($size[1] > $maxHeight)
				{
					/* only height */
					$size[0] = (int) ($size[0] * (1.0*$maxHeight / $size[1]));
					$size[1] = $maxHeight;
				}
			}
		}
		else
		{
			/* only width matters */
			if ($size[0] > $maxWidth)
			{
				$size[1] = (int) ($size[1] * (1.0*$maxWidth / $size[0]));
				$size[0] = $maxWidth;
			}
		}
	}
	else
	{
		if ($maxHeight > 0)
		{
			/* only height matters */
			if ($size[1] > $maxHeight)
			{
				$size[0] = (int) ($size[0] * (1.0*$maxHeight / $size[1]));
				$size[1] = $maxHeight;
			}
		}
		/* else if both are 0, we don't need to do anything */
	}
	return $size;
} // function cs_getimagesize
