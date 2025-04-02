<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests\Utility;


use JDWX\PsrHttp\Utility\AbstractFilesAnalyzer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractFilesAnalyzer::class )]
final class AbstractFilesAnalyzerTest extends TestCase {


    public function testBasicMulti() : void {
        $rFiles = [
            'foo' => [
                'name' => [ 'foo.txt', 'bar.txt' ],
                'type' => [ 'text/plain', 'text/plain' ],
                'tmp_name' => [ '/tmp/php123', '/tmp/php456' ],
                'error' => [ 0, 0 ],
                'size' => [ 1234, 5678 ],
            ],
        ];
        $afa = $this->newAnalyzer();
        $result = $afa->map( $rFiles );
        self::assertSame( 1, count( $result ) );
        self::assertSame( 2, count( $result[ 'foo' ] ) );
        self::assertSame( 'foo.txt', $result[ 'foo' ][ 0 ][ 'name' ] );
        self::assertSame( '/tmp/php123', $result[ 'foo' ][ 0 ][ 'tmp_name' ] );
        self::assertSame( 'text/plain', $result[ 'foo' ][ 0 ][ 'type' ] );
        self::assertSame( 0, $result[ 'foo' ][ 0 ][ 'error' ] );
        self::assertSame( 1234, $result[ 'foo' ][ 0 ][ 'size' ] );
        self::assertSame( 'bar.txt', $result[ 'foo' ][ 1 ][ 'name' ] );
        self::assertSame( '/tmp/php456', $result[ 'foo' ][ 1 ][ 'tmp_name' ] );
        self::assertSame( 'text/plain', $result[ 'foo' ][ 1 ][ 'type' ] );
        self::assertSame( 0, $result[ 'foo' ][ 1 ][ 'error' ] );
        self::assertSame( 5678, $result[ 'foo' ][ 1 ][ 'size' ] );

    }


    public function testBasicOne() : void {
        $rFiles = [
            'foo' => [
                'name' => 'foo.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php123',
                'error' => 0,
                'size' => 1234,
            ],
        ];

        $afa = $this->newAnalyzer();
        $result = $afa->map( $rFiles );

        self::assertSame( 1, count( $result ) );
        self::assertSame( 'foo.txt', $result[ 'foo' ][ 'name' ] );
        self::assertSame( '/tmp/php123', $result[ 'foo' ][ 'tmp_name' ] );
        self::assertSame( 'text/plain', $result[ 'foo' ][ 'type' ] );
        self::assertSame( 0, $result[ 'foo' ][ 'error' ] );
        self::assertSame( 1234, $result[ 'foo' ][ 'size' ] );

    }


    public function testNestedMulti() : void {
        $rFiles = [
            'foo' => [
                'name' => [ 'bar' => [ 'baz' => [ 0 => 'foo.txt', 1 => 'bar.txt' ] ] ],
                'type' => [ 'bar' => [ 'baz' => [ 0 => 'text/plain', 1 => 'text/plain' ] ] ],
                'tmp_name' => [ 'bar' => [ 'baz' => [ 0 => '/tmp/php123', 1 => '/tmp/php456' ] ] ],
                'error' => [ 'bar' => [ 'baz' => [ 0 => 0, 1 => 0 ] ] ],
                'size' => [ 'bar' => [ 'baz' => [ 0 => 1234, 1 => 5678 ] ] ],
            ],
        ];
        $afa = $this->newAnalyzer();
        $result = $afa->map( $rFiles );
        self::assertSame( 1, count( $result ) );
        self::assertSame( 1, count( $result[ 'foo' ] ) );
        self::assertSame( 1, count( $result[ 'foo' ][ 'bar' ] ) );
        self::assertSame( 2, count( $result[ 'foo' ][ 'bar' ][ 'baz' ] ) );

        self::assertSame( 'foo.txt', $result[ 'foo' ][ 'bar' ][ 'baz' ][ 0 ][ 'name' ] );
        self::assertSame( '/tmp/php123', $result[ 'foo' ][ 'bar' ][ 'baz' ][ 0 ][ 'tmp_name' ] );
        self::assertSame( 'text/plain', $result[ 'foo' ][ 'bar' ][ 'baz' ][ 0 ][ 'type' ] );
        self::assertSame( 0, $result[ 'foo' ][ 'bar' ][ 'baz' ][ 0 ][ 'error' ] );
        self::assertSame( 1234, $result[ 'foo' ][ 'bar' ][ 'baz' ][ 0 ][ 'size' ] );

        self::assertSame( 'bar.txt', $result[ 'foo' ][ 'bar' ][ 'baz' ][ 1 ][ 'name' ] );
        self::assertSame( '/tmp/php456', $result[ 'foo' ][ 'bar' ][ 'baz' ][ 1 ][ 'tmp_name' ] );
        self::assertSame( 'text/plain', $result[ 'foo' ][ 'bar' ][ 'baz' ][ 1 ][ 'type' ] );
        self::assertSame( 0, $result[ 'foo' ][ 'bar' ][ 'baz' ][ 1 ][ 'error' ] );
        self::assertSame( 5678, $result[ 'foo' ][ 'bar' ][ 'baz' ][ 1 ][ 'size' ] );

    }


