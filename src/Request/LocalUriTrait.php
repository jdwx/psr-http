<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Request;


use JDWX\PsrHttp\Uri;
use Psr\Http\Message\UriInterface;


trait LocalUriTrait {


    private UriInterface $uri;


    public function getUri() : UriInterface {
        return $this->uri;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function withUri( UriInterface $uri, bool $preserveHost = false ) : static {
        $x = clone $this;
        $x->uri = $uri;
        return $x;
    }


    protected function setUri( UriInterface|string|null $i_uri ) : void {
        if ( is_null( $i_uri ) ) {
            $i_uri = '/';
        }
        $this->uri = Uri::from( $i_uri );
    }


}