<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests;


use JDWX\PsrHttp\Request;
use JDWX\PsrHttp\Uri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Request::class )]
final class RequestTest extends TestCase {


    public function testConstructForUri() : void {
        $st = 'https://example.com/a/b?foo=1&bar=baz#qux';
        $uri = Uri::fromString( $st );
        $srq = new Request( i_uri: $uri );
        self::assertSame( strval( $uri ), strval( $srq->getUri() ) );
    }


    public function testConstructForUriString() : void {
        $st = 'https://example.com/a/b?foo=1&bar=baz#qux';
        $srq = new Request( i_uri: $st );
        self::assertSame( $st, strval( $srq->getUri() ) );
    }


    public function testGetMethod() : void {
        $req = new Request();
        $req2 = $req->withMethod( 'POST' );
        self::assertSame( 'GET', $req->getMethod() );
        self::assertSame( 'POST', $req2->getMethod() );
    }


    public function testGetRequestTarget() : void {
        $req = new Request();
        $req2 = $req->withUri( Uri::fromString( 'https://example.com/a/b?foo=1&bar=baz' ) );
        $req3 = $req2->withRequestTarget( '/c/d' );
        self::assertSame( '/', $req->getRequestTarget() );
        self::assertSame( '/a/b?foo=1&bar=baz', $req2->getRequestTarget() );
        self::assertSame( '/c/d', $req3->getRequestTarget() );
    }


    public function testGetUri() : void {
        $uri = Uri::fromString( 'https://example.com/a/b?foo=1&bar=baz#qux' );
        $request = new Request( i_uri: $uri );
        self::assertSame( strval( $uri ), strval( $request->getUri() ) );
    }


    public function testWithMethod() : void {
        $req = new Request();
        $req2 = $req->withMethod( 'POST' );
        self::assertSame( 'GET', $req->getMethod() );
        self::assertSame( 'POST', $req2->getMethod() );
    }


    public function testWithRequestTarget() : void {
        $req = new Request();
        $req2 = $req->withRequestTarget( '/a/b' );
        self::assertSame( '/', $req->getRequestTarget() );
        self::assertSame( '/a/b', $req2->getRequestTarget() );
    }


    public function testWithUri() : void {
        $req = new Request();
        $uri = Uri::fromString( 'https://example.com/a/b?foo=1&bar=baz#qux' );
        $req2 = $req->withUri( $uri );
        self::assertSame( '/', strval( $req->getUri() ) );
        self::assertSame( strval( $uri ), strval( $req2->getUri() ) );
    }


}
