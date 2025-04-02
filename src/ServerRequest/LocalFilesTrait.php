<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\ServerRequest;


use Psr\Http\Message\UploadedFileInterface;


trait LocalFilesTrait {


    /** @var array<string, UploadedFileInterface> */
    private array $rUploadedFiles = [];


    /** @return array<string, UploadedFileInterface> */
    public function getUploadedFiles() : array {
        return $this->rUploadedFiles;
    }


    /**
     * @param array<string, UploadedFileInterface> $uploadedFiles
     * @suppress PhanTypeMismatchReturn
     */
    public function withUploadedFiles( array $uploadedFiles ) : static {
        $x = clone $this;
        $x->rUploadedFiles = $uploadedFiles;
        return $x;
    }


}