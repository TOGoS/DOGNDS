<?php

class DOGNDS_Hash_HashUtil
{
        public static function urnToBase32Sha1( $urn ) {
                if( preg_match('/^urn:(?:sha1|bitprint):([0-9A-Z]{32})(?:\.|$)/',$urn,$bif) ) {
                        return $bif[1];
                } else {
                        return null;
                }
        }
        
        public static function verifyHashUrn( $content, $urn ) {
                if( preg_match('/^urn:(?:sha1|bitprint):([0-9A-Z]{32})(?:\.|$)/',$urn,$bif) ) {
                        $expectedSha1 = $bif[1];
                        $calculatedSha1 = DOGNDS_String_Base32::encode(sha1( $content, true ));
                        if( $expectedSha1 == $calculatedSha1 ) return true;
                        DOGNDS_Log::log("Expected SHA-1 = $expectedSha1, calculated = $calculatedSha1 (Base32) (calculated from data of length ".strlen($content).")");
                        return false;
                } else {
                        return false;
                }
        }
}
