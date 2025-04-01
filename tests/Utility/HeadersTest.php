<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests\Utility;


use JDWX\PsrHttp\Utility\Headers;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Headers::class )]
final class HeadersTest extends TestCase {


    public function testParse() : void {
        $stHeaders = "Foo: bar\nBaz: qux; quux=\"corge grault\"\n";
        $expected = [
            'foo' => [ 0 => 'bar' ],
            'baz' => [ 0 => 'qux', 'quux' => 'corge grault' ],
        ];
        self::assertSame( $expected, Headers::parse( $stHeaders ) );

        $stHeaders = "Foo: bar\r\nBaz: qux; quux=\"corge grault\"\r\n";
        self::assertSame( $expected, Headers::parse( $stHeaders ) );
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
        [ $rHeaders, $stBody ] = Headers::splitFromBodyAndParse( $stText );
        self::assertSame( [
            'foo' => [ 0 => 'bar' ],
            'baz' => [ 0 => 'qux', 'quux' => 'corge grault' ],
        ], $rHeaders );
        self::assertSame( "Garply: the body!\n", $stBody );
    }


    public function testSplitFromBodyForNoBody() : void {
        $stText = "Foo: bar\nBaz: qux; quux=\"corge grault\"\n";
        [ $stHeaders, $stBody ] = Headers::splitFromBody( $stText );
        self::assertSame( "Foo: bar\nBaz: qux; quux=\"corge grault\"", $stHeaders );
        self::assertSame( '', $stBody );
    }


}
