<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests\Utility;


use JDWX\PsrHttp\Utility\HeaderList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( HeaderList::class )]
final class HeaderListTest extends TestCase {


    public function testFromForArray() : void {
        $hdr = HeaderList::from( [ 'foo' => [ 'bar' ], 'Baz' => 'qux' ] );
        self::assertSame( [ 'foo' => [ 'bar' ], 'baz' => [ 'qux' ] ], $hdr->getAll() );
    }


    public function testFromForHeaderList() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar' ] ] );
        $hdr2 = HeaderList::from( $hdr );
        self::assertSame( $hdr, $hdr2 );
    }


    public function testFromForString() : void {
        $hdr = HeaderList::from( 'foo: bar' );
        self::assertSame( [ 'foo' => [ 'bar' ] ], $hdr->getAll() );
    }


    public function testFromForStringWithBadLine() : void {
        $hdr = HeaderList::from( "foo: bar\nbaz qux\n" );
        self::assertSame( [ 'foo' => [ 'bar' ] ], $hdr->getAll() );
    }


    public function testGet() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar', 'baz' ] ] );
        self::assertSame( [ 'bar', 'baz' ], $hdr->get( 'foo' ) );
        self::assertSame( [], $hdr->get( 'qux' ) );
    }


    public function testGetAll() : void {
        $r = [ 'foo' => [ 'bar', 'baz' ], 'qux' => [ 'quux' ] ];
        $hdr = new HeaderList( $r );
        self::assertSame( $r, $hdr->getAll() );
    }


    public function testGetLine() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar' ] ] );
        self::assertSame( 'bar', $hdr->getLine( 'foo' ) );

        $hdr = new HeaderList( [ 'foo' => [ 'bar', 'baz' ] ] );
        self::assertSame( 'bar, baz', $hdr->getLine( 'foo' ) );
        self::assertSame( '', $hdr->getLine( 'qux' ) );
    }


    public function testGetValue() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar' ] ] );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        self::assertSame( '', $hdr->getValue( 'foo', 1 ) );
        self::assertSame( 'baz', $hdr->getValue( 'foo', 1, 'baz' ) );

        $hdr = new HeaderList( [ 'foo' => [ 'bar', 'baz' ] ] );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        self::assertSame( 'baz', $hdr->getValue( 'foo', 1 ) );
        self::assertSame( '', $hdr->getValue( 'foo', 2 ) );

        $hdr = new HeaderList( [ 'foo' => [ 'bar; baz=qux' ] ] );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        self::assertSame( 'qux', $hdr->getValue( 'foo', 'baz' ) );
        self::assertSame( '', $hdr->getValue( 'foo', 'quux' ) );
    }


    public function testGetValues() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar; baz=qux' ] ] );
        self::assertSame( [ 0 => 'bar', 'baz' => 'qux' ], $hdr->getValues( 'foo' ) );

        $hdr = new HeaderList( [ 'foo' => [ 'bar; baz=qux', 'quux' ] ] );
        self::assertSame( [ 0 => 'bar', 'baz' => 'qux', 1 => 'quux' ], $hdr->getValues( 'foo' ) );
    }


    public function testHas() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar', 'baz' ], 'qux' => [ 'quux' ] ] );
        self::assertTrue( $hdr->has( 'foo' ) );
        self::assertTrue( $hdr->has( 'qux' ) );
        self::assertFalse( $hdr->has( 'corge' ) );
    }


    public function testHasValue() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar', 'baz' ], 'qux' => [ 'quux; corge=grault' ] ] );
        self::assertTrue( $hdr->hasValue( 'foo', 0 ) );
        self::assertTrue( $hdr->hasValue( 'foo', 1 ) );
        self::assertFalse( $hdr->hasValue( 'foo', 2 ) );
        self::assertTrue( $hdr->hasValue( 'qux', 0 ) );
        self::assertFalse( $hdr->hasValue( 'qux', 1 ) );
        self::assertTrue( $hdr->hasValue( 'qux', 'corge' ) );
        self::assertFalse( $hdr->hasValue( 'qux', 'garply' ) );
        self::assertFalse( $hdr->hasValue( 'xyzzy', 0 ) );
    }


    public function testWith() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar' ] ] );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        $hdr2 = $hdr->with( 'baz', 'qux' );
        $hdr3 = $hdr2->with( 'foo', 'quux' );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        self::assertSame( 'quux', $hdr3->getValue( 'foo', 0 ) );
        self::assertSame( [ 'foo' => [ 'bar' ] ], $hdr->getAll() );
        self::assertSame( [ 'foo' => [ 'bar' ], 'baz' => [ 'qux' ] ], $hdr2->getAll() );
        self::assertSame( [ 'foo' => [ 'quux' ], 'baz' => [ 'qux' ] ], $hdr3->getAll() );
    }


    public function testWithAdded() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar' ] ] );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        $hdr2 = $hdr->withAdded( 'baz', 'qux' );
        $hdr3 = $hdr2->withAdded( 'foo', 'quux' );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        self::assertSame( 'bar', $hdr3->getValue( 'foo', 0 ) );
        self::assertSame( 'quux', $hdr3->getValue( 'foo', 1 ) );
        self::assertSame( [ 'foo' => [ 'bar' ] ], $hdr->getAll() );
        self::assertSame( [ 'foo' => [ 'bar' ], 'baz' => [ 'qux' ] ], $hdr2->getAll() );
        self::assertSame( [ 'foo' => [ 'bar', 'quux' ], 'baz' => [ 'qux' ] ], $hdr3->getAll() );
    }


    public function testWithIterableForAdd() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar' ] ] );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        $hdr2 = $hdr->withIterable( [ 'foo' => 'baz', 'qux' => 'quux' ], true );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        self::assertSame( 'bar', $hdr2->getValue( 'foo', 0 ) );
        self::assertSame( 'baz', $hdr2->getValue( 'foo', 1 ) );
        self::assertSame( 'quux', $hdr2->getValue( 'qux', 0 ) );
    }


    public function testWithIterableForNoAdd() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar' ] ] );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        $hdr2 = $hdr->withIterable( [ 'foo' => 'baz', 'qux' => 'quux' ] );
        self::assertSame( 'bar', $hdr->getValue( 'foo', 0 ) );
        self::assertSame( 'baz', $hdr2->getValue( 'foo', 0 ) );
        self::assertSame( 'quux', $hdr2->getValue( 'qux', 0 ) );
    }


    public function testWithout() : void {
        $hdr = new HeaderList( [ 'foo' => [ 'bar' ], 'baz' => [ 'qux' ] ] );
        $hdr2 = $hdr->without( 'foo' );
        self::assertSame( [ 'foo' => [ 'bar' ], 'baz' => [ 'qux' ] ], $hdr->getAll() );
        self::assertSame( [ 'baz' => [ 'qux' ] ], $hdr2->getAll() );
    }


}
