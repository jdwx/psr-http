<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests\Utility;


use JDWX\PsrHttp\Utility\TempFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( TempFile::class )]
final class TempFileTest extends TestCase {


    public function testDeleteAfterDestruct() : void {
        $file = new TempFile( 'TEST_FILE' );
        $stFilename = strval( $file );
        unset( $file );
        self::assertFileDoesNotExist( $stFilename );
    }


    public function testFOpen() : void {
        $file = new TempFile( 'TEST_FILE' );
        $fp = $file->fopen( 'r' );
        self::assertIsResource( $fp );
        self::assertSame( 'TEST_FILE', stream_get_contents( $fp ) );
        fclose( $fp );
    }


}
