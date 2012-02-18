<?php

namespace DOGNDS\String;

/* (PD) 2006 The Bitzi Corporation
 * Please see http://bitzi.com/publicdomain for more info. */

/**
 * Base32 - encodes and decodes RFC3548 Base32
 * (see http://www.faqs.org/rfcs/rfc3548.html )
 *
 * @author Robert Kaye
 * @author Gordon Mohr
 * @author Dan Stevens (translated to PHP)
 */
class Base32
{
	protected static $base32Chars =
		"ABCDEFGHIJKLMNOPQRSTUVWXYZ234567";
	protected static $base32Lookup = array(
		0xFF,0xFF,0x1A,0x1B,0x1C,0x1D,0x1E,0x1F, // '0', '1', '2', '3', '4', '5', '6', '7'
		0xFF,0xFF,0xFF,0xFF,0xFF,0xFF,0xFF,0xFF, // '8', '9', ':', ';', '<', '=', '>', '?'
		0xFF,0x00,0x01,0x02,0x03,0x04,0x05,0x06, // '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G'
		0x07,0x08,0x09,0x0A,0x0B,0x0C,0x0D,0x0E, // 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'
		0x0F,0x10,0x11,0x12,0x13,0x14,0x15,0x16, // 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W'
		0x17,0x18,0x19,0xFF,0xFF,0xFF,0xFF,0xFF, // 'X', 'Y', 'Z', '[', '\', ']', '^', '_'
		0xFF,0x00,0x01,0x02,0x03,0x04,0x05,0x06, // '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g'
		0x07,0x08,0x09,0x0A,0x0B,0x0C,0x0D,0x0E, // 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o'
		0x0F,0x10,0x11,0x12,0x13,0x14,0x15,0x16, // 'p', 'q', 'r', 's', 't', 'u', 'v', 'w'
		0x17,0x18,0x19,0xFF,0xFF,0xFF,0xFF,0xFF  // 'x', 'y', 'z', '{', '|', '}', '~', 'DEL'
	);

	/**
	 * Encodes byte array to Base32 String.
	 *
	 * Unlike the encoding described in RFC 3548, this does not
	 * pad encoded data with '=' characters.
	 */
	public static function encode( $bytes ) {
		$i = 0; $index = 0; $digit = 0;
		$base32 = "";

		while( $i < strlen($bytes) ) {
			$currByte = ord($bytes{$i});
			/* Is the current digit going to span a byte boundary? */
			if( $index > 3 ) {
				if( ($i + 1) < strlen($bytes) ) {
					$nextByte = ord($bytes{$i+1});
				} else {
					$nextByte = 0;
				}

				$digit = $currByte & (0xFF >> $index);
				$index = ($index + 5) % 8;
				$digit <<= $index;
				$digit |= $nextByte >> (8 - $index);
				$i++;
			} else {
				$digit = ($currByte >> (8 - ($index + 5))) & 0x1F;
				$index = ($index + 5) % 8;
				if( $index == 0 ) $i++;
			}
			$base32 .= self::$base32Chars{$digit};
		}

		return $base32;
	}

	/**
	 * Decodes the given Base32 String to a raw byte array.
	 */
	public static function decode( $base32 ) {
		$bytes = array();

		for( $i=strlen($base32)*5/8-1; $i>=0; --$i ) {
			$bytes[] = 0;
		}

		for( $i = 0, $index = 0, $offset = 0; $i < strlen($base32); $i++ ) {
			$lookup = ord($base32{$i}) - ord('0');

			/* Skip chars outside the lookup table */
			if( $lookup < 0 || $lookup >= count(self::$base32Lookup) ) {
				continue;
			}

			$digit = self::$base32Lookup[$lookup];

			/* If this digit is not in the table, ignore it */
			if( $digit == 0xFF ) continue;

			if( $index <= 3 ) {
				$index = ($index + 5) % 8;
				if( $index == 0) {
					$bytes[$offset] |= $digit;
					$offset++;
					if( $offset >= count($bytes) ) break;
				} else {
					$bytes[$offset] |= $digit << (8 - $index);
				}
			} else {
				$index = ($index + 5) % 8;
				$bytes[$offset] |= ($digit >> $index);
				$offset++;

				if ($offset >= count($bytes) ) break;
				$bytes[$offset] |= $digit << (8 - $index);
			}
		}
		$bites = "";
		foreach( $bytes as $byte ) $bites .= chr($byte);
		return $bites;
	}

	/**
	 * For testing, take a command-line argument in Base32, decode, print in hex,
	 * encode, print
	 */
	public static function main( $args ) {
		if( count($args) == 0) {
			echo "You must supply a Base32-encoded argument.";
			return;
		}
		echo " Original: ", $args[0], "\n";
		$decoded = self::decode($args[0]);
		echo " Decoded: ", $decoded, "\n";
		echo " Reencoded: " + self::encode($decoded), "\n";
	}
}
