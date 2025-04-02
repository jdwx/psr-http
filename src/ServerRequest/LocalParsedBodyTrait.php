<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\ServerRequest;


trait LocalParsedBodyTrait {


    /** @var array<string, string|list<string>>|object|null */
    private array|object|null $xParsedBody = null;


    /** @return array<string, string|list<string>>|object|null */
    public function getParsedBody() : array|object|null {
        return $this->xParsedBody;
    }


    /**
     * @param array<string, string|list<string>>|object|null $data
     * @suppress PhanTypeMismatchReturn
     */
    public function withParsedBody( $data ) : static {
        $x = clone $this;
        $x->xParsedBody = $data;
        return $x;
    }


}