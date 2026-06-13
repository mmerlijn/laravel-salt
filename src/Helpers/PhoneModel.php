<?php

namespace mmerlijn\LaravelSalt\Helpers;


class PhoneModel
{
    private array $netnummers = [
        "010", "0111", "0113", "0114", "0115", "0117", "0118", "013", "014", "015", "0161", "0162", "0164", "0165", "0166", "0167", "0168", "0172", "0174", "0180", "0181", "0182", "0183", "0184", "0186", "0187",
        "020", "0222", "0223", "0224", "0226", "0227", "0228", "0229", "023", "024", "0251", "0252", "0255", "026", "027", "0294", "0297", "0299",
        "030", "0313", "0314", "0315", "0316", "0317", "0318", "0320", "0321", "033", "0341", "0342", "0343", "0344", "0345", "0346", "0347", "0348", "035", "036", "038",
        "040", "0411", "0412", "0413", "0416", "0418", "043", "044", "045", "046", "0475", "0478", "0481", "0485", "0486", "0487", "0488", "0492", "0493", "0495", "0497", "0499",
        "050", "0511", "0512", "0513", "0514", "0515", "0516", "0517", "0518", "0519", "0521", "0522", "0523", "0524", "0525", "0527", "0528", "0529", "053", "0541", "0543", "0544", "0545", "0546", "0547", "0548", "055", "0561", "0562", "0566", "0570", "0571", "0572", "0573", "0575", "0577", "0578", "058", "0591", "0592", "0593", "0594", "0595", "0596", "0597", "0598", "0599",
        "06",
        "070", "071", "072", "073", "074", "075", "076", "077", "078", "079",
        "0800", "082", "084", "085", "087", "088",
        "0900", "0906", "0909", "091", "0970", "0971", "0972", "0973", "0974", "0975", "0976", "0977", "0978"
    ];
    private array $countryPrefixes = ["nl" => "+31", "be" => "+32", "fr" => "+33", "it" => "+39", "de" => "+49"];

    public function __construct(public ?string $phone, public ?string $city = null)
    {
    }

    public function get(): string
    {
        foreach ($this->netnummers as $netnummer) {
            if (str_starts_with($this->phone, $netnummer)) {
                $l = strlen($netnummer);
                $n = ($l == 2 or $l == 3) ? 4 : 3;
                return preg_replace('~^.{' . $l . '}|.{' . $n . '}(?!$)~', '$0 ', $this->phone);
            }
        }
        return preg_replace('~^.{3}|.{4}(?!$)~', '$0 ', $this->phone);
    }

    public function set(): string
    {
        $value = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($value) > 10) //remove country prefix
            $value = "0" . preg_replace('/^([3-4]{1}\d{1}[0]?)/', '', $value);
        if (strlen($value) < 8 and $value) {
            $value = $this->netnummersLijst($this->city) . $value;
        }
        return (string)$value;
    }

    public function smsPhone(string $countryCode = "nl"): string
    {

        $phone = $this->set();
        if (!preg_match('/(06){1}[0-9]{8}/', $phone)) {
            throw new \Exception('Not a mobile phone: ' . $phone);
        }
        if (!array_key_exists($countryCode, $this->countryPrefixes)) {
            throw new \Exception('Not a valid country code: ' . $countryCode);
        }
        return $this->withCountryCode($countryCode);

    }

    public function withCountryCode(string $countryCode = "nl"): string
    {
        return $this->countryPrefixes[strtolower($countryCode)] . preg_replace('/^(0)*/', '', $this->phone);
    }

    public function __toString(): string
    {
        return $this->get();
    }


    private function netnummersLijst($city): string
    {
        switch (strtolower($city)) {
            case "purmerend":
            case "monnickendam":
            case "marken":
            case "edam":
            case "graft":
            case "hobrede":
            case "katwoude":
            case "kwadijk":
            case "noordbeemster":
            case "westbeemster":
            case "oosthuizen":
            case "purmer":
            case "purmerland":
            case "volendam":
            case "wijdewormer":
            case "middenbeemster":
            case "zuidoostbeemster":
                return "0299";
                break;
            case "hoorn":
            case "zwaag":
                return "0229";
                break;
            case "amstelveen":
            case "broek in waterland":
            case "amsterdam":
            case "diemen":
            case "ilpendam":
            case "landsmeer":
            case "watergang":
            case "halfweg":
                return "020";
                break;
            case "castricum":
            case "bakkem":
            case "uitgeest":
            case "akersloot":
            case "beverwijk":
            case "heemskerk":
                return "0251";
                break;
            case "alkmaar":
            case "egmond aan den hoef":
            case "egmond aan zee":
            case "egmond-binnen":
            case "groet":
            case "heerhugowaard":
            case "heiloo":
            case "limmen":
            case "zuidschermer":
                return "072";
                break;
            case "ijmuiden":
                return "0255";
                break;
            case "assendelft":
            case "jisp":
            case "koog aan de zaan":
            case "krommenie":
            case "oostzaan":
            case "west-grafdijk":
            case "westknollendam":
            case "westzaan":
            case "wormer":
            case "wormerveer":
            case "zaandam":
            case "zaandijk":
            case "zaanstad":
                return "075";
                break;
            case "haarlem":
            case "heemstede":
                return "023";
                break;
            default:
                return "";
        }
    }
}