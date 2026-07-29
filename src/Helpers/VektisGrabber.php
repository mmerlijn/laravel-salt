<?php

namespace mmerlijn\LaravelSalt\Helpers;

use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Auth\User;
use mmerlijn\LaravelSalt\Helpers\Traits\VektisQualificationsTrait;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\LaravelSalt\Notifications\ProblemNotification;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Enums\PatientSexEnum;
use mmerlijn\msgRepo\Enums\VektisType;
use mmerlijn\msgRepo\Enums\YesNoEnum;
use mmerlijn\msgRepo\Name;

/*
 * Organizations: https://www.vektis.nl/agb-register/onderneming-12345678
 * Data retrieved:
 * - Name
 * - Address (street, postcode, city)
 * - Email (general)
 * - Phone (general)
 * - qualifications
 * - caregivers
 * - organizations

* Caregivers:   https://www.vektis.nl/agb-register/zorgverlener-12345678
 * Data retrieved
 * - Name
 * - Sex
 * - qualifications
 * - organizations

 * Department: https://www.vektis.nl/agb-register/vestiging-01057200
 * Data retrieved
 * - Name
 * - Address (street, postcode, city)
 * - Email (general)
 * - Phone (general)
 * - qualifications
 * - caregivers
 */

class VektisGrabber
{
    use VektisQualificationsTrait;

    public array $data = [
        'name' => '',
        'sex' => '',
        'address' => null,
        'email' => '',
        'phone' => '',
        'qualifications' => [],
        'relations' => [],
        'start' => null,
        'end' => null,
        'gp' => YesNoEnum::NO,
        'type' => VektisType::ZORGVERLENER,
    ];

