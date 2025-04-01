<?php


declare( strict_types = 1 );


namespace JDWX\Psr7;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;


class Response extends Message implements ResponseInterface {


    public function __construct( StreamInterface|string $i_body = '',
                                 string                 $i_stProtocolVersion = '1.1',
                                 array                  $i_rHeaders = [],
                                 public int             $uStatusCode = 200,
                                 public string          $stReasonPhrase = '' ) {
        parent::__construct( $i_body, $i_stProtocolVersion, $i_rHeaders );
    }


    public function getReasonPhrase() : string {
        return $this->stReasonPhrase;
    }


    public function getStatusCode() : int {
        return $this->uStatusCode;
    }


    public function withStatus( $code, $reasonPhrase = '' ) : static {
        $x = clone $this;
        $x->uStatusCode = $code;
        $x->stReasonPhrase = $reasonPhrase ?: $this->stReasonPhrase;
        return $x;
    }


}
