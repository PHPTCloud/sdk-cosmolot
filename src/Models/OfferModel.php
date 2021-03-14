<?php
/**
 * @class ItemsCollection
 * @package Cosmolot\Models
 */

namespace Cosmolot\Models;

class OfferModel
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var int
     */
    private $id;

    /**
     * @var OffersLandingsModelsCollection
     */
    private $landings;

    /**
     * @var PaymentArgumentsModel
     */
    private $commission;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function __construct()
    {
        //
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'landings' => [],
            'payments' => [],
        ];
    }

    /**
     * @param array $array
     * @return self
     */
    public function fromArray(array $array): self
    {
        if(isset($array['id']))
            $this->setId((int) $array['id']);

        if(isset($array['code']))
            $this->setTitle($array['code']);

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return (empty($this->getId()) && empty($this->getTitle()))
            ? true
            : false;
    }
}
