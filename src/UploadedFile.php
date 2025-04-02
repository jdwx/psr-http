<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp;


use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;


class UploadedFile implements UploadedFileInterface {


    public string $stBody;

    public bool $bMoved = false;


    public function __construct( public string  $stFileName,
                                 public int     $iError = \UPLOAD_ERR_OK,
                                 public ?string $nstClientFilename = null,
                                 public ?string $nstClientMediaType = null,
                                 public bool    $i_bDeleteOnDestruct = false ) {}


    /**
     * @param string $i_stTag The name of the file input field.
     * @param int|null $i_nuIndex The index of the file in the array (if applicable).
     * @param array<string, mixed[]>|null $i_nrFiles The $_FILES array (if applicable).
     */
    public static function fromFiles( string $i_stTag, ?int $i_nuIndex = null, ?array $i_nrFiles = null ) : ?static {
        $i_nrFiles ??= $_FILES;
        if ( ! isset( $i_nrFiles[ $i_stTag ] ) ) {
            # This is not a file upload.
            return null;
        }
        if ( is_int( $i_nuIndex ) ) {
            [ $nstTmpName, $nuError, $nstClientFileName, $nstType ] = static::fromFilesMultiple(
                $i_nuIndex, $i_nrFiles[ $i_stTag ]
            );
        } else {
            [ $nstTmpName, $nuError, $nstClientFileName, $nstType ] = static::fromFilesSingle(
                $i_nrFiles[ $i_stTag ]
            );
        }
        if ( is_null( $nstTmpName ) || ! file_exists( $nstTmpName ) ) {
            return null;
        }
        /** @phpstan-ignore new.static */
        return new static( $nstTmpName, $nuError, $nstClientFileName, $nstType, false );
    }


    public static function fromStream( StreamInterface $i_stream,
                                       int             $i_uError = \UPLOAD_ERR_OK,
                                       ?string         $i_nstClientFilename = null,
                                       ?string         $i_nstClientMediaType = null ) : static {
        $i_nstClientMediaType ??= 'text/plain';
        if ( is_null( $i_nstClientFilename ) ) {
            $i_nstClientFilename = uniqid( 'clientName_', true );
        }
        $stFilename = tempnam( sys_get_temp_dir(), 'upload_' );
        $f = fopen( $stFilename, 'w' );
        assert( is_resource( $f ) ); # This assertion is not testable.
        while ( ! $i_stream->eof() ) {
            $st = $i_stream->read( 8192 );
            $uWrite = fwrite( $f, $st );
            assert( is_int( $uWrite ) && $uWrite == strlen( $st ) ); # This assertion is not testable.
        }
        /** @phpstan-ignore new.static */
        return new static(
            $stFilename,
            $i_uError, $i_nstClientFilename,
            $i_nstClientMediaType, true
        );
    }


    public static function fromString( string  $i_stBody,
                                       int     $i_uError = \UPLOAD_ERR_OK,
                                       ?string $i_nstClientFilename = null,
                                       string  $i_stClientMediaType = 'text/plain' ) : static {
        if ( is_null( $i_nstClientFilename ) ) {
            $i_nstClientFilename = uniqid( 'clientName_', true );
        }
        $stFilename = tempnam( sys_get_temp_dir(), 'upload_' );
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $buSize = @file_put_contents( $stFilename, $i_stBody );
        assert( is_int( $buSize ) && $buSize == strlen( $i_stBody ) ); # Assertion is not testable.
        /** @phpstan-ignore new.static */
        return new static(
            $stFilename,
            $i_uError, $i_nstClientFilename,
            $i_stClientMediaType, true
        );
    }


    /**
     * @param array<string, array<int, string|int>> $i_rFile
     * @return list<int|string|null>|null
     */
    protected static function fromFilesMultiple( int $i_uIndex, array $i_rFile ) : ?array {
        if ( ! is_array( $i_rFile[ 'error' ] ) ) {
            # This is a single file upload, not multiple.
            return null;
        }
        if ( ! isset( $i_rFile[ 'error' ][ $i_uIndex ] ) ) {
            # This is a wrong index.
            return null;
        }
        $nstClientFileName = $i_rFile[ 'name' ][ $i_uIndex ] ?? null;
        $nstType = $i_rFile[ 'type' ][ $i_uIndex ] ?? null;
        $nstTmpName = $i_rFile[ 'tmp_name' ][ $i_uIndex ] ?? null;
        $nuError = $i_rFile[ 'error' ][ $i_uIndex ];
        return [ $nstTmpName, $nuError, $nstClientFileName, $nstType ];
    }


    /**
     * @param array<string, string|int> $i_rFile
     * @return list<int|string|null>|null
     */
    protected static function fromFilesSingle( array $i_rFile ) : ?array {
        $nstClientFileName = $i_rFile[ 'name' ] ?? null;
        $nstType = $i_rFile[ 'type' ] ?? null;
        $nstTmpName = $i_rFile[ 'tmp_name' ] ?? null;
        $nuError = $i_rFile[ 'error' ] ?? null;
        return [ $nstTmpName, $nuError, $nstClientFileName, $nstType ];
    }


    public function __destruct() {
        if ( $this->i_bDeleteOnDestruct && ! $this->bMoved ) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection */
            @unlink( $this->stFileName );
        }
    }


    public function getClientFilename() : ?string {
        return $this->nstClientFilename;
    }


    public function getClientMediaType() : ?string {
        return $this->nstClientMediaType;
    }


    public function getError() : int {
        return $this->iError;
    }


    public function getSize() : int {
        return strlen( $this->stBody );
    }


    public function getStream() : StreamInterface {
        if ( $this->bMoved ) {
            throw new RuntimeException( 'File has already been moved' );
        }
        return new FileStream( $this->stFileName );
    }


    public function moveTo( string $targetPath ) : void {
        if ( $this->bMoved ) {
            throw new RuntimeException( 'File has already been moved' );
        }

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $bi = @file_put_contents( $targetPath, $this->stBody );
        if ( false === $bi ) {
            throw new RuntimeException( "Failed to move file to {$targetPath}" );
        }
        $this->bMoved = true;
    }


}