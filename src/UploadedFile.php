<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp;


use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;


class UploadedFile implements UploadedFileInterface {


    public bool $bMoved = false;


    public function __construct( public string  $stFileName,
                                 public ?int    $nuSize = 0,
                                 public int     $iError = \UPLOAD_ERR_OK,
                                 public ?string $nstClientFilename = null,
                                 public ?string $nstClientMediaType = null,
                                 public bool    $i_bDeleteOnDestruct = false ) {}


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
        $uSize = 0;
        while ( ! $i_stream->eof() ) {
            $st = $i_stream->read( 8192 );
            $uWrite = fwrite( $f, $st );
            assert( is_int( $uWrite ) && $uWrite == strlen( $st ) ); # This assertion is not testable.
            $uSize += $uWrite;
        }
        /** @phpstan-ignore new.static */
        return new static(
            $stFilename, $uSize,
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
            $stFilename, strlen( $i_stBody ),
            $i_uError, $i_nstClientFilename,
            $i_stClientMediaType, true
        );
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


    public function getSize() : ?int {
        return $this->nuSize;
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
        if ( ! @rename( $this->stFileName, $targetPath ) ) {
            throw new RuntimeException( "Failed to move file to {$targetPath}" );
        }
        $this->bMoved = true;
    }


}