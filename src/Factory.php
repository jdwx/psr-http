<?php


declare( strict_types = 1 );


namespace JDWX\Psr7;


use InvalidArgumentException;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;


class Factory implements RequestFactoryInterface, ResponseFactoryInterface, UriFactoryInterface,
    StreamFactoryInterface, ServerRequestFactoryInterface, UploadedFileFactoryInterface {


    public function createRequest( string $method, $uri ) : RequestInterface {
        return new Request( stMethod: $method, i_uri: $uri );
    }


    public function createResponse( int $code = 200, string $reasonPhrase = '' ) : ResponseInterface {
        return new Response( uStatusCode: $code, stReasonPhrase: $reasonPhrase );
    }


    public function createServerRequest( string $method, $uri, array $serverParams = [] ) : ServerRequestInterface {
        return new ServerRequest( stMethod: $method, i_uri: $uri, rServerParams: $serverParams );
    }


    public function createStream( string $content = '' ) : StreamInterface {
        return new StringStream( $content );
    }


    public function createStreamFromFile( string $filename, string $mode = 'r' ) : StreamInterface {
        return new FileStream( $filename );
    }


    /** @param resource $resource */
    public function createStreamFromResource( $resource ) : StreamInterface {
        if ( ! is_resource( $resource ) ) {
            throw new InvalidArgumentException( 'Invalid resource provided.' );
        }
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $bst = @stream_get_contents( $resource );
        if ( false === $bst ) {
            # I don't know how to test this.
            // @codeCoverageIgnoreStart
            throw new RuntimeException( 'Failed to read resource.' );
            // @codeCoverageIgnoreEnd
        }
        return $this->createStream( $bst );
    }


    public function createUploadedFile( StreamInterface $stream, ?int $size = null,
                                        int             $error = \UPLOAD_ERR_OK, ?string $clientFilename = null,
                                        ?string         $clientMediaType = null ) : UploadedFileInterface {
        return UploadedFile::fromStream( $stream, $error, $clientFilename, $clientMediaType );
    }


    public function createUri( string $uri = '' ) : UriInterface {
        return Uri::fromString( $uri );
    }


}