    public function __invoke(VektisType $type, string $agbcode): array|null
    {

        try {
            $html = null;
            $counter = 0;
            while (!$html and $counter < 2) {
                sleep(random_int(3, 7));
                $url = "https://www.vektis.nl/agb-register/{$type->value}-" . $agbcode;
                $html = $this->getHtml($url);
                logger("VektisGrabber: Trying to grab AGBcode: $agbcode with url: $url");
                if (!$html) {
                    $type = match ($type) {
                        VektisType::ONDERNEMING => VektisType::VESTIGING,
                        VektisType::VESTIGING => VektisType::ZORGVERLENER,
                        VektisType::ZORGVERLENER => VektisType::ONDERNEMING,
                    };

                }
                $counter++;

            }
            if (!$html) {
                User::find(1)->notify(new ProblemNotification("AGBcode: $agbcode niet gevonden op Vektis met all urls"));
                logger("VektisGrabber: AGBcode: $agbcode not found at Vektis with all urls");
                return [
                    'agbcode' => $agbcode,
                    'type' => VektisType::NOT_FOUND,
                    'end' => now(),
                ];
            }


            // HTML parsen
            $dom = new DOMDocument();
            @$dom->loadHTML($html);

            $xpath = new DOMXPath($dom);

            if (str_contains(trim($xpath->evaluate("string(//h1[@class='title'])") ?? ''), "606")) {
                logger("VektisGrabber: AGBcode: $agbcode blokked: $url");
                throw new \Exception("ERROR 606 bij Vektis ophalen voor AGBcode: $agbcode");
            }
            switch ($type) {
                case VektisType::ZORGVERLENER:
                    $this->getNameAndSex($xpath);
                    $this->getQualifications($xpath);
                    $this->getWorkRelations($xpath);
                    $this->data['type'] = VektisType::ZORGVERLENER;
                    break;
                case VektisType::ONDERNEMING:
                    $this->getNameAndAddress($xpath);
                    $this->getQualifications($xpath);
                    $this->getZorgverleners($xpath, VektisType::ONDERNEMING);
                    $this->data['type'] = VektisType::ONDERNEMING;
                    break;
                case VektisType::VESTIGING:
                    $this->getNameAndAddress($xpath);
                    $this->getQualifications($xpath);
                    $this->getZorgverleners($xpath, VektisType::VESTIGING);
                    $this->data['type'] = VektisType::VESTIGING;
                    break;
            }

            if (str_contains($this->data['name'], 'niet gevonden') or !$this->data['name']) {
                logger("VektisGrabber: AGBcode: $agbcode (geen naam gevonden) not found at Vektis with url: $url");
                User::find(1)->notify(new ProblemNotification("AGBcode: $agbcode (geen naam gevonden) op Vektis met url: $url"));
                throw new \Exception("AGBcode: $agbcode niet gevonden op Vektis");
            }
            try {
                $fields = [
                    'is_gp' => $this->data['gp'] ?? YesNoEnum::NO,
                    'vektis_name' => $this->data['name'] ?? '',
                    'type' => $this->data['type'] ?? VektisType::NOT_FOUND,
                    'qualifications' => $this->data['qualifications'] ?? [],
                    'vektis_at' => now(),
                    'started_at' => $this->data['start'] ?? null,
                    //'deleted_at' => $this->data['end'] ?? null,
                ];

                //update the Organization / Requester
                if ($this->data['type'] == VektisType::ZORGVERLENER) {
                    $name = new Name(name: $this->data['name']);
                    $fields['initials'] = $name->initials;
                    $fields['own_lastname'] = $name->own_lastname;
                    $fields['own_prefix'] = $name->own_prefix;
                } elseif ($this->data['type'] == VektisType::ONDERNEMING) {
                    $fields['city'] = $this->data['city'] ?? null;
                    $fields['street'] = $this->data['street'] ?? null;
                    $fields['postcode'] = $this->data['postcode'] ?? null;
                    $fields['building'] = $this->data['building'] ?? null;

                }
                if ($this->data['email']) {
                    $fields['email'] = $this->data['email'];
                }
                if ($this->data['phone']) {
                    $fields['phone'] = $this->data['phone'];
                }

                Requester::updateOrCreate(
                    ['agbcode' => $agbcode],
                    $fields
                );

            } catch (\Exception|\Error $e) {
                logger("VektisGrabber: Error updating database for AGBcode: $agbcode - " . $e->getMessage());
            }
            return $this->data;
        } catch (\Exception $e) {
            User::find(1)->notify(new ProblemNotification("VektisGrabber: Error grabbing AGBcode: $agbcode - " . $e->getMessage()));
            logger("VektisGrabber: Error grabbing AGBcode: $agbcode - " . $e->getMessage());
            return null;
        }
    }


    private function getNameAndSex(DOMXPath $xpath): void
    {
        $naamQuery = "//dt[normalize-space(text())='Naam']/following-sibling::dd[1]";
        $geslachtQuery = "//dt[normalize-space(text())='Geslacht']/following-sibling::dd[1]";

        $naam = trim($xpath->query($naamQuery)->item(0)?->nodeValue ?? '');
        $geslacht = trim($xpath->query($geslachtQuery)->item(0)?->nodeValue ?? '');
        if ($geslacht == 'Vrouwelijk') {
            $this->data['sex'] = PatientSexEnum::FEMALE;
        } elseif ($geslacht == 'Mannelijk') {
            $this->data['sex'] = PatientSexEnum::MALE;
        }
        $this->data['name'] = $naam;
    }

