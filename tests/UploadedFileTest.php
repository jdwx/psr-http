<?php


declare( strict_types = 1 );


use JDWX\Psr7\FileStream;
use JDWX\Psr7\UploadedFile;
use JDWX\Psr7\Utility\TempFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( UploadedFile::class )]
final class UploadedFileTest extends TestCase {


    public function testFromFilesForMissingFile() : void {
        $rFiles = [
            'file0' => [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'tmp_name' => '/no/such/file',
            ],
        ];
        self::assertNull( UploadedFile::fromFiles( 'file0', null, $rFiles ) );
    }


    public function testFromFilesForMissingName() : void {
        $rFiles = [
            'file0' => [
                'error' => UPLOAD_ERR_OK,
                'type' => 'text/plain',
                'tmp_name' => '/no/such/file',
            ],
        ];
        self::assertNull( UploadedFile::fromFiles( 'file0', null, $rFiles ) );
    }


    public function testFromFilesForMissingTag() : void {
        $rFiles = [ 'file0' => [] ];
        self::assertNull( UploadedFile::fromFiles( 'file1', null, $rFiles ) );
    }


    public function testFromFilesForMultiple() : void {
        $tmp1 = new TempFile( 'TEST_FILE_1' );
        $tmp2 = new TempFile( 'TEST_FILE_2' );
        $rFiles = [
            'file' => [
                'name' => [
                    0 => 'test1.txt',
                    1 => 'test2.txt',
                ],
                'error' => [
                    0 => UPLOAD_ERR_OK,
                    1 => UPLOAD_ERR_OK,
                ],
                'type' => [
                    0 => 'text/plain',
                    1 => 'text/plain',
                ],
                'tmp_name' => [
                    0 => strval( $tmp1 ),
                    1 => strval( $tmp2 ),
                ],
            ],
        ];
        self::assertNull( UploadedFile::fromFiles( 'file1', null, $rFiles ) );
        $file1 = UploadedFile::fromFiles( 'file', 0, $rFiles );
        self::assertSame( 'TEST_FILE_1', $file1->getStream()->getContents() );
        $file2 = UploadedFile::fromFiles( 'file', 1, $rFiles );
        self::assertSame( 'TEST_FILE_2', $file2->getStream()->getContents() );
        self::assertNull( UploadedFile::fromFiles( 'file', 2, $rFiles ) );
    }


    public function testFromFilesForSingle() : void {
        $stContent = 'TEST_CONTENT';
        $tmp = new TempFile( $stContent );
        $rFiles = [ 'file0' => [
            'name' => 'test.txt',
            'error' => UPLOAD_ERR_OK,
            'type' => 'text/plain',
            'tmp_name' => strval( $tmp ),
        ], ];
        $file = UploadedFile::fromFiles( 'file0', null, $rFiles );
        self::assertSame( $stContent, $file->getStream()->getContents() );
        self::assertNull( UploadedFile::fromFiles( 'file1', null, $rFiles ) );
        self::assertNull( UploadedFile::fromFiles( 'file0', 0, $rFiles ) );
    }


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
        $file->stBody = $stContent;
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
        $file->stBody = $stContent;
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
        $file = UploadedFile::fromString( 'TEST_CONTENT' );
        $file->stBody = $stContent;
        $stFileName = '/no/such/file';
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        self::expectException( RuntimeException::class );
        $file->moveTo( $stFileName );
    }


}