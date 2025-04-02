<?php


declare( strict_types = 1 );


namespace JDWX\PsrHttp;


use JDWX\PsrHttp\ServerRequest\LocalAttributeTrait;
use JDWX\PsrHttp\ServerRequest\LocalCookieTrait;
use JDWX\PsrHttp\ServerRequest\LocalFilesTrait;
use JDWX\PsrHttp\ServerRequest\LocalParsedBodyTrait;
use JDWX\PsrHttp\ServerRequest\LocalQueryTrait;
use JDWX\PsrHttp\ServerRequest\LocalServerTrait;
use JDWX\PsrHttp\Utility\BodyParserFactory;
use JDWX\PsrHttp\Utility\BodyParserFactoryInterface;
use JDWX\PsrHttp\Utility\Headers;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;


class ServerRequest extends Request implements ServerRequestInterface {


    use LocalAttributeTrait;

    use LocalCookieTrait;

    use LocalFilesTrait;

    use LocalParsedBodyTrait;

    use LocalQueryTrait;

    use LocalServerTrait;


    /**
     * @param StreamInterface|string $i_body
     * @param string $i_stProtocolVersion
     * @param array<string, list<string>> $i_rHeaders
     * @param string $stMethod
     * @param UriInterface|string|null $i_uri
     * @param string|null $nstRequestTarget
     * @param array<string, string|list<string>> $rQueryParams
     * @param array<int|string, UploadedFileInterface> $rUploadedFiles
     * @param array<string, string> $rCookieParams
     * @param array<string, mixed> $rAttributes
     * @param array<string, mixed> $rServerParams
     * @param array<string, string|list<string>>|object|null $xParsedBody
     */
    public function __construct( StreamInterface|string   $i_body = '', string $i_stProtocolVersion = '1.1',
                                 array                    $i_rHeaders = [], string $stMethod = 'GET',
                                 UriInterface|string|null $i_uri = null, ?string $nstRequestTarget = null,
                                 array                    $rQueryParams = [],
                                 array                    $rUploadedFiles = [],
                                 array                    $rCookieParams = [],
                                 array                    $rAttributes = [],
                                 array                    $rServerParams = [],
                                 array|object|null        $xParsedBody = null ) {
        parent::__construct( $i_body, $i_stProtocolVersion, $i_rHeaders, $stMethod, $i_uri, $nstRequestTarget );
        $this->rAttributes = $rAttributes;
        $this->rCookieParams = $rCookieParams;
        $this->xParsedBody = $xParsedBody;
        $this->rQueryParams = $rQueryParams;
        $this->rServerParams = $rServerParams;
        $this->rUploadedFiles = $rUploadedFiles;
    }


    /**
     * @param StreamInterface|string $i_body
     * @param string $i_stProtocolVersion
     * @param array<string, list<string>> $i_rHeaders
     * @param string $stMethod
     * @param UriInterface|string|null $i_uri
     * @param string|null $nstRequestTarget
     * @param array<string, string|list<string>> $rQueryParams
     * @param array<string, string> $rCookieParams
     * @param array<string, mixed> $rAttributes
     * @param array<string, mixed> $rServerParams
     * @param BodyParserFactoryInterface|null $i_bodyParserFactory
     * @return self
     */
    public static function fromBody( StreamInterface|string      $i_body = '', string $i_stProtocolVersion = '1.1',
                                     array                       $i_rHeaders = [], string $stMethod = 'GET',
                                     UriInterface|string|null    $i_uri = null, ?string $nstRequestTarget = null,
                                     array                       $rQueryParams = [],
                                     array                       $rCookieParams = [],
                                     array                       $rAttributes = [],
                                     array                       $rServerParams = [],
                                     ?BodyParserFactoryInterface $i_bodyParserFactory = null ) : self {
        $i_bodyParserFactory ??= new BodyParserFactory();
        $stContentType = Headers::getLine( $i_rHeaders, 'Content-Type' );
        $bodyParser = $i_bodyParserFactory->createBodyParser( $i_body, $stContentType );
        $nrParsedBody = $bodyParser->fetchBody();
        $rUploadedFiles = $bodyParser->fetchFiles();
        return new self( $i_body, $i_stProtocolVersion, $i_rHeaders, $stMethod, $i_uri, $nstRequestTarget,
            $rQueryParams, $rUploadedFiles, $rCookieParams, $rAttributes, $rServerParams, $nrParsedBody );
    }


}