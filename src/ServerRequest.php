<?php


declare( strict_types = 1 );


namespace JDWX\Psr7;


use JDWX\Psr7\Utility\BodyParserFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;


class ServerRequest extends Request implements ServerRequestInterface {


    use ServerRequestCommonTrait;


    /**
     * @param array<string, string|list<string>> $rQueryParams
     * @param ?array<string, UploadedFileInterface> $nrUploadedFiles
     * @param array<string, string> $rCookieParams
     * @param array<string, mixed> $rAttributes
     * @param array<string, mixed> $rServerParams
     * @param array<string, string|list<string>>|object|null $nrParsedBody
     */
    public function __construct( StreamInterface|string   $i_body = '', string $i_stProtocolVersion = '1.1',
                                 array                    $i_rHeaders = [], string $stMethod = 'GET',
                                 UriInterface|string|null $i_uri = null, ?string $nstRequestTarget = null,
                                 public array             $rQueryParams = [],
                                 public ?array            $nrUploadedFiles = null,
                                 array                    $rCookieParams = [],
                                 array                    $rAttributes = [],
                                 array                    $rServerParams = [],
                                 public array|object|null $nrParsedBody = null ) {
        parent::__construct( $i_body, $i_stProtocolVersion, $i_rHeaders, $stMethod, $i_uri, $nstRequestTarget );
        $this->rCookieParams = $rCookieParams;
        $this->rAttributes = $rAttributes;
        $this->rServerParams = $rServerParams;
        if ( is_null( $this->nrParsedBody ) || is_null( $this->nrUploadedFiles ) ) {
            $bodyParserFactory = new BodyParserFactory();
            $bodyParser = $bodyParserFactory->createBodyParser(
                $this->getBody(),
                $this->getHeaderLine( 'Content-Type' )
            );
            if ( is_null( $this->nrParsedBody ) ) {
                $this->nrParsedBody = $bodyParser->fetchBody();
            }
            if ( is_null( $this->nrUploadedFiles ) ) {
                $this->nrUploadedFiles = $bodyParser->fetchFiles();
            }
        }
    }


    public function getParsedBody() : array|object|null {
        return $this->nrParsedBody;
    }


    /** @return array<string, string|list<string>> */
    public function getQueryParams() : array {
        return $this->rQueryParams;
    }


    /** @return array<string, UploadedFileInterface> */
    public function getUploadedFiles() : array {
        return $this->nrUploadedFiles;
    }


    /** @param array|object|null $data */
    public function withParsedBody( $data ) : static {
        $x = clone $this;
        $x->nrParsedBody = $data;
        return $x;
    }


    public function withQueryParams( array $query ) : static {
        $x = clone $this;
        $x->rQueryParams = $query;
        return $x;
    }


    public function withUploadedFiles( array $uploadedFiles ) : static {
        $x = clone $this;
        $x->nrUploadedFiles = $uploadedFiles;
        return $x;
    }


}