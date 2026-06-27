<?php

namespace mmerlijn\LaravelSalt\Actions;

use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Contact;
use mmerlijn\msgRepo\Order;
use mmerlijn\msgRepo\Organization;

class CreateRequestOrganziationCombination
{
    private ?Requester $r = null;
    private ?Requester $o = null;

    public function __invoke(
        ?Order        $msgRepoOrder,
        ?Contact      $msgRepoRequester,
        ?Organization $msgRepoOrganization,
        ?Requester    $requester,
        ?Requester    $organization
    ): void
    {
        try {
            if ($msgRepoOrder) {
                $this->r = Requester::find($msgRepoOrder->requester->agbcode);
                if (!$this->r and $msgRepoOrder->requester->agbcode) {
                    $this->r = new FindOrCreateRequester()($msgRepoOrder->requester);
                }
                $this->o = Requester::find($msgRepoOrder->organization->agbcode);
                if (!$this->o and $msgRepoOrder->organization->agbcode) {
                    $this->o = new FindOrCreateRequester()($msgRepoOrder->organization);
                }
            }
            if ($msgRepoRequester) {
                $this->r = Requester::find($msgRepoRequester->agbcode);
                if (!$this->r and $msgRepoRequester->agbcode) {
                    $this->r = new FindOrCreateRequester()($msgRepoRequester);
                }
            }
            if ($msgRepoOrganization) {
                $this->o = Requester::find($msgRepoOrganization->agbcode);
                if (!$this->o and $msgRepoOrganization->agbcode) {
                    $this->o = new FindOrCreateRequester()($msgRepoOrganization);
                }
            }
            if ($requester) {
                $this->r = $requester;
            }
            if ($organization) {
                $this->o = $organization;
            }
            if ($this->r and $this->o) {
                $this->o->members()->syncWithoutDetaching($this->r->agbcode);
            }

        } catch (\Exception|\Error $exception) {
            logger()->error($exception->getMessage() . "/n" . $exception->getTraceAsString());
        }
    }

}