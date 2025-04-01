<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests;


use InvalidArgumentException;
use JDWX\PsrHttp\Uri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Uri::class )]
final class UriTest extends TestCase {


    public function testConstructForFullUri() : void {
        self::expectException( InvalidArgumentException::class );
        $x = new Uri( 'https://example.com/' );
        unset( $x );
    }


    public function testFromForArray() : void {
        $uri = Uri::from( [
            'port' => '12345',
        ] );
        self::assertSame( 12345, $uri->nuPort );
    }


    public function testFromForString() : void {
        $uri = Uri::from( 'https://foo:bar@example.com:12345/baz/qux/?quux=1&corge=2#grault' );
        self::assertSame( 'https', $uri->stScheme );
        self::assertSame( 'example.com', $uri->stHost );
        self::assertSame( 12345, $uri->nuPort );
        self::assertSame( 'foo', $uri->stUser );
        self::assertSame( 'bar', $uri->stPassword );
        self::assertSame( '/baz/qux/', $uri->stPath );
        self::assertSame( 'quux=1&corge=2', $uri->stQuery );
        self::assertSame( 'grault', $uri->stFragment );

        $uri = Uri::from( 'https://example.com/foo/bar?baz=1&qux=2#quux' );
        self::assertSame( 'https', $uri->stScheme );
        self::assertSame( '', $uri->stUser ); // No user info specified
        self::assertSame( '', $uri->stPassword ); // No password specified
        self::assertSame( 'example.com', $uri->stHost );
        self::assertNull( $uri->nuPort ); // No port specified
        self::assertSame( '/foo/bar', $uri->stPath ); // Path should be '/foo/bar'
        self::assertSame( 'baz=1&qux=2', $uri->stQuery );
        self::assertSame( 'quux', $uri->stFragment );

        foreach ( [ 'https://example.com', 'https://example.com/' ] as $stUri ) {
            $uri = Uri::from( $stUri );
            self::assertSame( 'https', $uri->stScheme );
            self::assertSame( 'example.com', $uri->stHost );
            self::assertNull( $uri->nuPort ); // No port specified
            self::assertSame( '', $uri->stUser ); // No user info specified
            self::assertSame( '', $uri->stPassword ); // No password specified
            self::assertSame( '/', $uri->stPath ); // No path specified
            self::assertSame( '', $uri->stQuery ); // No query specified
            self::assertSame( '', $uri->stFragment ); // No fragment specified
        }


        self::expectException( InvalidArgumentException::class );
        Uri::from( 'https:////example.com' );

    }


    public function testFromForUri() : void {
        $uri = Uri::from( 'https://foo:bar@example.com:12345/baz/qux/?quux=1&corge=2#grault' );
        $uri2 = Uri::from( $uri );
        self::assertSame( strval( $uri ), strval( $uri2 ) );

        $uri = Uri::from( 'https://example.com/' );
        $uri2 = Uri::from( $uri );
        self::assertSame( strval( $uri ), strval( $uri2 ) );

        $uri = Uri::from( '/foo/bar/baz' );
        $uri2 = Uri::from( $uri );
        self::assertSame( strval( $uri ), strval( $uri2 ) );
    }


    public function testGetAuthority() : void {
        $uri = new Uri();
        self::assertSame( '', $uri->getAuthority() );

        $uri = new Uri( stUser: 'foo' );
        self::assertSame( 'foo@', $uri->getAuthority() );

        $uri = new Uri( stPassword: 'bar' );
        self::assertSame( '', $uri->getAuthority() );

        $uri = new Uri( stHost: 'baz' );
        self::assertSame( 'baz', $uri->getAuthority() );

        $uri = new Uri( nuPort: 8080 );
        self::assertSame( '', $uri->getAuthority() );

        $uri = new Uri( stHost: 'baz', nuPort: 8080 );
        self::assertSame( 'baz:8080', $uri->getAuthority() );

        $uri = new Uri( stUser: 'foo', stPassword: 'bar' );
        self::assertSame( 'foo:bar@', $uri->getAuthority() );

        $uri = new Uri( stUser: 'foo', stPassword: 'bar', stHost: 'baz', nuPort: 8080 );
        self::assertSame( 'foo:bar@baz:8080', $uri->getAuthority() );

        $uri = new Uri( stScheme: 'HTTP', stUser: 'foo', stHost: 'baz', nuPort: 80 );
        self::assertSame( 'foo@baz', $uri->getAuthority() );

        $uri = new Uri( stScheme: 'HtTpS', stUser: 'foo', stHost: 'baz', nuPort: 443 );
        self::assertSame( 'foo@baz', $uri->getAuthority() );

    }


