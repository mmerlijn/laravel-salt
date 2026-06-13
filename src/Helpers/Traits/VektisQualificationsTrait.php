<?php

namespace mmerlijn\LaravelSalt\Helpers\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait VektisQualificationsTrait
{
    public array $vektis_qualifications_list = [
        "0100" => "Huisartspraktijk",
        "0101" => "Huisarts",
        "0102" => "Huisarts, medicatiebeoordeling chronisch UR-geneesmiddelgebr",
        "0103" => "Huisarts, Uitsluitend waarnemend",
        "0110" => "Huisarts, Apotheekhoudend",
        "0200" => "Apothekers",
        "0201" => "Apotheker, medicatiebeoordeling chronisch UR-geneesmiddelgeb",
        "0301" => "Oogheelkunde",
        "0302" => "Keel-, neus- en oorheelkunde",
        "0303" => "Chirurgie (Heelkunde)",
        "0304" => "Plastische chirurgie",
        "0305" => "Orthopedie",
        "0306" => "Urologie",
        "0307" => "Obstetrie en gynaecologie",
        "0308" => "Neurochirurgie",
        "0310" => "Dermatologie en Venerologie",
        "0313" => "Interne geneeskunde",
        "0316" => "Kindergeneeskunde",
        "0318" => "Gastro-enterologie (Maag-darm-lever-arts)",
        "0320" => "Cardiologie",
        "0322" => "Longziekten",
        "0324" => "Reumatologie",
        "0327" => "Revalidatie",
        "0328" => "Cardio-thoracale chirurgie",
        "0329" => "Psychiatrie",
        "0330" => "Neurologie",
        "0335" => "Geriatrie",
        "0361" => "Radiotherapie",
        "0362" => "Radiologie",
        "0363" => "Nucleaire geneeskunde",
        "0386" => "Klinische chemie",
        "0387" => "Medische microbiologie",
        "0388" => "Pathologie",
        "0389" => "Anesthesiologie",
        "0390" => "Klinische genetica",
        "0401" => "Fysiotherapie",
        "0403" => "Kinderfysiotherapie",
        "0404" => "Manuele therapie",
        "0405" => "Oedeemtherapie",
        "0406" => "Bekkenfysiotherapie",
        "0407" => "Fysiotherapie in de geriatrie",
        "0408" => "Sportfysiotherapie",
        "0409" => "Psychosomatische fysiotherapie",
        "0410" => "Oro-faciale fysiotherapie",
        "0411" => "Arbeids- en/of bedrijfsfysiotherapie",
        "0412" => "Oncologie fysiotherapie",
        "0477" => "Fysiotherapie Leefstijlcoach",
        "0478" => "Hart-, vaat- en longfysiotherapie",
        "0479" => "Hart fysiotherapie",
        "0480" => "Vaat fysiotherapie",
        "0481" => "Long fysiotherapie",
        "0482" => "Fysiotherapie Kinderleefstijlcoach",
        "0483" => "Valpreventie fysiotherapie",
        "0484" => "Reumatoïde artritis fysiotherapie",
        "0485" => "Axiale spondyloartritis fysiotherapie",
        "0501" => "Logopedie",
        "0503" => "Stottertherapie",
        "0504" => "Preverbale logopedie",
        "0506" => "Afasie",
        "0507" => "Hanen-ouderprogramma",
        "0508" => "Integraal Zorg Stotteren (IZS)",
        "0700" => "Oefentherapie",
        "0703" => "Kinderoefentherapie",
        "0704" => "Psychosomatische oefentherapie",
        "0718" => "Oefentherapie Leefstijlcoach",
        "0719" => "Bekkenoefentherapie",
        "0720" => "Geriatrie Oefentherapie",
        "0721" => "Oefentherapie Kinderleefstijlcoach",
        "0722" => "Valpreventie Oefentherapie",
        "0723" => "Reumatoïde artritis indicatie 3 Oefentherapie",
        "0800" => "Verloskundige",
        //"0804" => "Uitwendige versie",
        //"0805" => "Eerste trimester SEO",
        //"0806" => "Tweede trimester SEO",
        //"0807" => "Biometrie echo",
        //"0808" => "Termijn echo",
        //"0809" => "Counseling prenatale screening",
        //"0810" => "Anticonceptie",
        //"0811" => "Antenataal CTG",
        "1100" => "Mondziekten en kaakchirurgie",
        "1200" => "Tandartsen",
        "1300" => "Dentomaxillaire orthopaedie",
        "1402" => "Arts bedrijfsgeneeskunde",
        "1403" => "Arts verzekeringsgeneeskunde",
        "2400" => "Diëtisten",
        //"2402" => "Diëtist Leefstijlcoach",
        //"2403" => "Diëtist Kinderleefstijlcoach",
        "2600" => "Podotherapie",
        "3301" => "Kraamzorg",
        "4400" => "Optometristen",
        "4402" => "Orthoptisten",
        "5301" => "Ketenzorg DM type 2",
        "5302" => "Ketenzorg CVR",
        "5303" => "Ketenzorg COPD",
        "5316" => "Samenwerkingsverband GLI",
        "5700" => "Physician Assistant",
        "8401" => "Arts, chiropractie",
        "8402" => "Arts, musculoskeletale geneeskunde",
        "8403" => "Arts/tandarts, Acupunctuur",
        "8404" => "Arts, iriscopie",
        "8405" => "Arts, homeopathie",
        "8406" => "Arts, natuurgeneeskunde",
        "8407" => "Arts, antroposofische geneeskunde",
        "8408" => "Moermantherapie",
        "8409" => "Arts, enzymtherapie",
        "8410" => "Arts, manuele geneeskunde",
        "8411" => "Arts, haptotherapie",
        "8412" => "Arts, osteopathie",
        "8413" => "Flebologie / proctologie",
        "8414" => "Arts, Orthomoleculaire geneeskunde",
        "8415" => "Neurale therapie",
        "8416" => "Sportgeneeskunde",
        "8417" => "Arts Spoedeisende Hulp (SEH)",
        "8418" => "Specialist ouderengeneeskunde",
        "8421" => "SCEN-arts",
        "8422" => "Orthopedische geneeskunde",
        "8423" => "Arts Tuberculosebestrijding",
        "8424" => "Verslavingsarts",
        "8425" => "Basisarts",
        "8426" => "Arts Verstandelijk gehandicapten",
        "8428" => "Jeugdarts",
        "8429" => "Klinisch fysicus audioloog",
        "8430" => "Arts Infectieziektebestrijding",
        "8431" => "Medisch seksuoloog",
        "8432" => "Klinisch technoloog",
        "8433" => "Cosmetisch arts",
        "8434" => "Ziekenhuisarts",
        "8435" => "Arts Maatschappij en Gezondheid",
        "8436" => "Arts Donorgeneeskunde",
        "8437" => "Forensisch arts",
        "8700" => "Mondhygiënisten",
        "8800" => "Ergotherapie",
        "8900" => "Schoonheidsspecialist",
        "9101" => "Specialisatie Diabetes",
        "9102" => "Specialisatie Continentie",
        "9104" => "Specialisatie Wond",
        "9105" => "Specialisatie Stoma",
        "9106" => "Specialisatie Long",
        "9107" => "Specialisatie Oncologie",
        "9108" => "Specialisatie Transfer",
        "9109" => "Verpleegkundigen niveau 4",
        "9110" => "Verpleegkundigen niveau 6 of hoger",
        "9111" => "Verzorgenden",
        "9112" => "Verpleegkundigen niveau 4 PGB",
        "9113" => "Verpleegkundigen niveau 6 PGB",
        "9116" => "Sociaal Psychiatrisch Verpleegkundigen",
        "9117" => "Jeugdverpleegkundigen",
        "9134" => "Verpleegkundig specialist, Geestelijke Gezondheidszorg",
        "9135" => "Verpleegkundig specialist, Algemene Gezondheidszorg",
        "9136" => "Verpleegkundigen niveau 5",
    ];

    public function getQualificationByCode(string $code): string
    {
        return $this->vektis_qualifications_list[$code] ?? 'Onbekend';
    }

    public function getAllQualifications(array $codes): array
    {
        $qualifications = [];
        foreach ($codes as $code) {
            $qualifications[] = $this->getQualificationByCode($code);
        }
        return $qualifications;
    }

    protected function can(): Attribute
    {

        return Attribute::make(
            get: fn(mixed $value, array $attributes) => array_map(fn($item) => $this->getQualificationByCode($item), json_decode($attributes['qualifications']))
            ,
        );
    }

    protected static function isGp(array $qualifications): bool
    {
        return in_array('0100', $qualifications) ||
            in_array('0101', $qualifications) ||
            in_array('0102', $qualifications) ||
            in_array('0103', $qualifications) ||
            in_array('0110', $qualifications);
    }
}
