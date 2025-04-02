<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Message;


use JDWX\PsrHttp\Utility\HeaderList;


trait LocalHeadersTrait {


    private HeaderList $headers;


    /** @return list<string> */
    public function getHeader( string $name ) : array {
        return $this->headers->get( $name );
    }


    public function getHeaderLine( string $name ) : string {
        return $this->headers->getLine( $name );
    }


    public function getHeaders() : array {
        return $this->headers->getAll();
    }


    public function hasHeader( string $name ) : bool {
        return $this->headers->has( $name );
    }


    /**
     * @param string $name Header name to add.
     * @param string|list<string> $value Value (or list of values) to add.
     * @suppress PhanTypeMismatchReturn
     *
     * This adds a header to the message without removing any existing
     * occurrences of that header.
     */
    public function withAddedHeader( string $name, $value ) : static {
        $name = strtolower( $name );
        $x = clone $this;
        $x->headers = $x->headers->withAdded( $name, $value );
        return $x;
    }


    /**
     * @param string|list<string> $value
     * @suppress PhanTypeMismatchReturn
     */
    public function withHeader( string $name, $value ) : static {
        $x = clone $this;
        $x->headers = $x->headers->with( $name, $value );
        return $x;
    }


    /**
     * @param array<string, string|list<string>> $i_rHeaders Headers to add.
     * @param bool $i_bAdd If true, add to existing headers with the same name
     *                     instead of replacing them.
     * @suppress PhanTypeMismatchReturn
     */
    public function withHeaders( iterable $i_rHeaders, bool $i_bAdd = false ) : static {
        $x = clone $this;
        $x->headers = $x->headers->withIterable( $i_rHeaders, $i_bAdd );
        return $x;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function withoutHeader( $name ) : static {
        $x = clone $this;
        $x->headers = $x->headers->without( $name );
        return $x;
    }


    /** @param array<string, list<string>>|string $i_headers */
    protected function setHeaders( array|string $i_headers ) : void {
        $this->headers = HeaderList::from( $i_headers );
    }


}