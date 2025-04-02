<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Utility;


use Psr\Http\Message\UploadedFileInterface;


interface BodyParserInterface {


    /** @return array<string, string|list<string>>|object|null */
    public function fetchBody() : array|object|null;


    /** @return array<string, UploadedFileInterface> */
    public function fetchFiles() : array;


}