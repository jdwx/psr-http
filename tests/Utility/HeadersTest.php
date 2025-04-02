<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests\Utility;


use JDWX\PsrHttp\Utility\Headers;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Headers::class )]
final class HeadersTest extends TestCase {


    public function testGet() : void {
        self::assertSame( [ 'bar', 'baz' ], Headers::get( [ 'foo' => [ 'bar', 'baz' ] ], 'Foo' ) );
    }


    public function testGetLine() : void {
        self::assertSame( 'bar', Headers::getLine( [ 'foo' => [ 'bar' ] ], 'Foo' ) );
        self::assertSame( 'bar, baz', Headers::getLine( [
            'foo' => [ 'bar', 'baz' ],
        ], 'Foo' ) );
        self::assertSame( '', Headers::getLine( [ 'foo' => [] ], 'Foo' ) );
        self::assertSame( '', Headers::getLine( [], 'Foo' ) );
    }


    public function testParse() : void {
        $stHeaders = "Foo: bar\nBaz: qux; quux=\"corge grault\"\nBaz: garply\n";
        $expected = [
            'foo' => [ 'bar' ],
            'baz' => [ 'qux; quux="corge grault"', 'garply' ],
        ];
        self::assertSame( $expected, Headers::parse( $stHeaders )->getAll() );

        $stHeaders = "Foo: bar\r\nBaz: qux; quux=\"corge grault\"\r\nBaz: garply\r\n";
        self::assertSame( $expected, Headers::parse( $stHeaders )->getAll() );
    }


    public function testParseValue() : void {
        $header = 'foo; bar=baz; qux="corge grault"';
        $expected = [
            0 => 'foo',
            'bar' => 'baz',
            'qux' => 'corge grault',
        ];
        self::assertSame( $expected, Headers::parseValue( $header ) );

        $header = 'bar=baz; foo; qux="corge grault"';
        $expected = [
            'bar' => 'baz',
            0 => 'foo',
            'qux' => 'corge grault',
        ];
        self::assertSame( $expected, Headers::parseValue( $header ) );
    }


    public function testSplitFromBody() : void {
        $stText = "Foo: bar\nBaz: qux; quux=\"corge grault\"\n\nGarply: the body!\n";
        [ $stHeaders, $stBody ] = Headers::splitFromBody( $stText );
        self::assertSame( "Foo: bar\nBaz: qux; quux=\"corge grault\"", $stHeaders );
        self::assertSame( "Garply: the body!\n", $stBody );

        $stText = "Foo: bar\r\nBaz: qux; quux=\"corge grault\"\r\n\r\nGarply: the body!\r\n";
        [ $stHeaders, $stBody ] = Headers::splitFromBody( $stText );
        self::assertSame( "Foo: bar\nBaz: qux; quux=\"corge grault\"", $stHeaders );
        self::assertSame( "Garply: the body!\r\n", $stBody );

        $stText = "Foo: bar\rBaz: qux; quux=\"corge grault\"\r\rGarply: the body!\r";
        [ $stHeaders, $stBody ] = Headers::splitFromBody( $stText );
        self::assertSame( "Foo: bar\nBaz: qux; quux=\"corge grault\"", $stHeaders );
        self::assertSame( "Garply: the body!\r", $stBody );
    }


    public function testSplitFromBodyAndParse() : void {
        $stText = "Foo: bar\nBaz: qux; quux=\"corge grault\"\n\nGarply: the body!\n";
        [ $headers, $stBody ] = Headers::splitFromBodyAndParse( $stText );
        self::assertSame( [
            'foo' => [ 'bar' ],
            'baz' => [ 'qux; quux="corge grault"' ],
        ], $headers->getAll() );
        self::assertSame( "Garply: the body!\n", $stBody );
    }


    public function testSplitFromBodyForNoBody() : void {
        $stText = "Foo: bar\nBaz: qux; quux=\"corge grault\"\n";
        [ $stHeaders, $stBody ] = Headers::splitFromBody( $stText );
        self::assertSame( "Foo: bar\nBaz: qux; quux=\"corge grault\"", $stHeaders );
        self::assertSame( '', $stBody );
    }


}
