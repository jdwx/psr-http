<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Utility;


final class Headers {


    /**
     * @param array<string, string|list<string>> $i_rHeaders List of headers.
     * @param string $i_stHeader The header name to get.
     * @return list<string> List of values for the header.
     */
    public static function get( array $i_rHeaders, string $i_stHeader ) : array {
        $i_stHeader = strtolower( trim( $i_stHeader ) );
        if ( ! isset( $i_rHeaders[ $i_stHeader ] ) ) {
            return [];
        }
        return $i_rHeaders[ $i_stHeader ];
    }


    /**
     * @param array<string, string|list<string>> $i_rHeaders
     * @param string $i_stHeader The header name to get.
     * @return string The header value, with multiple values joined by a comma.
     */
    public static function getLine( array $i_rHeaders, string $i_stHeader ) : string {
        $rHeader = self::get( $i_rHeaders, $i_stHeader );
        if ( empty( $rHeader ) ) {
            return '';
        }
        return implode( ', ', $rHeader );
    }


    /**
     * @param string $i_stHeaders The header text to parse.
     * @return HeaderList The parsed headers.
     */
    public static function parse( string $i_stHeaders ) : HeaderList {
        return HeaderList::fromString( $i_stHeaders );
    }


    /**
     * @param string $i_stValue The header value to parse.
     * @return array<int|string, string> The parsed value.
     *
     * This parses a header value into an array of values or key-value pairs.
     * Values get integer keys starting at 0. Quotes around values are removed.
     *
     * Content-Type: text/html; charset=UTF-8
     *
     * would return:
     *
     * [ 0 => 'text/html', 'charset' => 'UTF-8' ]
     */
    public static function parseValue( string $i_stValue ) : array {
        $rOut = [];
        foreach ( explode( ';', $i_stValue ) as $st ) {
            $st = trim( $st );
            if ( ! str_contains( $st, '=' ) ) {
                $value = $st;
                $key = null;
            } else {
                [ $key, $value ] = explode( '=', $st, 2 );
                $key = trim( $key );
                $value = trim( $value );
            }
            if ( str_starts_with( $value, '"' ) && str_ends_with( $value, '"' ) ) {
                $value = trim( substr( $value, 1, -1 ) );
            }
            if ( $key ) {
                $rOut[ $key ] = $value;
            } else {
                $rOut[] = $value;
            }
        }
        return $rOut;
    }


    /**
     * @param string $i_stFullBody The full body to split.
     * @return list<string> A list of two strings: the headers and the body.
     */
    public static function splitFromBody( string $i_stFullBody ) : array {
        $r = preg_split( '/(\r\n\r\n|\n\n|\r\r)/', $i_stFullBody, 2 );
        $stHeaders = preg_replace( '/[\r\n]+/', "\n", array_shift( $r ) ?? '' );
        $stBody = array_shift( $r ) ?? '';
        return [ trim( $stHeaders ), $stBody ];
    }


    /**
     * @param string $i_stFullBody The full body to split.
     * @return array{0: HeaderList, 1: string} A list of
     *                          two elements: the (parsed) headers and the body.
     */
    public static function splitFromBodyAndParse( string $i_stFullBody ) : array {
        [ $stHeaders, $stBody ] = self::splitFromBody( $i_stFullBody );
        return [
            self::parse( trim( $stHeaders ) ),
            $stBody,
        ];
    }


}
