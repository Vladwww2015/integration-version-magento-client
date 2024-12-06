<?php

namespace IntegrationHelper\IntegrationVersionMagentoClient\ApiRequest;

class LatestHashDataOutput
{
    public function __construct(
        protected string $hash,
        protected string $datetime,
        protected string $message,
        protected bool $isError,
    ){}

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getDatetime(): string
    {
        return $this->datetime;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isError(): bool
    {
        return $this->isError;
    }
}
