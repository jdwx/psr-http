<?php


declare( strict_types = 1 );


use JDWX\Psr7\Request;
use JDWX\Psr7\Uri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Request::class )]
final class RequestTest extends TestCase {


    public function testConstructForUri() : void {
        $st = 'https://example.com/a/b?foo=1&bar=baz#qux';
        $uri = Uri::fromString( $st );
        $srq = new Request( i_uri: $uri );
        self::assertSame( strval( $uri ), strval( $srq->uri ) );
    }


    public function testConstructForUriString() : void {
        $st = 'https://example.com/a/b?foo=1&bar=baz#qux';
        $srq = new Request( i_uri: $st );
        self::assertSame( $st, strval( $srq->uri ) );
    }


    public function testGetMethod() : void {
        $request = new Request();
        self::assertSame( 'GET', $request->getMethod() );

        $request->stMethod = 'POST';
        self::assertSame( 'POST', $request->getMethod() );
    }


    public function testGetRequestTarget() : void {
        $request = new Request();
        self::assertSame( '/', $request->getRequestTarget() );

        $request->uri = Uri::fromString( 'https://example.com/a/b?foo=1&bar=baz' );
        self::assertSame( '/a/b?foo=1&bar=baz', $request->getRequestTarget() );

        $request->nstRequestTarget = '/c/d';
        self::assertSame( '/c/d', $request->getRequestTarget() );
    }


    public function testGetUri() : void {
        $request = new Request();
        self::assertSame( '/', $request->getUri()->getPath() );

        $request->uri = Uri::fromString( 'https://example.com/a/b?foo=1&bar=baz' );
        self::assertSame( '/a/b', $request->getUri()->getPath() );
    }


    public function testWithMethod() : void {
        $req = new Request();
        self::assertSame( 'GET', $req->stMethod );
        $req = $req->withMethod( 'POST' );
        self::assertSame( 'POST', $req->stMethod );
    }


    public function testWithRequestTarget() : void {
        $req = new Request();
        self::assertNull( $req->nstRequestTarget );
        $req = $req->withRequestTarget( '/a/b' );
        self::assertSame( '/a/b', $req->nstRequestTarget );
    }


    public function testWithUri() : void {
        $req = new Request();
        self::assertNull( $req->uri );
        $uri = Uri::fromString( 'https://example.com/a/b?foo=1&bar=baz#qux' );
        $req = $req->withUri( $uri );
        self::assertSame( strval( $uri ), strval( $req->uri ) );
    }


}
