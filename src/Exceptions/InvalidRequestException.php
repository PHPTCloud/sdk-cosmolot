<?php

namespace Cosmolot\Exceptions;

use Exception;

class InvalidRequestException extends Exception
{
    /**
     * @var int
     */
    protected $httpCode;

    /**
     * @var string
     */
    protected $requestUrl;

    /**
     * @return int|null
     */
    public function getHttpCode(): ?int
    {
        return $this->httpCode;
    }

    /**
     * @param int $httpCode 
     * @return self
     */
    public function setHttpCode(int $httpCode): self
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRequestUrl(): ?string
    {
        return $this->requestUrl;
    }

    /**
     * @param string $requestUrl 
     * @return self
     */
    public function setRequestUrl(string $requestUrl): self
    {
        $this->requestUrl = $requestUrl;
        return $this;
    }
}
