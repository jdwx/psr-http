<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\ServerRequest;


trait LocalServerTrait {


    /** @var array<string, mixed> */
    private array $rServerParams = [];


    /** @return array<string, mixed> */
    public function getServerParams() : array {
        return $this->rServerParams;
    }


}