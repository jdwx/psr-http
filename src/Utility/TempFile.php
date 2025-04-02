<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Utility;


class TempFile implements \Stringable {


    private string $stFilename;


    public function __construct( string $i_stContent ) {
        $stFilename = tempnam( sys_get_temp_dir(), 'tmp' );
        assert( is_string( $stFilename ) );
        $this->stFilename = $stFilename;
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $buSize = @file_put_contents( $this->stFilename, $i_stContent );
        assert( $buSize === strlen( $i_stContent ) );
    }


    public function __destruct() {
        if ( file_exists( $this->stFilename ) ) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            @unlink( $this->stFilename );
        }
    }


    public function __toString() : string {
        return $this->stFilename;
    }


    /** @return resource */
    public function fopen( string $i_stMode ) {
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $bf = @fopen( $this->stFilename, $i_stMode );
        assert( is_resource( $bf ) );
        return $bf;
    }


}