    private function getNameAndAddress(DOMXPath $xpath): void
    {
        //$naamQuery = "//dt[normalize-space(text())='Naam']/following-sibling::dd[1]";
        $naamQuery = "//h2[normalize-space(text())='Basisregistratie']/ancestor::section//dt[normalize-space(text())='Naam']/following-sibling::dd[1]";
        $naam = trim($xpath->query($naamQuery)->item(0)?->nodeValue ?? '');

// 2. Bezoekadres (straat + huisnummer: eerste tekst-node van de p onder Bezoekadres)
        $adresQuery = "//h3[normalize-space(text())='Bezoekadres']/following-sibling::p[1]/text()[1]";
        $adres = trim($xpath->query($adresQuery)->item(0)?->nodeValue ?? '');

// 3. Postcode & Plaats (tweede tekst-node van de p onder Bezoekadres, na de <br>)
        $postcodeCityQuery = "//h3[normalize-space(text())='Bezoekadres']/following-sibling::p[1]/text()[2]";
        $postcodeCity = trim($xpath->query($postcodeCityQuery)->item(0)?->nodeValue ?? '');

// 4. E-mail (div onder E-mail -> Algemeen)
        $emailQuery = "//div[div='E-mail']//div[contains(@class, 'text-nowrap')]";
        $email = trim($xpath->query($emailQuery)->item(0)?->nodeValue ?? '');

// 5. Telefoonnummer (div onder Telefoonnummer -> Algemeen)
        $phoneQuery = "//div[div='Telefoonnummer']//div[contains(@class, 'text-nowrap')]";
        $phone = trim($xpath->query($phoneQuery)->item(0)?->nodeValue ?? '');

        $this->data['name'] = $naam;
        $this->data['address'] = new Address(
            postcode: str($postcodeCity)->before(",")->trim()->toString(),
            city: str($postcodeCity)->afterLast(',')->trim()->toString(),
            street: $adres,
        );
        $this->data['email'] = $email;
        $this->data['phone'] = $phone;
    }

    private function getQualifications(DOMXPath $xpath): void
    {
        $cardQuery = "//h3[normalize-space(text())='Mijn kwalificaties']/following::div[contains(concat(' ', normalize-space(@class), ' '), ' card ')]";
        $cards = $xpath->query($cardQuery);

        $kwalificaties = [];

        // 2. Loop door elke kaart heen
        foreach ($cards as $card) {
            // Relatieve XPath queries (let op de punt '.' aan het begin!):

            // Naam van de kwalificatie (h3 binnen de card)
            $qualificationQuery = ".//h3";

            // Startdatum
            $startQuery = ".//dt[normalize-space(text())='Start']/following-sibling::dd[1]";

            // Einddatum
            $eindeQuery = ".//dt[normalize-space(text())='Einde']/following-sibling::dd[1]";

            $qualification = trim($xpath->query($qualificationQuery, $card)->item(0)?->nodeValue ?? '');
            $start = $this->parseDate(trim($xpath->query($startQuery, $card)->item(0)?->nodeValue ?? ''));
            $einde = $this->parseDate(trim($xpath->query($eindeQuery, $card)->item(0)?->nodeValue ?? ''));
            foreach ($this->vektis_qualifications_list as $code => $name) {
                if ($einde) {
                    continue;
                }
                if (str_contains($qualification, $code)) { //als er een code in de naam staat, voeg deze toe aan de qualifications array
                    $this->data['qualifications'][] = $code;
                }
            }


            $this->data['start'] = $start;
            $this->data['end'] = $einde;

        }
        $this->data['gp'] = $this->isGp($this->data['qualifications']) ? YesNoEnum::YES : YesNoEnum::NO;
    }

