<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp;


use JDWX\PsrHttp\Message\LocalBodyTrait;
use JDWX\PsrHttp\Message\LocalHeadersTrait;
use JDWX\PsrHttp\Message\LocalProtocolVersionTrait;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;


class Message implements MessageInterface {


    use LocalBodyTrait;

    use LocalHeadersTrait;

    use LocalProtocolVersionTrait;


    /** @param array<string, list<string>>|string $headers */
    public function __construct( StreamInterface|string $body = '', string $stProtocolVersion = '1.1',
                                 array|string           $headers = [] ) {
        $this->setBody( $body );
        $this->stProtocolVersion = $stProtocolVersion;
        $this->setHeaders( $headers );
    }


}
