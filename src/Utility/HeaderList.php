<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Utility;


class HeaderList {


    /** @var array<string, array<int|string, string>> */
    private array $rValueCache = [];


    /** @param array<string, list<string>> $rHeaders */
    public function __construct( private readonly array $rHeaders ) {}


    /** @param array<string, string|list<string>>|string|HeaderList $i_headers */
    public static function from( array|string|HeaderList $i_headers ) : self {
        if ( is_array( $i_headers ) ) {
            return self::fromArray( $i_headers );
        }
        if ( $i_headers instanceof HeaderList ) {
            return $i_headers;
        }
        return self::fromString( $i_headers );
    }


    /** @param array<string, string|list<string>> $i_rHeaders */
    public static function fromArray( array $i_rHeaders ) : self {
        $rHeaders = [];
        foreach ( $i_rHeaders as $stHeader => $value ) {
            $stHeader = strtolower( trim( $stHeader ) );
            if ( ! is_array( $value ) ) {
                $value = [ $value ];
            }
            if ( ! isset( $rHeaders[ $stHeader ] ) ) {
                $rHeaders[ $stHeader ] = [];
            }
            $rHeaders[ $stHeader ] = array_merge( $rHeaders[ $stHeader ], $value );
        }
        return new self( $rHeaders );
    }


    public static function fromString( string $i_stHeaders ) : self {
        $rHeaders = [];
        foreach ( preg_split( '/\r?\n/', $i_stHeaders ) as $stHeader ) {
            $r = explode( ':', $stHeader, 2 );
            if ( count( $r ) < 2 ) {
                continue;
            }
            $stHeader = strtolower( trim( array_shift( $r ) ) );
            $value = trim( array_shift( $r ) );
            if ( ! isset( $rHeaders[ $stHeader ] ) ) {
                $rHeaders[ $stHeader ] = [];
            }
            $rHeaders[ $stHeader ][] = $value;
        }
        return new self( $rHeaders );
    }


    /**
     * @param string $i_stHeader The header name to get.
     * @return list<string> A list of values for the header.
     */
    public function get( string $i_stHeader ) : array {
        $i_stHeader = strtolower( trim( $i_stHeader ) );
        if ( ! array_key_exists( $i_stHeader, $this->rHeaders ) ) {
            return [];
        }
        return $this->rHeaders[ $i_stHeader ];
    }


    /**
     * @return array<string, string|list<string>> The list of headers.
     */
    public function getAll() : array {
        return $this->rHeaders;
    }


    /**
     * @param string $i_stHeader The header name to get.
     * @return string The header value, with multiple values joined by a comma.
     */
    public function getLine( string $i_stHeader ) : string {
        $rHeader = $this->get( $i_stHeader );
        if ( empty( $rHeader ) ) {
            return '';
        }
        return implode( ', ', $rHeader );
    }


    public function getValue( string $i_stHeader, int|string $i_index, string $i_stDefault = '' ) : string {
        $rHeader = $this->getValues( $i_stHeader );
        return $rHeader[ $i_index ] ?? $i_stDefault;
    }


    /**
     * @param string $i_stHeader The header name to get.
     * @return array<int|string, string> The parsed values for the header.
     *
     * This will parse out components of a header value splitting on
     * semicolons. See Headers::parseValue().
     */
    public function getValues( string $i_stHeader ) : array {
        if ( ! isset( $this->rValueCache[ $i_stHeader ] ) ) {
            $rHeader = $this->get( $i_stHeader );
            $rOut = [];
            foreach ( $rHeader as $stValue ) {
                $rOut = array_merge( $rOut, Headers::parseValue( $stValue ) );
            }
            $this->rValueCache[ $i_stHeader ] = $rOut;
        }
        return $this->rValueCache[ $i_stHeader ];
    }


    public function has( string $i_stHeader ) : bool {
        $i_stHeader = strtolower( trim( $i_stHeader ) );
        return isset( $this->rHeaders[ $i_stHeader ] );
    }


    public function hasValue( string $i_stHeader, int|string $i_index ) : bool {
        $rHeader = $this->getValues( $i_stHeader );
        return isset( $rHeader[ $i_index ] );
    }


    /**
     * @param string $i_stHeader
     * @param string|list<string> $i_value
     * @return self
     */
    public function with( string $i_stHeader, array|string $i_value ) : self {
        $i_stHeader = strtolower( trim( $i_stHeader ) );
        if ( ! is_array( $i_value ) ) {
            $i_value = [ $i_value ];
        }
        $rHeaders = array_merge( [], $this->rHeaders );
        $rHeaders[ $i_stHeader ] = $i_value;
        return new self( $rHeaders );
    }


    /**
     * @param string $i_stHeader The header name to add.
     * @param string|list<string> $i_value The value(s) to add.
     * @return self A new instance with the header added.
     */
    public function withAdded( string $i_stHeader, array|string $i_value ) : self {
        $i_stHeader = strtolower( trim( $i_stHeader ) );
        if ( ! is_array( $i_value ) ) {
            $i_value = [ $i_value ];
        }
        $rHeaders = array_merge( [], $this->rHeaders );
        if ( ! isset( $rHeaders[ $i_stHeader ] ) ) {
            $rHeaders[ $i_stHeader ] = [];
        }
        $rHeaders[ $i_stHeader ] = array_merge( $rHeaders[ $i_stHeader ], $i_value );
        return new self( $rHeaders );
    }


    /**
     * @param iterable<string, string|list<string>> $i_itHeaders New headers to add.
     * @param bool $i_bAdd If true, add the headers to the existing ones,
     *                      otherwise replace them.
     */
    public function withIterable( iterable $i_itHeaders, bool $i_bAdd = false ) : self {
        $rHeaders = array_merge( [], $this->rHeaders );
        foreach ( $i_itHeaders as $stHeader => $value ) {
            $stHeader = strtolower( trim( $stHeader ) );
            if ( $i_bAdd ) {
                if ( ! isset( $rHeaders[ $stHeader ] ) ) {
                    $rHeaders[ $stHeader ] = [];
                }
                $rHeaders[ $stHeader ] = array_merge( $rHeaders[ $stHeader ], (array) $value );
            } else {
                $rHeaders[ $stHeader ] = (array) $value;
            }
        }
        return new self( $rHeaders );
    }


    public function without( string $i_stHeader ) : self {
        $i_stHeader = strtolower( trim( $i_stHeader ) );
        $rHeaders = $this->rHeaders;
        unset( $rHeaders[ $i_stHeader ] );
        return new self( $rHeaders );
    }


}
