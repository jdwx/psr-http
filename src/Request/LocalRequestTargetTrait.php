<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Request;


use Psr\Http\Message\UriInterface;


trait LocalRequestTargetTrait {


    private ?string $nstRequestTarget = null;


    public function getRequestTarget() : string {
        if ( is_string( $this->nstRequestTarget ) ) {
            return $this->nstRequestTarget;
        }
        $st = $this->getUri()->getPath();
        $stQuery = $this->getUri()->getQuery();
        $st .= $stQuery ? '?' . $stQuery : '';
        return $st;
    }


    abstract public function getUri() : UriInterface;


    /** @suppress PhanTypeMismatchReturn */
    public function withRequestTarget( string $requestTarget ) : static {
        $x = clone $this;
        $x->nstRequestTarget = $requestTarget;
        return $x;
    }


}
