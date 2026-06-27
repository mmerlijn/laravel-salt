<?php

namespace mmerlijn\LaravelSalt\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Enums\NoteSubjectEnum;
use mmerlijn\LaravelSalt\Enums\NoteTypeEnum;
use mmerlijn\LaravelSalt\Models\Note;

class NoteApiController extends Controller
{

    public function index(Request $request)
    {
        if ($request->subject && $request->id) {
            return response()->json(Note::whereSubjectType($request->subject)->whereSubjectId($request->id)->get()->toResourceCollection());
        }
        return collect([]);
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'note' => 'required',
            'subject' => 'required|in:' . implode(",", NoteSubjectEnum::values()), //subject_type
            'id' => 'required|int'                                            //subject_id
        ]);
        $data = ['subject_type' => $request->subject,
            'subject_id' => $request->id,
            'created_by' => auth()->user()?->id ?? 500,
            'type' => $request->type ?: NoteTypeEnum::_,
            'delete_after' => (($request->delete_after ?? 10000) == 10000) ? Carbon::parse('2038-01-19 00:00:00') : Carbon::now()->addDays($request->delete_after),
        ];


        Note::create([...$this->fieldsFilter($request->toArray()), ...$data]);
        return response()->json(Note::whereSubjectType($request->subject)->whereSubjectId($request->id)->get()->toResourceCollection());
    }


    public function show(Note $note)
    {
        //
    }


    public function edit(Note $note, Request $request)
    {
        //
    }

    public function update(Note $note, Request $request)
    {
        //
    }

    public function destroy(Note $note)
    {
        $subject = $note->subject;
        $note->delete();
        return response()->json($subject->notes?->toResourceCollection());
    }

    private function fieldsFilter(array $data)
    {
        return array_filter($data, function ($k) {
            return in_array($k, [
                'subject_id', 'subject_type', 'type', 'note', 'created_by', 'delete_after']);
        }, ARRAY_FILTER_USE_KEY);
    }
}
