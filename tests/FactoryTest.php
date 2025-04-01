<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests;


use InvalidArgumentException;
use JDWX\PsrHttp\Factory;
use JDWX\PsrHttp\StringStream;
use JDWX\PsrHttp\Utility\TempFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;


#[CoversClass( Factory::class )]
final class FactoryTest extends TestCase {


    public function testCreateRequest() : void {
        $fac = new Factory();
        $req = $fac->createRequest( 'GET', '/' );
        self::assertInstanceOf( RequestInterface::class, $req );
        self::assertSame( 'GET', $req->getMethod() );
        self::assertSame( '/', strval( $req->getUri() ) );
    }


    public function testCreateResponse() : void {
        $fac = new Factory();
        $rsp = $fac->createResponse( 404, 'Not Found' );
        self::assertInstanceOf( ResponseInterface::class, $rsp );
        self::assertSame( 404, $rsp->getStatusCode() );
        self::assertSame( 'Not Found', $rsp->getReasonPhrase() );
    }


    public function testCreateServerRequest() : void {
        $fac = new Factory();
        $req = $fac->createServerRequest( 'GET', '/' );
        self::assertInstanceOf( RequestInterface::class, $req );
        self::assertSame( 'GET', $req->getMethod() );
        self::assertSame( '/', strval( $req->getUri() ) );
    }


    public function testCreateStream() : void {
        $fac = new Factory();
        $stream = $fac->createStream( 'TEST_CONTENT' );
        self::assertInstanceOf( StreamInterface::class, $stream );
        self::assertSame( 'TEST_CONTENT', $stream->getContents() );
    }


    public function testCreateStreamFile() : void {
        $fac = new Factory();
        $stContent = 'TEST_CONTENT_FILE';
        $stFilename = tempnam( sys_get_temp_dir(), 'test' );
        file_put_contents( $stFilename, $stContent );
        $stream = $fac->createStreamFromFile( $stFilename );
        self::assertInstanceOf( StreamInterface::class, $stream );
        self::assertSame( $stContent, $stream->getContents() );
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        @unlink( $stFilename );
    }


    public function testCreateStreamFileForNoFile() : void {
        $fac = new Factory();
        $this->expectException( RuntimeException::class );
        $fac->createStreamFromFile( '/no/such/file' );
    }


    public function testCreateStreamResource() : void {
        $tmp = new TempFile( 'TEST_CONTENT_FILE' );
        $fac = new Factory();
        $stream = $fac->createStreamFromResource( $tmp->fopen( 'r' ) );
        self::assertInstanceOf( StreamInterface::class, $stream );
        self::assertSame( 'TEST_CONTENT_FILE', $stream->getContents() );
    }


    /** @suppress PhanTypeMismatchArgumentProbablyReal */
    public function testCreateStreamResourceForInvalidType() : void {
        $fac = new Factory();
        $this->expectException( InvalidArgumentException::class );
        /**
         * @noinspection PhpParamsInspection
         * @phpstan-ignore-next-line
         */
        $fac->createStreamFromResource( 'invalid_resource' );
    }


    public function testCreateUploadedFile() : void {
        $stContent = 'TEST_CONTENT';
        $stream = new StringStream( $stContent );
        $fac = new Factory();
        $file = $fac->createUploadedFile( $stream );
        self::assertSame( $stContent, $file->getStream()->getContents() );
    }


    public function testCreateUri() : void {
        $fac = new Factory();
        $stUri = 'https://example.com/foo?bar=1&baz=2';
        $uri = $fac->createUri( $stUri );
        self::assertInstanceOf( UriInterface::class, $uri );
        self::assertSame( $stUri, strval( $uri ) );
    }


}
