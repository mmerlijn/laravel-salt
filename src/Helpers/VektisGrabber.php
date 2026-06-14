<?php

namespace mmerlijn\LaravelSalt\Helpers;

use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use mmerlijn\LaravelSalt\Actions\FindOrCreateRequester;
use mmerlijn\LaravelSalt\Helpers\Traits\VektisQualificationsTrait;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Enums\PatientSexEnum;
use mmerlijn\msgRepo\Enums\VektisType;
use mmerlijn\msgRepo\Enums\YesNoEnum;
use mmerlijn\msgRepo\Name;
use mmerlijn\msgRepo\Phone;

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
            $data = ['type' => $type];

            if (str_contains(trim($xpath->evaluate("string(//h1[@class='title'])") ?? ''), "606")) {
                logger("VektisGrabber: AGBcode: $agbcode blokked: $url");
                throw new \Exception("ERROR 606 bij Vektis ophalen voor AGBcode: $agbcode");
            }
            //get name (and sex)
            $data['name'] = trim($xpath->evaluate("string(//div[@class='data-stack__label' and normalize-space()='Naam']/following-sibling::div[@class='data-stack__value'])") ?? '');
            if ($type === VektisType::ZORGVERLENER) {

                $geslacht = trim($xpath->evaluate("string(//div[@class='data-stack__label' and normalize-space()='Geslacht']/following-sibling::div[@class='data-stack__value'])") ?? '');
                if ($geslacht == 'Vrouwelijk') {
                    $data['sex'] = PatientSexEnum::FEMALE;
                } elseif ($geslacht == 'Mannelijk') {
                    $data['sex'] = PatientSexEnum::MALE;
                }
            }
            if (str_contains($data['name'], 'niet gevonden')) {
                logger("VektisGrabber: AGBcode: $agbcode not found at Vektis with url: $url");
                throw new \Exception("AGBcode: $agbcode niet gevonden op Vektis");
            } elseif (!$data['name']) {
                logger("VektisGrabber: No name found for AGBcode: $agbcode with url: $url");
                throw new \Exception("Geen naam gevonden voor AGBcode: $agbcode");
            }

            //get address for organizations
            if ($type === VektisType::ONDERNEMING or $type === VektisType::VESTIGING) {
                $bezoekAdresNode = $xpath->query("//h4[contains(., 'Bezoekadres')]/following-sibling::div[1]")->item(0);
                if ($bezoekAdresNode) {
                    $lines = array_filter(array_map('trim', explode(",", $bezoekAdresNode->textContent)));

                    $data['address'] = new Address(
                        postcode: substr($lines[0] ?? '', -6),
                        city: str($lines[1] ?? '')->beforeLast(' ')->trim()->toString(),
                        street: trim(substr($lines[0] ?? '', 0, -6)),
                    );
                }
                // E-mail Algemeen
                $data['email'] = trim($xpath->evaluate("string(//div[div[@class='mb-2 title title--h5' and contains(.,'E-mail')]]//h4[contains(.,'Algemeen')]/following-sibling::div[@class='text-nowrap'][1])"));
                // Telefoon
                $data['phone'] = trim($xpath->evaluate("string(//div[div[@class='mb-2 title title--h5' and contains(.,'Telefoonnummer')]]//h4[contains(.,'Algemeen')]/following-sibling::div[@class='text-nowrap'][1])"));
            }

            // Get type of care provider for caregivers
            $qualification_data = $xpath->evaluate("string(//div[contains(@class, 'agb-code-container')][.//h3[contains(., 'Mijn kwalificaties')]])");
            $data['qualifications'] = [];
            foreach ($this->vektis_qualifications_list as $code => $name) {
                if (str_contains($qualification_data, $code)) {
                    $data['qualifications'][] = $code;
                }
            }

            $end = trim(trim($xpath->evaluate("string(//div[contains(@class, 'agb-code-container')][.//h3[contains(., 'Mijn kwalificaties')]]//div[@class='data-stack__label' and contains(.,'Einde')]/following-sibling::div[@class='data-stack__value'])")), "-");
            $start = trim($xpath->evaluate("string(//div[contains(@class, 'agb-code-container')][.//h3[contains(., 'Mijn kwalificaties')]]//div[@class='data-stack__label' and contains(.,'Start')]/following-sibling::div[@class='data-stack__value'])"));
            if ($end) {
                $data['end'] = $this->parseDate($end);
            }
            if ($start) {
                $data['start'] = $this->parseDate($start);
            }
            $data['gp'] = (Caregiver::isGp($data['qualifications'])) ? YesNoEnum::YES : YesNoEnum::NO;

            //relations
            $relations = [];
            if ($type === VektisType::ZORGVERLENER) {
                foreach ($xpath->query("//table[contains(@class, 'card-table')][.//caption[contains(., 'Ik heb een arbeidsrelatie met')]]/tbody/tr") as $tr) {
                    $relation = $this->relationsFromTr($tr, $xpath, VektisType::ONDERNEMING);
                    if ($relation) {
                        $relations[] = $relation;
                    }
                }
            } elseif ($type === VektisType::ONDERNEMING) {
                //veel resultaten
                //caregivers
                if ($xpath->query("//table[contains(@id, 'DataTables_Table_0')]")->length > 0) {
                    foreach ($xpath->query("//table[contains(@id, 'DataTables_Table_0')]/tbody/tr") as $tr) {
                        $relation = $this->relationsFromTr($tr, $xpath, VektisType::ZORGVERLENER);
                        if ($relation) {
                            $relations[] = $relation;
                        }
                    }
                } else {
                    foreach ($xpath->query("//table[contains(@class, 'card-table')][.//caption[contains(., 'Bij deze onderneming werken de volgende zorgverleners')]]/tbody/tr") as $tr) {
                        $relation = $this->relationsFromTr($tr, $xpath, VektisType::ZORGVERLENER);
                        if ($relation) {
                            $relations[] = $relation;
                        }
                    }
                }
                // other organizations
                //veel resultaten
                if ($xpath->query("//table[contains(@id, 'DataTables_Table_1')]")->length > 0) {
                    //loop through rows of the table
                    foreach ($xpath->query("//table[contains(@id, 'DataTables_Table_1')]/tbody/tr") as $tr) {
                        $relation = $this->relationsFromTr($tr, $xpath, VektisType::ONDERNEMING);
                        if ($relation) {
                            $relations[] = $relation;
                        }
                    }
                } else {
                    foreach ($xpath->query("//table[contains(@class, 'card-table')][.//caption[contains(., 'Deze onderneming heeft een relatie met de volgende ondernemingen')]]/tbody/tr") as $tr) {
                        $relation = $this->relationsFromTr($tr, $xpath, VektisType::ONDERNEMING);
                        if ($relation) {
                            $relations[] = $relation;
                        }
                    }
                }
            } else {//vestiging
                //caregivers
                foreach ($xpath->query("//table[contains(@class, 'card-table')][.//caption[contains(., 'Werkzaam als zorgverlener')]]/tbody/tr") as $tr) {
                    $relation = $this->relationsFromTr($tr, $xpath, VektisType::ZORGVERLENER);
                    if ($relation) {
                        $relations[] = $relation;
                    }
                }
            }
            $data['relations'] = $relations;
            try {
                //update the Organization / Requester
                    $requester = new FindOrCreateRequester()($data,true);

            } catch (\Exception|\Error $e) {
                logger("VektisGrabber: Error updating database for AGBcode: $agbcode - " . $e->getMessage());
            }

            return $data;
        } catch (\Exception $e) {
            logger("VektisGrabber: Error grabbing AGBcode: $agbcode - " . $e->getMessage());
            return null;
        }
    }

    private function relationsFromTr($tr, DOMXPath $xpath, VektisType $vektisType): array|null
    {
        $tds = $xpath->query("td", $tr);
        $name = trim($tds->item(0)->textContent);
        $role = trim($tds->item(1)->textContent);
        $agbcode = trim($tds->item(2)->textContent);
        $start = trim($tds->item(3)->textContent);
        $end = trim(trim($tds->item(4)->textContent), "-");
        if ($agbcode) {
            return [
                'name' => $name,
                'role' => $role,
                'agbcode' => $agbcode,
                'start' => $this->parseDate($start),
                'end' => $this->parseDate($end),
                'type' => $vektisType,
            ];
        }
        return null;
    }

    private function getHtml(string $url): string|null
    {

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
}
