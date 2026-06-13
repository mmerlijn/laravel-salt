<?php

namespace mmerlijn\LaravelSalt\Observers;




use mmerlijn\LaravelSalt\Jobs\GetCaregiverJob;
use mmerlijn\LaravelSalt\Models\Requester;
use mmerlijn\msgRepo\Enums\VektisType;

class RequesterObserver
{

    public function creating(Requester $requester): void
    {
//        $r = $requester->relations ?? [];
//        $r[] = $requester->agbcode;
//        $r = array_unique($r);
//        sort($r);
//        foreach (Requester::whereIn('agbcode', $r)->where('agbcode', '<>', $requester->agbcode)->get() as $relation) {
//            $relation->relations = $r;
//            $relation->saveQuietly(); //without oberservers called
//        }
//        $requester->relations = $r;
    }
    public function created(Requester $requester): void
    {
        if(config('laravel_salt.vektis', false)) {
            GetCaregiverJob::dispatch(VektisType::ZORGVERLENER, $requester->agbcode);
        }
    }

    public function updating(Requester $requester): void
    {
        //if ($requester->isDirty('relations')) { //
        //    $r = $requester->relations ?? [];
//
        //    $r[] = $requester->agbcode;
        //    $r = array_unique($r);
        //    sort($r);
        //    //sync new relations
        //    foreach (Requester::whereIn('agbcode', $r)->where('agbcode', "<>", $requester->agbcode)->get() as $related) {
        //        $related->relations = $r;
        //        $related->saveQuietly();
        //    }
        //    $requester->relations = $r;
        //    $diff = array_diff($requester->getOriginal('relations'), $r);
        //    //sync removed relations
        //    foreach (Requester::whereIn('agbcode', $diff)->get() as $related) {
        //        $r = $related->relations;
        //        if (($key = array_search($requester->agbcode, $r)) !== false) {
        //            unset($r[$key]);
        //        }
        //        $related->relations = $r;
        //        $related->saveQuietly(); //without oberservers called
        //    }
        //}
    }

    public function deleting(Requester $requester)
    {
        //$r = [];
//
        //$r[] = $requester->agbcode;
        //$requester->relations = $r;
        //$diff = array_diff($requester->getOriginal('relations') ?? [], $r);
        ////sync removed relations
        //foreach (Requester::whereIn('agbcode', $diff)->get() as $related) {
        //    $r = $related->relations;
        //    if (($key = array_search($requester->agbcode, $r)) !== false) {
        //        unset($r[$key]);
        //    }
        //    $related->relations = $r;
        //    $related->saveQuietly(); //without oberservers called
        //}
    }
}
