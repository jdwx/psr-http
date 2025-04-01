<?php


declare( strict_types = 1 );


namespace JDWX\Psr7\Utility;


use JDWX\Psr7\Factory;
use JDWX\Psr7\StringStream;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;


class BodyParser implements BodyParserInterface {


    /** @var array<string, string|list<string>>|null */
    protected ?array $nrParsedBody = null;


    /** @var array<string, UploadedFileInterface> */
    protected array $rFiles = [];


    private string $stContentType;

    private string $stBoundary;

    private string $stBody;

    private UploadedFileFactoryInterface $uploadedFileFactory;


    public function __construct( StreamInterface|string        $i_body, string $i_stContentType,
                                 ?UploadedFileFactoryInterface $i_uploadedFileFactory = null ) {
        $rContentType = Headers::parseValue( $i_stContentType );
        $this->stContentType = trim( $rContentType[ 0 ] ?? '' );
        $this->stBoundary = trim( $rContentType[ 'boundary' ] ?? '' );
        $this->uploadedFileFactory = $i_uploadedFileFactory ?? new Factory();
        if ( ! is_string( $i_body ) ) {
            $i_body = strval( $i_body );
        }
        $this->stBody = $i_body;
        $this->parse();
    }


    public function fetchBody() : array|null {
        return $this->nrParsedBody;
    }


    public function fetchBodyArray() : array {
        if ( is_array( $this->nrParsedBody ) ) {
            return $this->nrParsedBody;
        }
        throw new \RuntimeException( 'No body.' );
    }


    public function fetchFiles() : array {
        return $this->rFiles;
    }


    protected function parse() : void {
        match ( $this->stContentType ) {
            'application/x-www-form-urlencoded' => $this->parseUrlEncoded(),
            'multipart/form-data' => $this->parseMultipart(),
            '' => null,
            default => throw new \InvalidArgumentException(
                "Unsupported content type: {$this->stContentType}"
            ),
        };
    }


    protected function parseMultipart() : void {
        $stBoundary = '--' . $this->stBoundary;
        foreach ( explode( $stBoundary, $this->stBody ) as $stPart ) {
            $stPartCheck = trim( $stPart );
            if ( '' === $stPartCheck || '--' === $stPartCheck ) {
                continue;
            }
            $this->parseMultipartPart( $stPart );
        }
    }


    protected function parseMultipartPart( string $stPart ) : void {
        # Each part contains one or more headers, followed by a blank line, followed by the body.
        # Lines are *supposed* to be terminated by CRLF, but some clients use LF only.
        [ $rHeaders, $stBody ] = Headers::splitFromBodyAndParse( $stPart );
        $stContentDisposition = $rHeaders[ 'content-disposition' ][ 0 ] ?? '';
        if ( 'form-data' != $stContentDisposition ) {
            return;
        }
        $stName = $rHeaders[ 'content-disposition' ][ 'name' ] ?? '';
        if ( '' === $stName ) {
            return;
        }
        $bIsFile = isset( $rHeaders[ 'content-disposition' ][ 'filename' ] ) || isset( $rHeaders[ 'content-type' ] );
        if ( ! $bIsFile ) {
            $this->nrParsedBody[ $stName ] = trim( $stBody );
            return;
        }
        $this->rFiles[ $stName ] = $this->uploadedFileFactory->createUploadedFile(
            new StringStream( $stBody ),
            clientFilename: $rHeaders[ 'content-disposition' ][ 'filename' ] ?? '',
            clientMediaType: $rHeaders[ 'content-type' ][ 0 ] ?? '',
        );
    }


    protected function parseUrlEncoded() : void {
        parse_str( $this->stBody, $this->nrParsedBody );
    }


}
