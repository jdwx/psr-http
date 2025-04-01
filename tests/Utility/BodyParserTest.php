<?php


declare( strict_types = 1 );


namespace Utility;


use InvalidArgumentException;
use JDWX\Psr7\StringStream;
use JDWX\Psr7\Utility\BodyParser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;


#[CoversClass( BodyParser::class )]
final class BodyParserTest extends TestCase {


    public function testConstructForEmptyBody() : void {
        $body = '';
        $parser = new BodyParser( $body, '' );
        self::assertNull( $parser->fetchBody() );
        self::assertSame( [], $parser->fetchFiles() );
        self::expectException( RuntimeException::class );
        $parser->fetchBodyArray();
    }


    public function testFetchFiles() : void {
        $body = <<<EOT
--BOUNDARY
Content-Disposition: form-data; name="file1"; filename="a.txt"

Content of a.txt.
--BOUNDARY
Content-Disposition: form-data; name="file2"; filename="a.html"
Content-Type: text/html

<!DOCTYPE html><head><title>Content of a.html.</title></head><body></body></html>
--BOUNDARY
Content-Disposition: form-data; name="foo"

Foo!
--BOUNDARY--
EOT;
        $stContentType = 'multipart/form-data; boundary=BOUNDARY';
        $parser = new BodyParser( $body, $stContentType );
        $rFiles = $parser->fetchFiles();
        self::assertSame( "Content of a.txt.\n", $rFiles[ 'file1' ]->getStream()->getContents() );
        self::assertStringContainsString( 'Content of a.html.', $rFiles[ 'file2' ]->getStream()->getContents() );

    }


    public function testParseForUnknownContentType() : void {
        $stContentType = 'application/octet-stream';
        $this->expectException( InvalidArgumentException::class );
        $x = new BodyParser( '', $stContentType );
        unset( $x );
    }


    public function testParseMultipart() : void {
        $body = <<<EOT

-----------------------------9051914041544843365972754266
Content-Disposition: form-data; name="text"

text default
-----------------------------9051914041544843365972754266
Content-Disposition: form-data; name="file1"; filename="a.txt"
Content-Type: text/plain

Content of a.txt.

-----------------------------9051914041544843365972754266
Content-Disposition: form-data; name="file2"; filename="a.html"
Content-Type: text/html

<!DOCTYPE html><title>Content of a.html.</title>

-----------------------------9051914041544843365972754266--

EOT;
        $stContentType = 'multipart/form-data; boundary=---------------------------9051914041544843365972754266';
        $parser = new BodyParser( new StringStream( $body ), $stContentType );

        self::assertSame( 'text default', $parser->fetchBodyArray()[ 'text' ] );

    }


    public function testParseMultipartForMalformedHeader() : void {
        $body = <<<EOT
-----------------------------9051914041544843365972754266
Content-Disposition: form-data; name="foo"

Foo!
-----------------------------9051914041544843365972754266
Content-Disposition form-data; name="bar"

Bar!
-----------------------------9051914041544843365972754266--
EOT;
        $stContentType = 'multipart/form-data; boundary=---------------------------9051914041544843365972754266';
        $parser = new BodyParser( new StringStream( $body ), $stContentType );
        self::assertSame( [ 'foo' => 'Foo!' ], $parser->fetchBody() );

    }


    public function testParseMultipartForMissingName() : void {
        $body = <<<EOT
-----------------------------9051914041544843365972754266
Content-Disposition: form-data; filename="a.txt"

-----------------------------9051914041544843365972754266
Content-Disposition: form-data; name="foo"

Bar.
-----------------------------9051914041544843365972754266--
EOT;
        $stContentType = 'multipart/form-data; boundary=---------------------------9051914041544843365972754266';
        $parser = new BodyParser( new StringStream( $body ), $stContentType );
        self::assertSame( 'Bar.', $parser->fetchBodyArray()[ 'foo' ] );

    }


    public function testParseMultipartForWrongDisposition() : void {
        $body = <<<EOT
-----------------------------9051914041544843365972754266
Content-Disposition: form-wrong; name="foo"

Foo!
-----------------------------9051914041544843365972754266
Content-Disposition: form-data; name="bar"

Bar!
-----------------------------9051914041544843365972754266--
EOT;
        $stContentType = 'multipart/form-data; boundary=---------------------------9051914041544843365972754266';
        $parser = new BodyParser( new StringStream( $body ), $stContentType );
        self::assertSame( [ 'bar' => 'Bar!' ], $parser->fetchBody() );
    }


    public function testParseUrlEncoded() : void {
        $body = 'foo=bar&baz=qux';
        $stContentType = 'application/x-www-form-urlencoded';
        $parser = new BodyParser( new StringStream( $body ), $stContentType );

        self::assertSame( 'bar', $parser->fetchBodyArray()[ 'foo' ] );
        self::assertSame( 'qux', $parser->fetchBodyArray()[ 'baz' ] );
    }


}
