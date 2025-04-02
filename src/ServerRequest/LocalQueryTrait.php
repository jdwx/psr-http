<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\ServerRequest;


trait LocalQueryTrait {


    /** @var array<string, string|list<string>> */
    private array $rQueryParams = [];


    /** @return array<string, string|list<string>> */
    public function getQueryParams() : array {
        return $this->rQueryParams;
    }


    /**
     * @param array<string, string|list<string>> $query
     * @suppress PhanTypeMismatchReturn
     */
    public function withQueryParams( array $query ) : static {
        $x = clone $this;
        $x->rQueryParams = $query;
        return $x;
    }


}