    public function testNestedOne() : void {
        $rFiles = [
            'foo' => [
                'name' => [ 'bar' => [ 'baz' => 'foo.txt' ] ],
                'type' => [ 'bar' => [ 'baz' => 'text/plain' ] ],
                'tmp_name' => [ 'bar' => [ 'baz' => '/tmp/php123' ] ],
                'error' => [ 'bar' => [ 'baz' => 0 ] ],
                'size' => [ 'bar' => [ 'baz' => 1234 ] ],
            ],
        ];
        $afa = $this->newAnalyzer();
        $result = $afa->map( $rFiles );
        self::assertSame( 1, count( $result ) );
        self::assertSame( 1, count( $result[ 'foo' ] ) );
        self::assertSame( 1, count( $result[ 'foo' ][ 'bar' ] ) );
        self::assertSame( 'foo.txt', $result[ 'foo' ][ 'bar' ][ 'baz' ][ 'name' ] );
        self::assertSame( '/tmp/php123', $result[ 'foo' ][ 'bar' ][ 'baz' ][ 'tmp_name' ] );
        self::assertSame( 'text/plain', $result[ 'foo' ][ 'bar' ][ 'baz' ][ 'type' ] );
        self::assertSame( 0, $result[ 'foo' ][ 'bar' ][ 'baz' ][ 'error' ] );
        self::assertSame( 1234, $result[ 'foo' ][ 'bar' ][ 'baz' ][ 'size' ] );

    }


    public function testSkipFile() : void {
        $rFiles = [
            'foo' => [
                'name' => 'foo.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php123',
                'error' => 0,
                'size' => 69420,
            ],
        ];
        $afa = $this->newAnalyzer();
        $result = $afa->map( $rFiles );
        self::assertSame( 0, count( $result ) );
    }


    public function testSkipFileNested() : void {
        $rFiles = [
            'foo' => [
                'name' => [ 'bar' => [ 'foo.txt', 'bar.txt' ] ],
                'type' => [ 'bar' => [ 'text/plain', 'text/plain' ] ],
                'tmp_name' => [ 'bar' => [ '/tmp/php123', '/tmp/php456' ] ],
                'error' => [ 'bar' => [ 0, 0 ] ],
                'size' => [ 'bar' => [ 69420, 5678 ] ],
            ],
        ];
        $afa = $this->newAnalyzer();
        $result = $afa->map( $rFiles );
        self::assertSame( 1, count( $result ) );
        self::assertSame( 1, count( $result[ 'foo' ][ 'bar' ] ) );
        self::assertSame( 'bar.txt', $result[ 'foo' ][ 'bar' ][ 0 ][ 'name' ] );
    }


    public function testTwoTopLevel() : void {
        $rFiles = [
            'foo' => [
                'name' => 'foo.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php123',
                'error' => 0,
                'size' => 1234,
            ],
            'bar' => [
                'name' => 'bar.txt',
                'type' => 'text/plain',
                'tmp_name' => '/tmp/php456',
                'error' => 0,
                'size' => 5678,
            ],
        ];
        $afa = $this->newAnalyzer();
        $result = $afa->map( $rFiles );
        self::assertSame( 2, count( $result ) );

        self::assertSame( 'foo.txt', $result[ 'foo' ][ 'name' ] );
        self::assertSame( '/tmp/php123', $result[ 'foo' ][ 'tmp_name' ] );
        self::assertSame( 'text/plain', $result[ 'foo' ][ 'type' ] );
        self::assertSame( 0, $result[ 'foo' ][ 'error' ] );
        self::assertSame( 1234, $result[ 'foo' ][ 'size' ] );

        self::assertSame( 'bar.txt', $result[ 'bar' ][ 'name' ] );
        self::assertSame( '/tmp/php456', $result[ 'bar' ][ 'tmp_name' ] );
        self::assertSame( 'text/plain', $result[ 'bar' ][ 'type' ] );
        self::assertSame( 0, $result[ 'bar' ][ 'error' ] );
        self::assertSame( 5678, $result[ 'bar' ][ 'size' ] );

    }


    private function newAnalyzer() : AbstractFilesAnalyzer {
        return new class() extends AbstractFilesAnalyzer {


            /** @return mixed[]|null */
            protected function process( ?string $i_nstClientFilename, ?string $i_nstClientMediaType,
                                        ?string $i_nstTmpName, int $i_uError, ?int $i_nuSize ) : ?array {
                if ( 69420 === $i_nuSize ) {
                    return null;
                }
                return [
                    'name' => $i_nstClientFilename,
                    'tmp_name' => $i_nstTmpName,
                    'type' => $i_nstClientMediaType,
                    'error' => $i_uError,
                    'size' => $i_nuSize,
                ];
            }


        };
    }


}
