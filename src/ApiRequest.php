<?php
/**
 * @class ApiRequest
 * @package Cosmolot
 */

namespace Cosmolot;

use Illuminate\Http\Response;
use Cosmolot\Dictionaries\ChromeUserAgentsDictionary;
use Cosmolot\Exceptions\InvalidRequestException;
use Rct567\DomQuery\DomQuery;

class ApiRequest
{
    /**
     * @var string
     */
    private const HTTP_SCHEME = 'https';

    /**
     * @var string
     */
    protected const LOGIN_PAGE = '';

    /**
     * @var string
     */
    protected const LOGIN_METHOD = '/users/login';

    /**
     * @var string
     */
    protected const COOKIE_PATH = __DIR__ . '/storage/cookie/';

    /**
     * @var string
     */
    protected const COOKIE_FILENAME = 'cookies.txt';

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var resource
     */
    private $connection;

    /**
     * @var string
     */
    private $cookieFilePath;

    /**
     * @param string $domain
     * @return self
     */
    public function setDomain(string $domain): self
    {
        $this->domain = self::HTTP_SCHEME . '::/' . $domain;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return self
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return self
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return resource|null
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $login
     * @param string $password
     * @param string $domain
     */
    public function __construct(string $login, string $password, string $domain)
    {
        $this->domain = $domain;
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @param string $login
     * @param string $password
     * @param string $domain
     */
    public function __invoke(string $login, string $password, string $domain)
    {
        $this->domain = $domain;
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getCookieFilePath(): ?string
    {
        return static::COOKIE_PATH . static::COOKIE_FILENAME;
    }

    /**
     * @param string $method
     * @return string|null
     */
    public function getDomain(string $method = ''): ?string
    {
        return implode('', [self::HTTP_SCHEME, '://', $this->domain, $method]);
    }

    /**
     * @return resource
     */
    public function openConnection()
    {
        $this->connection = curl_init();
        return $this->getConnection();
    }

    /**
     * @return void
     */
    public function closeConnection()
    {
        $this->connection = curl_close($this->connection);
        unset($this->connection);
        $this->connection = null;
    }

    /**
     * @return bool
     */
    public function authentication(): bool
    {
        $postParams = implode('&', [
            'login=' . $this->getLogin(),
            'password=' . $this->getPassword(),
            '_token=' . $this->getToken()
        ]);

        try
        {
            $this->post(self::LOGIN_METHOD, $postParams);
        }
        catch(InvalidRequestException $e)
        {
            if($e->getHttpCode() === Response::HTTP_INTERNAL_SERVER_ERROR)
            {
                return true;
            }
            return false;
        }
        
        return true;
    }

    /**
     * @return string
     */
    protected function getToken(): string
    {
        $token = '';
        try
        {
            $response = $this->get(static::LOGIN_PAGE);
            $document = new DomQuery($response);
            $token = $document->find('[name="_token"]');
            
            if($token->count()) {
                $token = $token->getAttribute('value');
            }
        }
        catch(InvalidRequestException $e)
        {
            print_r($e);
        }

        return $token;
    }

    /**
     * @param string $methodUrl
     * @param array $queryParams
     * @param bool $includeCookie
     * @return mixed
     */
    public function get(string $methodUrl, array $queryParams = [], bool $includeCookie = true)
    {
        /**
         * Prepare http params for request
         *
         * * See below default headers
         */
        $defaultParams = [
            CURLOPT_URL => $this->getDomain($methodUrl),
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'User-Agent: ' . ChromeUserAgentsDictionary::USER_AGENT_0,
            ],
        ];
        /**
         * * Cookie params
         */
        if($includeCookie === true)
        {
            $defaultParams[CURLOPT_COOKIEJAR] = $this->getCookieFilePath();
            $defaultParams[CURLOPT_COOKIEFILE] = $this->getCookieFilePath();
        }

        try
        {
            $response = $this->request($defaultParams);
            return $response;
        }
        catch(InvalidRequestException $e)
        {
            throw $e;
        }
    }

    /**
     * @param string $methodUrl
     * @param array|string $queryParams
     * @param array $params
     * @param bool $includeCookie
     * @return mixed
     */
    public function post(string $methodUrl, $postFields = [], array $params = [], bool $includeCookie = true)
    {
        /**
         * Prepare http params for request
         *
         * * See below default headers
         */
        $defaultParams = [
            CURLOPT_URL => $this->getDomain($methodUrl),
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => [
                'User-Agent: ' . ChromeUserAgentsDictionary::USER_AGENT_0,
            ],
        ];
        /**
         * * Cookie params
         */
        if($includeCookie === true)
        {
            $defaultParams[CURLOPT_COOKIEJAR] = $this->getCookieFilePath();
            $defaultParams[CURLOPT_COOKIEFILE] = $this->getCookieFilePath();
        }

        try
        {
            $response = $this->request($defaultParams);
            return $response;
        }
        catch(InvalidRequestException $e) {
            throw $e;
        }
    }

    /**
     * @param resource $connection
     * @return mixed
     */
    private function request($params)
    {
        $connection = $this->openConnection();
        curl_setopt_array($connection, $params);
        /**
         * Request execution
         */
        $response = curl_exec($connection);
        $responseCode = curl_getinfo($connection, CURLINFO_HTTP_CODE);

        if($responseCode === Response::HTTP_OK || $responseCode === Response::HTTP_CREATED) {
            return $response;
        }
        else if($responseCode === Response::HTTP_NOT_FOUND) {
            $exception = new InvalidRequestException('exception ' . Response::$statusTexts[Response::HTTP_NOT_FOUND]);
            $exception->setHttpCode(Response::HTTP_NOT_FOUND);
            $exception->setRequestUrl($params[CURLOPT_URL]);
            throw $exception;
        }
        else if($responseCode === Response::HTTP_FORBIDDEN) {
            $exception = new InvalidRequestException('exception ' . Response::$statusTexts[Response::HTTP_FORBIDDEN]);
            $exception->setHttpCode(Response::HTTP_FORBIDDEN);
            $exception->setRequestUrl($params[CURLOPT_URL]);
            throw $exception;
        }
        else if($responseCode === Response::HTTP_BAD_REQUEST) {
            $exception = new InvalidRequestException('exception ' . Response::$statusTexts[Response::HTTP_BAD_REQUEST]);
            $exception->setHttpCode(Response::HTTP_BAD_REQUEST);
            $exception->setRequestUrl($params[CURLOPT_URL]);
            throw $exception;
        }
        else if($responseCode === Response::HTTP_BAD_GATEWAY) {
            $exception = new InvalidRequestException('exception ' . Response::$statusTexts[Response::HTTP_BAD_GATEWAY]);
            $exception->setHttpCode(Response::HTTP_BAD_GATEWAY);
            $exception->setRequestUrl($params[CURLOPT_URL]);
            throw $exception;
        }
        else if($responseCode === Response::HTTP_INTERNAL_SERVER_ERROR) {
            $exception = new InvalidRequestException('exception ' . Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]);
            $exception->setHttpCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $exception->setRequestUrl($params[CURLOPT_URL]);
            throw $exception;
        }
        else if($responseCode === Response::HTTP_METHOD_NOT_ALLOWED) {
            $exception = new InvalidRequestException('exception ' . Response::$statusTexts[Response::HTTP_METHOD_NOT_ALLOWED]);
            $exception->setHttpCode(Response::HTTP_METHOD_NOT_ALLOWED);
            $exception->setRequestUrl($params[CURLOPT_URL]);
            throw $exception;
        }

        $this->closeConnection();
    }
}
