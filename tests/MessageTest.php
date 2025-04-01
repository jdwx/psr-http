<?php


declare( strict_types = 1 );


use JDWX\Psr7\Message;
use JDWX\Psr7\StringStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Message::class )]
final class MessageTest extends TestCase {


    public function testGetBody() : void {
        $msg = new Message( 'Hello, World!' );
        self::assertSame( 'Hello, World!', $msg->getBody()->getContents() );
    }


    public function testGetHeaderForNone() : void {
        $msg = new Message();
        self::assertEquals( [], $msg->getHeader( 'content-type' ) );
    }


    public function testGetHeaderForOne() : void {
        $msg = new Message();
        $msg->rHeaders = [
            'content-type' => [ 'text/plain' ],
        ];
        self::assertEquals( [ 'text/plain' ], $msg->getHeader( 'conTEnt-typE' ) );
    }


    public function testGetHeaderForTwo() : void {
        $msg = new Message();
        $msg->rHeaders = [
            'content-type' => [ 'text/plain', 'application/json' ],
        ];
        self::assertEquals( [ 'text/plain', 'application/json' ], $msg->getHeader( 'Content-Type' ) );
    }


    public function testGetHeaderLineForNone() : void {
        $msg = new Message();
        self::assertSame( '', $msg->getHeaderLine( 'content-type' ) );
    }


    public function testGetHeaderLineForOne() : void {
        $msg = new Message();
        $msg->rHeaders = [
            'content-type' => [ 'text/plain' ],
        ];
        self::assertSame( 'text/plain', $msg->getHeaderLine( 'CONTENT-TYPE' ) );
    }


    public function testGetHeaderLineForTwo() : void {
        $msg = new Message();
        $msg->rHeaders = [
            'content-type' => [ 'text/plain', 'application/json' ],
        ];
        self::assertSame( 'text/plain, application/json', $msg->getHeaderLine( 'Content-Type' ) );
    }


    public function testGetHeaders() : void {
        $msg = new Message();
        $msg->rHeaders = [
            'content-type' => [ 'text/plain', 'application/json' ],
        ];
        self::assertEquals( [ 'content-type' => [ 'text/plain', 'application/json' ] ], $msg->getHeaders() );
    }


    public function testGetProtocolVersion() : void {
        $msg = new Message();
        self::assertSame( '1.1', $msg->getProtocolVersion() );
    }


    public function testHasHeader() : void {
        $msg = new Message();
        self::assertFalse( $msg->hasHeader( 'content-type' ) );
        $msg->rHeaders = [
            'content-type' => [ 'text/plain' ],
        ];
        self::assertTrue( $msg->hasHeader( 'content-type' ) );
        $msg->rHeaders = [
            'content-type' => [ 'text/plain', 'application/json' ],
        ];
        self::assertTrue( $msg->hasHeader( 'content-type' ) );
        self::assertFalse( $msg->hasHeader( 'content-length' ) );
    }


    public function testWithAddedHeader() : void {
        $msg = new Message();
        $msg = $msg->withAddedHeader( 'Content-Type', 'text/plain' );
        self::assertEquals( [ 'text/plain' ], $msg->rHeaders[ 'content-type' ] );

        $msg = $msg->withAddedHeader( 'content-type', 'application/json' );
        self::assertEquals( [ 'content-type' => [ 'text/plain', 'application/json' ] ], $msg->getHeaders() );

        $msg = $msg->withAddedHeader( 'Content-Type', [ 'text/html', 'application/xml' ] );
        self::assertEquals( [
            'text/plain',
            'application/json',
            'text/html',
            'application/xml',
        ], $msg->rHeaders[ 'content-type' ] );
    }


    public function testWithBody() : void {
        $msg = new Message();
        $body = new StringStream( 'TEST_BODY' );
        $msg2 = $msg->withBody( $body );
        self::assertSame( '', $msg->getBody()->getContents() );
        self::assertSame( 'TEST_BODY', $msg2->getBody()->getContents() );
    }


    public function testWithHeader() : void {
        $msg = new Message();
        $msg = $msg->withHeader( 'Content-Type', 'text/plain' );
        self::assertEquals( [ 'content-type' => [ 'text/plain' ] ], $msg->getHeaders() );

        $msg = $msg->withHeader( 'Content-Type', 'application/json' );
        self::assertEquals( [ 'content-type' => [ 'application/json' ] ], $msg->getHeaders() );

        $msg = $msg->withHeader( 'Content-Type', [ 'text/html', 'application/xml' ] );
        self::assertEquals( [ 'text/html', 'application/xml' ], $msg->rHeaders[ 'content-type' ] );
    }


    public function testWithHeadersForAdd() : void {
        $msg = new Message();
        $msg->rHeaders = [
            'content-type' => [ 'text/plain' ],
            'content-length' => [ '123' ],
        ];
        $msg = $msg->withHeaders( [
            'content-type' => [ 'application/json' ],
            'host' => 'example.com',
        ], true );
        self::assertSame( [
            'content-type' => [ 'text/plain', 'application/json' ],
            'content-length' => [ '123' ],
            'host' => [ 'example.com' ],
        ], $msg->rHeaders );
    }


    public function testWithHeadersForNoAdd() : void {
        $msg = new Message();
        $msg->rHeaders = [
            'content-type' => [ 'text/plain' ],
            'content-length' => [ '123' ],
        ];
        $msg = $msg->withHeaders( [
            'content-type' => [ 'application/json' ],
            'host' => 'example.com',
        ] );
        self::assertSame( [
            'content-type' => [ 'application/json' ],
            'content-length' => [ '123' ],
            'host' => [ 'example.com' ],
        ], $msg->rHeaders );
    }


    public function testWithProtocolVersion() : void {
        $msg = new Message();
        self::assertSame( '1.1', $msg->stProtocolVersion );
        $msg = $msg->withProtocolVersion( '2.0' );
        self::assertSame( '2.0', $msg->stProtocolVersion );
    }


    public function testWithoutHeader() : void {
        $msg = new Message();
        $msg->rHeaders = [
            'content-type' => [ 'text/plain', 'application/json' ],
            'content-length' => [ '123' ],
        ];
        $msg = $msg->withoutHeader( 'content-type' );
        self::assertEquals( [ 'content-length' => [ '123' ] ], $msg->rHeaders );
    }


}
