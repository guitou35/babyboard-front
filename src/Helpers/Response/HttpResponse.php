<?php

namespace App\Helpers\Response;

class HttpResponse
{
    public function __construct(private $status, private $content, private bool $isError, private ?string $message = null)
    {
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function isError(): bool
    {
        return $this->isError;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getHydraMember(): array
    {
        return $this->content['hydra:member'];
    }

    public function getHydraTotalItems(): int
    {
        return $this->content['hydra:totalItems'];
    }

    public function getHydraView(): array
    {
        return $this->content['hydra:view'];
    }

    public function getHydraSearch(): array
    {
        return $this->content['hydra:search'];
    }

    public function getHydraIriTemplate(): array
    {
        return $this->content['hydra:iriTemplate'];
    }

    public function getHydraTemplate(): array
    {
        return $this->content['hydra:template'];
    }

    public function getHydraMapping(): array
    {
        return $this->content['hydra:mapping'];
    }

    public function getItemContent(): array
    {
        foreach($this->content as $key => $value){
            if(preg_match('/^@/', $key)) unset($this->content[$key]);
        }
       return $this->content;
    }
}