    private function getWorkRelations(DOMXPath $xpath): void
    {
// 1. Zoek specifiek naar de <tbody> van de tabel die de juiste caption heeft
        $rijenQuery = "//table[caption[normalize-space(text())='Ik heb een arbeidsrelatie met']]//tbody/tr";
        $rijen = $xpath->query($rijenQuery);

// 2. Loop door de rijen van dát specifieke tabelblok
        foreach ($rijen as $rij) {
            // Naam (zit in een <a> tag in de 1e cel)
            $naamQuery = ".//td[1]";
            // Rol (2e cel)
            $rolQuery = ".//td[2]";
            // AGB-code (3e cel)
            $agbQuery = ".//td[3]";
            // Startdatum (4e cel)
            $startQuery = ".//td[4]";
            // Einddatum (5e cel)
            $eindeQuery = ".//td[5]";

            $naam = trim($xpath->query($naamQuery, $rij)->item(0)?->nodeValue ?? '');
            $rol = trim($xpath->query($rolQuery, $rij)->item(0)?->nodeValue ?? '');
            $agb = trim($xpath->query($agbQuery, $rij)->item(0)?->nodeValue ?? '');
            $start = trim($xpath->query($startQuery, $rij)->item(0)?->nodeValue ?? '');
            $einde = trim($xpath->query($eindeQuery, $rij)->item(0)?->nodeValue ?? '');

            $this->data['relations'][] = [
                'name' => $this->formatName($naam),
                'role' => $rol,
                'agbcode' => $agb,
                'start' => $this->parseDate($start),
                'end' => $this->parseDate($einde),
                'type' => VektisType::ONDERNEMING,
            ];
        }
    }

    private function getZorgverleners(DOMXPath $xpath, VektisType $type): void
    {
// 1. Zoek specifiek naar de <tbody> van de tabel die de juiste caption heeft
        if ($type == VektisType::ONDERNEMING) {
            $rijenQuery = "//table[caption[normalize-space(text())='Bij deze onderneming werken de volgende zorgverleners']]//tbody/tr";
        } else {
            $rijenQuery = "//table[caption[normalize-space(text())='Werkzaam als zorgverlener']]//tbody/tr";
        }
        $rijen = $xpath->query($rijenQuery);

// 2. Loop door de rijen van dát specifieke tabelblok
        foreach ($rijen as $rij) {
            // Naam (zit in een <a> tag in de 1e cel)
            $naamQuery = ".//td[1]";
            // Rol (2e cel)
            $rolQuery = ".//td[2]";
            // AGB-code (3e cel)
            $agbQuery = ".//td[3]";
            // Startdatum (4e cel)
            $startQuery = ".//td[4]";
            // Einddatum (5e cel)
            $eindeQuery = ".//td[5]";

            $naam = trim($xpath->query($naamQuery, $rij)->item(0)?->nodeValue ?? '');
            $rol = trim($xpath->query($rolQuery, $rij)->item(0)?->nodeValue ?? '');
            $agb = trim($xpath->query($agbQuery, $rij)->item(0)?->nodeValue ?? '');
            $start = trim($xpath->query($startQuery, $rij)->item(0)?->nodeValue ?? '');
            $einde = trim($xpath->query($eindeQuery, $rij)->item(0)?->nodeValue ?? '');

            $this->data['relations'][] = [
                'name' => $this->formatName($naam),
                'role' => $rol,
                'agbcode' => $agb,
                'start' => $this->parseDate($start),
                'end' => $this->parseDate($einde),
                'type' => VektisType::ZORGVERLENER,
            ];
        }
    }

    private function getHtml(string $url): string|null
    {
        //return file_get_contents(__DIR__ . "/../../../tests/Unit/Actions/App/vektis-search-zorgverlener.html");
        //return file_get_contents(__DIR__ . "/../../../tests/Unit/Actions/App/vektis-search-onderneming.html");
        // HTML ophalen met cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
        $html = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 200) {
            return $html;
        }
        return null;
    }

    private function parseDate(string $dateString): Carbon|null
    {
        if ($dateString == '-' || !$dateString) {
            return null;
        }
        try {
            $date = Carbon::parse($dateString);
            if ($date->isBefore('1971-01-01')) {
                return Carbon::parse('1971-01-01');
            }
            return $date;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function isGp(array $qualifications): bool
    {
        return in_array('0100', $qualifications) ||
            in_array('0101', $qualifications) ||
            in_array('0102', $qualifications) ||
            in_array('0103', $qualifications) ||
            in_array('0110', $qualifications);
    }

    private function formatName(string $name): string
    {
        return preg_replace('/\s+/', ' ', trim($name));
    }

}
