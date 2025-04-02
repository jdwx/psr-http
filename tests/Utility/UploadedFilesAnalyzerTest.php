<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Tests\Utility;


use JDWX\PsrHttp\Factory;
use JDWX\PsrHttp\Utility\TempFile;
use JDWX\PsrHttp\Utility\UploadedFilesAnalyzer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;


#[CoversClass( UploadedFilesAnalyzer::class )]
final class UploadedFilesAnalyzerTest extends TestCase {


    public function testForBasicOne() : void {
        $stContent = 'TEST_CONTENT';
        $tmp = new TempFile( $stContent );
        $rFiles = [
            'foo' => [
                'name' => 'foo.txt',
                'type' => 'text/plain',
                'tmp_name' => strval( $tmp ),
                'error' => 0,
                'size' => strlen( $stContent ),
            ],
        ];

        $ufa = new UploadedFilesAnalyzer( new Factory() );
        $result = $ufa->map( $rFiles );
        self::assertSame( 1, count( $result ) );
        self::assertArrayHasKey( 'foo', $result );
        $file = $result[ 'foo' ];
        self::assertInstanceOf( UploadedFileInterface::class, $file );
        self::assertSame( 'foo.txt', $file->getClientFilename() );
        self::assertSame( $stContent, $file->getStream()->getContents() );
        self::assertSame( 'text/plain', $file->getClientMediaType() );
        self::assertSame( 0, $file->getError() );
        self::assertSame( strlen( $stContent ), $file->getSize() );
    }


    public function testForMissing() : void {
        $rFiles = [
            'foo' => [
                'name' => 'foo.txt',
                'type' => 'text/plain',
                'error' => 0,
                'size' => 0,
            ],
        ];

        $ufa = new UploadedFilesAnalyzer( new Factory() );
        $result = $ufa->map( $rFiles );
        self::assertEmpty( $result );
    }


}
