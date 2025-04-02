<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests;


use JDWX\PsrHttp\ServerRequest;
use JDWX\PsrHttp\UploadedFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ServerRequest::class )]
final class ServerRequestTest extends TestCase {


    public function testFromBodyForMultipart() : void {
        $stBody = <<<EOT
--BOUNDARY
Content-Disposition: form-data; name="foo"

bar
--BOUNDARY
Content-Disposition: form-data; name="baz"

qux
--BOUNDARY
Content-Disposition: form-data; name="quux"; filename="test.txt"

QUUX_CONTENT
--BOUNDARY--
EOT;
        $rHeaders = [
            'content-type' => [ 'multipart/form-data; boundary=BOUNDARY' ],
        ];
        $ssr = ServerRequest::fromBody( $stBody, i_rHeaders: $rHeaders );
        $rParams = $ssr->getParsedBody();
        assert( is_array( $rParams ) );
        self::assertSame( 'bar', $rParams[ 'foo' ] );
        self::assertSame( 'qux', $rParams[ 'baz' ] );
        self::assertArrayNotHasKey( 'quux', $rParams );
        $file = $ssr->getUploadedFiles()[ 'quux' ];
        self::assertSame( "QUUX_CONTENT\n", $file->getStream()->__toString() );
    }


    public function testFromBodyForUrlEncoded() : void {
        $stBody = 'foo=bar&baz=qux';
        $rHeaders = [
            'content-type' => [ 'application/x-www-form-urlencoded' ],
        ];
        $ssr = ServerRequest::fromBody( $stBody, i_rHeaders: $rHeaders );
        $rParams = $ssr->getParsedBody();
        assert( is_array( $rParams ) );
        self::assertSame( 'bar', $rParams[ 'foo' ] );
        self::assertSame( 'qux', $rParams[ 'baz' ] );
        self::assertArrayNotHasKey( 'quux', $rParams );
        self::assertEmpty( $ssr->getUploadedFiles() );
    }


    public function testGetAttribute() : void {
        $ssr = new ServerRequest( rAttributes: [ 'foo' => 'bar' ] );
        self::assertSame( 'bar', $ssr->getAttribute( 'foo' ) );
        self::assertSame( 'qux', $ssr->getAttribute( 'baz', 'qux' ) );
        self::assertNull( $ssr->getAttribute( 'quux' ) );
    }


    public function testGetAttributes() : void {
        $ssr = new ServerRequest( rAttributes: [ 'foo' => 'bar', 'baz' => 'qux' ] );
        self::assertSame( [ 'foo' => 'bar', 'baz' => 'qux' ], $ssr->getAttributes() );
    }


    public function testGetCookieParams() : void {
        $ssr = new ServerRequest( rCookieParams: [ 'foo' => 'bar' ] );
        self::assertSame( [ 'foo' => 'bar' ], $ssr->getCookieParams() );
    }


    public function testGetParsedBody() : void {
        $ssr = new ServerRequest( xParsedBody: [ 'foo' => 'bar' ] );
        self::assertSame( [ 'foo' => 'bar' ], $ssr->getParsedBody() );
    }


    public function testGetQueryParams() : void {
        $ssr = new ServerRequest( rQueryParams: [ 'foo' => 'bar' ] );
        self::assertSame( [ 'foo' => 'bar' ], $ssr->getQueryParams() );
    }


    public function testGetServerParams() : void {
        $ssr = new ServerRequest( rServerParams: [ 'foo' => 'bar' ] );
        self::assertSame( [ 'foo' => 'bar' ], $ssr->getServerParams() );
    }


    public function testGetUploadedFiles() : void {
        $f1 = UploadedFile::fromString( 'TEST_CONTENT', i_nstClientFilename: 'foo.txt' );
        $f2 = UploadedFile::fromString( 'TEST_CONTENT', i_nstClientFilename: 'bar.txt' );
        $ssr = new ServerRequest( rUploadedFiles: [ 'foo' => $f1, 'bar' => $f2 ] );
        $r = $ssr->getUploadedFiles();
        self::assertSame( 'foo.txt', $r[ 'foo' ]->getClientFilename() );
        self::assertSame( 'bar.txt', $r[ 'bar' ]->getClientFilename() );
    }


    public function testWithAttribute() : void {
        $ssr = new ServerRequest( rAttributes: [ 'foo' => 'bar' ] );
        $ssr2 = $ssr->withAttribute( 'baz', 'qux' );
        self::assertSame( [ 'foo' => 'bar', 'baz' => 'qux' ], $ssr2->getAttributes() );
        self::assertSame( [ 'foo' => 'bar' ], $ssr->getAttributes() );
    }


    public function testWithCookieParams() : void {
        $ssr = new ServerRequest( rCookieParams: [ 'foo' => 'bar' ] );
        $ssr2 = $ssr->withCookieParams( [ 'baz' => 'qux' ] );
        self::assertSame( [ 'baz' => 'qux' ], $ssr2->getCookieParams() );
        self::assertSame( [ 'foo' => 'bar' ], $ssr->getCookieParams() );
    }


    public function testWithParsedBody() : void {
        $ssr = new ServerRequest( xParsedBody: [ 'foo' => 'bar' ] );
        $ssr2 = $ssr->withParsedBody( [ 'baz' => 'qux' ] );
        self::assertSame( [ 'baz' => 'qux' ], $ssr2->getParsedBody() );
        self::assertSame( [ 'foo' => 'bar' ], $ssr->getParsedBody() );
    }


    public function testWithQueryParams() : void {
        $ssr = new ServerRequest( rQueryParams: [ 'foo' => 'bar' ] );
        $ssr2 = $ssr->withQueryParams( [ 'baz' => [ 'qux', 'quux' ] ] );
        self::assertSame( [ 'baz' => [ 'qux', 'quux' ] ], $ssr2->getQueryParams() );
        self::assertSame( [ 'foo' => 'bar' ], $ssr->getQueryParams() );
    }


    public function testWithUploadedFiles() : void {
        $f1 = UploadedFile::fromString( 'TEST_CONTENT' );
        $f1->nstClientFilename = 'foo';
        $f2 = UploadedFile::fromString( 'TEST_CONTENT' );
        $f2->nstClientFilename = 'bar';
        $ssr = new ServerRequest();
        $ssr = $ssr->withUploadedFiles( [ 'foo' => $f1 ] );
        self::assertSame( [ 'foo' => $f1 ], $ssr->getUploadedFiles() );
        $ssr = $ssr->withUploadedFiles( [ 'bar' => $f2 ] );
        self::assertSame( [ 'bar' => $f2 ], $ssr->getUploadedFiles() );
        $ssr = $ssr->withUploadedFiles( [ 'foo' => $f1, 'bar' => $f2 ] );
        self::assertSame( [ 'foo' => $f1, 'bar' => $f2 ], $ssr->getUploadedFiles() );
    }


    public function testWithoutAttribute() : void {
        $ssr = new ServerRequest( rAttributes: [ 'foo' => 'bar', 'baz' => 'qux' ] );
        $ssr2 = $ssr->withoutAttribute( 'baz' );
        self::assertSame( [ 'foo' => 'bar' ], $ssr2->getAttributes() );
        self::assertSame( [ 'foo' => 'bar', 'baz' => 'qux' ], $ssr->getAttributes() );
    }


}