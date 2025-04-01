<?php


declare( strict_types = 1 );


namespace Utility;


use JDWX\Psr7\Utility\BodyParser;
use JDWX\Psr7\Utility\BodyParserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( BodyParserFactory::class )]
final class BodyParserFactoryTest extends TestCase {


    public function testCreateBodyParser() : void {
        $fac = new BodyParserFactory();
        $oParser = $fac->createBodyParser( 'TEST_CONTENT', 'application/x-www-form-urlencoded' );
        self::assertInstanceOf( BodyParser::class, $oParser );
    }


}
