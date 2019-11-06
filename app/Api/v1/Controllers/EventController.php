<?php

namespace App\Api\v1\Controllers;

use Illuminate\Http\Request;

use App\Api\v1\Models\EventModel;
use App\Api\v1\Resources\EventResource;
use Illuminate\Support\Facades\Validator;
use App\Api\v1\Controllers\DanteBaseController;

class EventController extends DanteBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        $data = EventResource::collection(EventModel::paginate(config('dante.default_params.limit')));
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        /* Validator */
        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');
        $validator = Validator::make($request->all(), [
            'id_locator'        => 'integer',
            'fk_pref_hyp'       => 'nullable|integer',
            'fk_pref_mag'       => 'nullable|integer',
            'fk_events_group'   => 'integer',
            'type_group'        => 'integer',
            'fk_type_event'     => 'required|integer|exists:type_event,id',
            'fk_provenance'     => 'required|integer|exists:provenance,id'
        ], $validator_default_message)->validate();
        /*
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        */
   
        /* Store record */
        $event = EventModel::create($request->all());
        
        return $event;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(EventModel $id)
    {
        return new EventResource($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        \Log::debug(" id=$id");
        
        $event = EventModel::findOrFail($id);
        
        // check if currently authenticated user is the owner of the book
        /*
        if ($request->user()->id !== $book->user_id) {
            return response()->json(['error' => 'You can only edit your own books.'], 403);
        }
        */

        $event->update($request->only(['title', 'description']));

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        \Log::debug(" id=$id");
        
        $event = EventModel::findOrFail($id);
        
        $event->delete();

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return response()->json(null, 204);
    }
}
