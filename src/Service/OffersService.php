<?php

/**
 * @class OffersService
 * @package Cosmolot\Service
 */

namespace Cosmolot\Service;

use Cosmolot\Models\AbstractModel;
use Cosmolot\Contracts\AbstractServiceContract;
use Cosmolot\Exceptions\InvalidRequestException;
use Cosmolot\Models\OfferModel;
use Cosmolot\Models\OfferModelsCollection;
use Rct567\DomQuery\DomQuery;

class OffersService extends AbstractService implements AbstractServiceContract
{
    /**
     * @var string
     */
    protected $method = '/promo/materials';

    /**
     * @param int $id
     * @return OfferModel
     */
    public function getOne(int $id): ?OfferModel
    {
        $result = new OfferModel();

        if($this->getRequest()->authentication())
        {
            $method = $this->getMethod() . '?promo_id=' . $id;
            $response = $this->getRequest()->get($method);
            $document = new DomQuery($response);
            $offers = $document->find('promo-archive');

            if($offers->count())
            {
                $offers = json_decode(
                    $document->find('promo-archive')->getAttribute(':items')
                );
                
                foreach($offers as $offer)
                {
                    $result->fromArray((array) $offer);
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * @return OfferModelsCollection
     */
    public function get(): OfferModelsCollection
    {
        $result = new OfferModelsCollection([]);

        if($this->getRequest()->authentication())
        {
            $response = $this->getRequest()->get($this->getMethod());
            $document = new DomQuery($response);
            $offers = $document->find('promo-archive');

            if($offers->count())
            {
                $offers = json_decode(
                    $document->find('promo-archive')->getAttribute(':items')
                );

                foreach($offers as $offer)
                {
                    $result->add((new OfferModel())->fromArray((array) $offer));
                }
            }
        }
        return $result;
    }
}
