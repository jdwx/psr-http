<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Message;


use JDWX\PsrHttp\StringStream;
use Psr\Http\Message\StreamInterface;


trait LocalBodyTrait {


    private StreamInterface $body;


    public function getBody() : StreamInterface {
        return $this->body;
    }

    
    /** @suppress PhanTypeMismatchReturn */
    public function withBody( StreamInterface $body ) : static {
        $x = clone $this;
        $x->body = $body;
        return $x;
    }


    protected function setBody( StreamInterface|string $i_body ) : void {
        if ( ! $i_body instanceof StreamInterface ) {
            $i_body = new StringStream( $i_body );
        }
        $this->body = $i_body;
    }


}