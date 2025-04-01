<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Utility;


use Psr\Http\Message\UploadedFileInterface;


interface BodyParserInterface {


    public function fetchBody() : array|object|null;


    /** @return array<string, UploadedFileInterface> */
    public function fetchFiles() : array;


}