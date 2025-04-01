<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Utility;


use Psr\Http\Message\StreamInterface;


class BodyParserFactory implements BodyParserFactoryInterface {


    public function createBodyParser( StreamInterface|string $i_body,
                                      string                 $i_stContentType ) : BodyParserInterface {
        return new BodyParser( $i_body, $i_stContentType );
    }


}
