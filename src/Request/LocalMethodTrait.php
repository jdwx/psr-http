<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Request;


trait LocalMethodTrait {


    private string $stMethod;


    public function getMethod() : string {
        return $this->stMethod;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function withMethod( string $method ) : static {
        $x = clone $this;
        $x->stMethod = $method;
        return $x;
    }


}