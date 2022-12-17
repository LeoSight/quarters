<?php

namespace App\Service;

class InjuryService {

    /**
     * @var array<int, string>
     */
    private array $injuries = [];

    /**
     * @var array<int, string>
     */
    private array $bodyParts = [
        "HD", // head (hlava nebo krk - v případě střelného poranění vždy smrtelné)
        "UT", // upper torso (trup/záda/ramena)
        "BT", // bottom torso (břicho/bedra)
        "LA", // left arm (levá ruka - mimo rameno, zaškrtitelné)
        "RA", // right arm (pravá ruka - mimo rameno, zaškrtitelné)
        "LL", // left leg (levá noha - zaškrtitelné)
        "RL", // right leg (pravá noha - zaškrtitelné)
    ];

    /**
     * @var array<int, string>
     */
    private array $ammoTypes = [
        "762", // 7.62x51mm
        "7RU", // 7.62x39mm
        "556", // 5.56x45mm
        "BOL", // ball of lead (12 gauge)
        "22L", // .22 LR
        "9MM", // 9x19mm
        "45A", // .45 ACP
    ];

    /**
     * @var array<int, string>
     */
    private array $shotInjuries = [];

    public function __construct()
    {
        foreach($this->bodyParts as $part){
            foreach($this->ammoTypes as $ammo){
                $this->shotInjuries[] = "SHOT_" . $part . "_" . $ammo;
            }
            $this->injuries[] = "STAB_" . $part;
        }

        $this->injuries = array_merge($this->injuries, $this->shotInjuries);
    }

    /**
     * @return array<int, string>
     */
    public function getValidInjuries(): array
    {
        return $this->injuries;
    }

    public function getInjuryDescription(string $injury): string
    {
        $replace = [
            '_' => ' ',
            'SHOT' => 'střelné poranění v oblasti',
            'STAB' => 'řezné poranění v oblasti',
            'HD' => 'hlavy',
            'UT' => 'hrudi',
            'BT' => 'břicha',
            'LA' => 'levé horní končetiny',
            'RA' => 'pravé horní končetiny',
            'LL' => 'levé dolní končetiny',
            'RL' => 'pravé dolní končetiny',
            '762' => '(7.62×51mm)',
            '7RU' => '(7.62×39mm)',
            '556' => '(5.56×45mm)',
            'BOL' => '(sférická olověná střela)',
            '22L' => '(.22 LR)',
            '9MM' => '(9×19mm)',
            '45A' => '(.45 ACP)',
        ];

        return str_replace(array_keys($replace), array_values($replace), $injury);
    }

    /**
     * @return array
     */
    public function getBodyParts(): array
    {
        return $this->bodyParts;
    }

}