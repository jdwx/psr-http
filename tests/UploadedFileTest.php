<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests;


use JDWX\PsrHttp\FileStream;
use JDWX\PsrHttp\UploadedFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;


#[CoversClass( UploadedFile::class )]
final class UploadedFileTest extends TestCase {


    public function testFromStream() : void {
        $stContent = 'TEST_CONTENT';
        $stream = FileStream::fromString( $stContent );
        $file = UploadedFile::fromStream( $stream );
        self::assertSame( $stContent, $file->getStream()->getContents() );
    }


    public function testGetClientFilename() : void {
        $file = UploadedFile::fromString( 'TEST_CONTENT', i_nstClientFilename: 'test.txt' );
        self::assertSame( 'test.txt', $file->getClientFilename() );
    }


    public function testGetClientMediaType() : void {
        $file = UploadedFile::fromString( 'TEST_CONTENT' );
        self::assertSame( 'text/plain', $file->getClientMediaType() );

        $file = UploadedFile::fromString( 'TEST_CONTENT', i_stClientMediaType: 'text/html' );
        self::assertSame( 'text/html', $file->getClientMediaType() );
    }


    public function testGetError() : void {
        $file = UploadedFile::fromString( 'TEST_CONTENT' );
        self::assertSame( 0, $file->getError() );
        $file->iError = 1;
        self::assertSame( 1, $file->getError() );
    }


    public function testGetSize() : void {
        $stContent = 'TEST_CONTENT';
        $file = UploadedFile::fromString( 'TEST_CONTENT' );
        self::assertSame( strlen( $stContent ), $file->getSize() );
    }


    public function testGetStream() : void {
        $stContent = 'TEST_CONTENT';
        $file = UploadedFile::fromString( $stContent );
        self::assertSame( $stContent, $file->getStream()->getContents() );
        $file->bMoved = true;
        self::expectException( RuntimeException::class );
        $file->getStream();
    }


    public function testMoveTo() : void {
        $stContent = 'TEST_CONTENT';
        $file = UploadedFile::fromString( 'TEST_CONTENT' );
        $stFileName = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid( '', true ) . '.txt';
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        @unlink( $stFileName );
        $file->moveTo( $stFileName );
        self::assertFileExists( $stFileName );
        self::assertSame( $stContent, file_get_contents( $stFileName ) );
        unlink( $stFileName );
        self::assertTrue( $file->bMoved );
        self::expectException( RuntimeException::class );
        $file->moveTo( $stFileName );
    }


    public function testMoveToForWriteError() : void {
        $stContent = 'TEST_CONTENT';
        $file = UploadedFile::fromString( $stContent );
        $stFileName = '/no/such/file';
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        self::expectException( RuntimeException::class );
        $file->moveTo( $stFileName );
    }


}