<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Utility;


/**
 * The structure of the $_FILES array is utterly bonkers.
 *
 * The simplest case is one file with a simple field name like 'foo.'
 *
 * That looks like:
 *
 * $_FILES = [ 'foo' => [
 *    'name' => 'filename.txt',
 *   'type' => 'text/plain',
 *   'tmp_name' => '/tmp/php1234.tmp',
 *  'error' => 0,
 *  'size' => 1234,
 * ]];
 *
 * PHP allows nesting of the $_FILES array using field names
 * like "foo[bar][baz]". In such cases, the $_FILES array
 * will look like:
 *
 * $_FILES = [
 *   'foo' => [
 *    'name' => [ 'bar' => [ 'baz' => 'filename.txt' ] ],
 *    'type' => [ 'bar' => [ 'baz' => 'text/plain' ] ],
 *    'tmp_name' => [ 'bar' => [ 'baz' => '/tmp/php1234.tmp' ] ],
 *    'error' => [ 'bar' => [ 'baz' => 0 ] ],
 *    'size' => [ 'bar' => [ 'baz' => 1234 ] ],
 *  ];
 *
 * But maybe you allowed multiple files to be uploaded with
 * a name like "foo[bar][baz][]"! Now the $_FILES array looks like:
 *
 * $_FILES = [
 *  'foo' => [
 *   'name' => [ 'bar' => [ 'baz' => [ 0 => 'filename.txt', 1 => 'filename2.txt' ] ] ],
 *   'type' => [ 'bar' => [ 'baz' => [ 0 => 'text/plain', 1 => 'text/plain' ] ] ],
 *   'tmp_name' => [ 'bar' => [ 'baz' => [ 0 => '/tmp/php1234.tmp', 1 => '/tmp/php1235.tmp' ] ] ],
 *   'error' => [ 'bar' => [ 'baz' => [ 0 => 0, 1 => 0 ] ] ],
 *   'size' => [ 'bar' => [ 'baz' => [ 0 => 1234, 1 => 1235 ] ] ],
 *  ];
 *
 * So, in order to figure out what the heck has been uploaded, we have to creep
 * down the $_FILES array, checking for the presence of arrays at each level.
 * When we find something that isn't an array, we then have to look back at
 * the previous level to see if it was a numeric or string key. If it was a numeric
 * key, we know that we have multiple files. If it was a string key, we know
 * that we have a single file (for this name).
 *
 * The purpose of this class is to walk through a $_FILES-like array, perform
 * one operation per uploaded file, and return the results of those operations
 * in a format consistent with the input.
 *
 * I.e., we are mapping from:
 *
 *  $_FILES[ tag ][ predefined_key ][ tag... ][ index? ] => value
 *
 * to:
 *  $result[ tag ][ tag... ][ index? ] => mapped_value
 */
abstract class AbstractFilesAnalyzer {


    /**
     * @param array<string, array<string, mixed>> $i_rFiles
     * @return array<string, mixed>
     */
    public function map( array $i_rFiles ) : array {
        $rOut = [];
        foreach ( $i_rFiles as $stTag => $rRest ) {
            $names = $rRest[ 'name' ] ?? null;
            $types = $rRest[ 'type' ] ?? null;
            $tmpNames = $rRest[ 'tmp_name' ] ?? null;
            $errors = $rRest[ 'error' ] ?? null;
            $sizes = $rRest[ 'size' ] ?? null;
            $x = $this->mapDown( $names, $types, $tmpNames, $errors, $sizes );
            if ( ! is_null( $x ) ) {
                $rOut[ $stTag ] = $x;
            }
        }
        return $rOut;
    }


    /**
     * @param mixed[]|string|null $names
     * @param mixed[]|string|null $types
     * @param mixed[]|string|null $tmpNames
     * @param int|mixed[] $errors
     * @param int|mixed[]|null $sizes
     */
    protected function mapDown( array|string|null $names,
                                array|string|null $types,
                                array|string|null $tmpNames,
                                array|int         $errors,
                                array|int|null    $sizes ) : mixed {
        if ( is_int( $errors ) ) {
            return $this->process( $names, $types, $tmpNames, $errors, $sizes );
        }
        assert( is_array( $names ) );
        assert( is_array( $types ) );
        assert( is_array( $tmpNames ) );
        assert( is_array( $sizes ) );
        return $this->mapDownArray( $names, $types, $tmpNames, $errors, $sizes );
    }


    /**
     * @param mixed[] $names
     * @param mixed[] $types
     * @param mixed[] $tmpNames
     * @param mixed[] $errors
     * @param mixed[] $sizes
     * @return mixed[]
     */
    protected function mapDownArray( array $names,
                                     array $types,
                                     array $tmpNames,
                                     array $errors,
                                     array $sizes ) : array {
        $rOut = [];
        foreach ( array_keys( $errors ) as $key ) {
            $x = $this->mapDown( $names[ $key ], $types[ $key ], $tmpNames[ $key ],
                $errors[ $key ], $sizes[ $key ] );
            if ( is_null( $x ) ) {
                continue;
            }
            if ( is_int( $key ) ) {
                $rOut[] = $x;
            } else {
                $rOut[ $key ] = $x;
            }
        }
        return $rOut;
    }


    abstract protected function process(
        ?string $i_nstClientFilename,
        ?string $i_nstClientMediaType,
        ?string $i_nstTmpName,
        int     $i_uError,
        ?int    $i_nuSize
    ) : mixed;


}
