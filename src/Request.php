<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp;


use JDWX\PsrHttp\Request\LocalMethodTrait;
use JDWX\PsrHttp\Request\LocalRequestTargetTrait;
use JDWX\PsrHttp\Request\LocalUriTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;


class Request extends Message implements RequestInterface {


    use LocalMethodTrait;

    use LocalRequestTargetTrait;

    use LocalUriTrait;


    /** @param array<string, list<string>> $i_rHeaders */
    public function __construct( StreamInterface|string   $i_body = '', string $i_stProtocolVersion = '1.1',
                                 array                    $i_rHeaders = [],
                                 string                   $stMethod = 'GET',
                                 UriInterface|string|null $i_uri = null,
                                 ?string                  $nstRequestTarget = null ) {
        parent::__construct( $i_body, $i_stProtocolVersion, $i_rHeaders );
        $this->stMethod = $stMethod;
        $this->nstRequestTarget = $nstRequestTarget;
        $this->setUri( $i_uri );
    }


}
