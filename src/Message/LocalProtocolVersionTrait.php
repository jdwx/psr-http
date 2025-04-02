<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Message;


trait LocalProtocolVersionTrait {


    private string $stProtocolVersion;


    public function getProtocolVersion() : string {
        return $this->stProtocolVersion;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function withProtocolVersion( $version ) : static {
        $x = clone $this;
        $x->stProtocolVersion = $version;
        return $x;
    }


}