    public function testGetFragment() : void {
        $uri = new Uri();
        self::assertSame( '', $uri->getFragment() );

        $uri = new Uri( stFragment: 'foo' );
        self::assertSame( 'foo', $uri->getFragment() );
    }


    public function testGetHost() : void {
        $uri = new Uri();
        self::assertSame( '', $uri->getHost() );

        $uri = new Uri( stHost: 'example.com' );
        self::assertSame( 'example.com', $uri->getHost() );
    }


    public function testGetPath() : void {
        $uri = new Uri();
        self::assertSame( '', $uri->getPath() );

        $uri = new Uri( stPath: '/a/b' );
        self::assertSame( '/a/b', $uri->getPath() );
    }


    public function testGetPort() : void {
        $uri = new Uri();
        self::assertNull( $uri->getPort() );

        $uri = new Uri( nuPort: 8080 );
        self::assertSame( 8080, $uri->getPort() );
    }


    public function testGetQuery() : void {
        $uri = new Uri( stQuery: 'foo=1&bar=baz' );
        self::assertSame( 'foo=1&bar=baz', $uri->getQuery() );
    }


    public function testGetScheme() : void {
        $uri = new Uri();
        self::assertSame( '', $uri->getScheme() );

        $uri = new Uri( stScheme: 'https' );
        self::assertSame( 'https', $uri->getScheme() );

        $uri = new Uri( stScheme: 'HTTP' );
        self::assertSame( 'http', $uri->getScheme() );
    }


    public function testGetUserInfo() : void {
        $uri = new Uri();
        self::assertSame( '', $uri->getUserInfo() );

        $uri = new Uri( stUser: 'user' );
        self::assertSame( 'user', $uri->getUserInfo() );

        $uri = new Uri( stPassword: 'password' );
        self::assertSame( '', $uri->getUserInfo() );

        $uri = new Uri( stUser: 'user', stPassword: 'password' );
        self::assertSame( 'user:password', $uri->getUserInfo() );

    }


    public function testToString() : void {
        $uri = new Uri( stPath: '/foo/bar', stQuery: 'baz=qux' );
        self::assertSame( '/foo/bar?baz=qux', (string) $uri );

        $uri = new Uri(
            stScheme: 'https', stHost: 'example.com', nuPort: 8080,
            stPath: '/foo/bar', stQuery: 'baz=qux', stFragment: 'quux'
        );
        self::assertSame( 'https://example.com:8080/foo/bar?baz=qux#quux', strval( $uri ) );

        $uri = new Uri(
            stScheme: 'https', stHost: 'example.com',
            stPath: '/foo/bar', stQuery: 'baz=1&qux=2', stFragment: 'quux'
        );
        self::assertSame( 'https://example.com/foo/bar?baz=1&qux=2#quux', strval( $uri ) );
    }


    public function testWithFragment() : void {
        $uri = new Uri( stFragment: 'foo' );
        $uri = $uri->withFragment( 'bar' );
        self::assertSame( 'bar', $uri->stFragment );
    }


    public function testWithHost() : void {
        $uri = new Uri( stHost: 'foo' );
        $uri = $uri->withHost( 'bar' );
        self::assertSame( 'bar', $uri->stHost );
    }


    public function testWithPath() : void {
        $uri = new Uri( stPath: 'foo' );
        $uri = $uri->withPath( 'bar' );
        self::assertSame( 'bar', $uri->stPath );
    }


    public function testWithPort() : void {
        $uri = new Uri( nuPort: 8080 );
        $uri = $uri->withPort( 80 );
        self::assertSame( 80, $uri->nuPort );

        $uri = $uri->withPort( null );
        self::assertNull( $uri->nuPort );
    }


    public function testWithQuery() : void {
        $uri = new Uri( stQuery: 'foo=1' );
        $uri = $uri->withQuery( 'bar=2' );
        self::assertSame( 'bar=2', $uri->stQuery );
    }


    public function testWithScheme() : void {
        $uri = new Uri( stScheme: 'http' );
        $uri = $uri->withScheme( 'https' );
        self::assertSame( 'https', $uri->stScheme );
    }


    public function testWithUserInfo() : void {
        $uri = new Uri();
        $uri = $uri->withUserInfo( 'user', 'password' );
        self::assertSame( 'user', $uri->stUser );
        self::assertSame( 'password', $uri->stPassword );
    }


}
