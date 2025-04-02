<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\ServerRequest;


trait LocalCookieTrait {


    /** @var array<string, string> */
    private array $rCookieParams = [];


    /** @return array<string, string> */
    public function getCookieParams() : array {
        return $this->rCookieParams;
    }


    /**
     * @param array<string, string> $cookies
     * @suppress PhanTypeMismatchReturn
     */
    public function withCookieParams( array $cookies ) : static {
        $x = clone $this;
        $x->rCookieParams = $cookies;
        return $x;
    }


}