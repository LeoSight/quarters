<?php

namespace App\Service;

class UtilsService {

    /**
     * @param array<int|string, int> $weightedValues
     * @return int|string
     */
    public function getRandomWeightedElement(array $weightedValues): int|string
    {
        $rand = mt_rand(1, (int)array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }

        return 0;
    }

}