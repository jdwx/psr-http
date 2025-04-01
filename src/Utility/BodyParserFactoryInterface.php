<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Utility;


use Psr\Http\Message\StreamInterface;


interface BodyParserFactoryInterface {


    public function createBodyParser( StreamInterface|string $i_body,
                                      string                 $i_stContentType ) : BodyParserInterface;


}