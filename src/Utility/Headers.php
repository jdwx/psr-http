<?php


declare( strict_types = 1 );


namespace JDWX\Psr7\Utility;


final class Headers {


    public static function parse( string $i_stHeaders ) : array {
        $rHeaders = [];
        foreach ( preg_split( '/\r?\n/', $i_stHeaders ) as $stHeader ) {
            if ( ! preg_match( '/^([^:]+): (.*)$/', $stHeader, $rMatches ) ) {
                continue;
            }
            $stHeaderName = strtolower( trim( $rMatches[ 1 ] ) );
            $rHeaders[ $stHeaderName ] = self::parseValue( trim( $rMatches[ 2 ] ) );
        }
        return $rHeaders;
    }


    public static function parseValue( string $i_stValue ) : array {
        $rOut = [];
        foreach ( explode( ';', $i_stValue ) as $st ) {
            $st = trim( $st );
            if ( ! str_contains( $st, '=' ) ) {
                $rOut[] = $st;
                continue;
            }
            [ $key, $value ] = explode( '=', $st, 2 );
            $value = trim( $value );
            if ( str_starts_with( $value, '"' ) && str_ends_with( $value, '"' ) ) {
                $value = trim( substr( $value, 1, -1 ) );
            }
            $rOut[ trim( $key ) ] = $value;
        }
        return $rOut;
    }


    public static function splitFromBody( string $i_st ) : array {
        $r = preg_split( '/(\r\n\r\n|\n\n|\r\r)/', $i_st, 2 );
        $stHeaders = preg_replace( '/[\r\n]+/', "\n", array_shift( $r ) ?? '' );
        $stBody = array_shift( $r ) ?? '';
        return [ trim( $stHeaders ), $stBody ];
    }


    public static function splitFromBodyAndParse( string $i_st ) : array {
        [ $stHeaders, $stBody ] = self::splitFromBody( $i_st );
        return [
            self::parse( trim( $stHeaders ) ),
            $stBody,
        ];
    }


}
