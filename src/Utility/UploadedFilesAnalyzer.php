<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp\Utility;


use JDWX\PsrHttp\FileStream;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;


class UploadedFilesAnalyzer extends AbstractFilesAnalyzer {


    public function __construct( private readonly UploadedFileFactoryInterface $uploadedFileFactory ) {}


    protected function process( ?string $i_nstClientFilename, ?string $i_nstClientMediaType,
                                ?string $i_nstTmpName, int $i_uError,
                                ?int    $i_nuSize ) : ?UploadedFileInterface {
        if ( ! is_string( $i_nstTmpName ) ) {
            return null;
        }
        $stream = new FileStream( $i_nstTmpName );
        return $this->uploadedFileFactory->createUploadedFile( $stream, $i_nuSize, $i_uError,
            $i_nstClientFilename, $i_nstClientMediaType );
    }


}
