<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\ServerRequest;


trait LocalAttributeTrait {


    /** @var array<string, mixed> */
    private array $rAttributes = [];


    public function getAttribute( string $name, $default = null ) {
        return $this->rAttributes[ $name ] ?? $default;
    }


    /** @return array<string, mixed> */
    public function getAttributes() : array {
        return $this->rAttributes;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function withAttribute( string $name, mixed $value ) : static {
        $x = clone $this;
        $x->rAttributes[ $name ] = $value;
        return $x;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function withoutAttribute( string $name ) : static {
        $x = clone $this;
        unset( $x->rAttributes[ $name ] );
        return $x;
    }


}