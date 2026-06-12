<?php

namespace App\Traits;

trait CalculateShippingFee
{
    use HasRepositoryFromAdmin;

    public function calculateShippingFee(int $provinceId, int $wardId, int $total)
    {
        $shippingRateRepository = $this->getShippingRateRepository();

        // Ưu tiên tìm theo province_id, ward_id
        $shippingRate = $shippingRateRepository->getBy([
            'province_id' => $provinceId,
            'ward_id' => $wardId
        ]);

        if (!isset($shippingRate[0])) {
            $shippingRate = $shippingRateRepository->getBy([
                'province_id' => $provinceId,
                'ward_id' => null
            ]);
        }

        if (!isset($shippingRate[0])) {
            return 0;
        }

        return $shippingRate[0]->price;
    }
}
