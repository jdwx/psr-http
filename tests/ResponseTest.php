<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests;


use JDWX\PsrHttp\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Response::class )]
final class ResponseTest extends TestCase {


    public function testGetReasonPhrase() : void {
        $response = new Response();
        self::assertSame( '', $response->getReasonPhrase() );

        $response->stReasonPhrase = 'Not Found';
        self::assertSame( 'Not Found', $response->getReasonPhrase() );
    }


    public function testGetStatusCode() : void {
        $response = new Response();
        self::assertSame( 200, $response->getStatusCode() );

        $response->uStatusCode = 404;
        self::assertSame( 404, $response->getStatusCode() );
    }


    public function testWithStatus() : void {
        $response = new Response();
        $response = $response->withStatus( 404, 'Not Found' );
        self::assertSame( 404, $response->uStatusCode );
        self::assertSame( 'Not Found', $response->stReasonPhrase );
    }


}
