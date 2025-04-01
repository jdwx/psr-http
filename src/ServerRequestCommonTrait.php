<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp;


trait ServerRequestCommonTrait {


    /** @var array<string, mixed> */
    private array $rAttributes = [];


    /** @var array<string, string> */
    private array $rCookieParams = [];

    /** @var array<string, mixed> */
    private array $rServerParams = [];


    public function getAttribute( string $name, $default = null ) {
        return $this->rAttributes[ $name ] ?? $default;
    }


    /** @return array<string, mixed> */
    public function getAttributes() : array {
        return $this->rAttributes;
    }


    /** @return array<string, string> */
    public function getCookieParams() : array {
        return $this->rCookieParams;
    }


    /** @return array<string, mixed> */
    public function getServerParams() : array {
        return $this->rServerParams;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function withAttribute( string $name, mixed $value ) : static {
        $x = clone $this;
        $x->rAttributes[ $name ] = $value;
        return $x;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function withCookieParams( array $cookies ) : static {
        $x = clone $this;
        $x->rCookieParams = $cookies;
        return $x;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function withoutAttribute( string $name ) : static {
        $x = clone $this;
        unset( $x->rAttributes[ $name ] );
        return $x;
    }


}