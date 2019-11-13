<?php

namespace App\Api\v1\Controllers\Tables;

use Illuminate\Http\Request;

use App\Api\v1\Models\Tables\TypeMagnitudeModel;
use Illuminate\Support\Facades\Validator;
use App\Api\v1\Controllers\DanteBaseController;

class TypeMagnitudeController extends DanteBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        $data = $this->paginateCache(TypeMagnitudeModel::class);
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
        $validator_store_rules      = (new TypeMagnitudeModel)->getValidatorRulesForStore();
        $validator = Validator::make($request->all(), $validator_store_rules, $validator_default_message)->validate();
        
        /* Create record */
        $type_magnitude = TypeMagnitudeModel::create($request->all());
        \Log::debug(' $type_magnitude->id = '.$type_magnitude->id);
        
        /* Get complete record just inserted */
        $data = TypeMagnitudeModel::findOrFail($type_magnitude->id);
        
        /* Set '$data->wasRecentlyCreated' attribute equal to '$type_magnitude->wasRecentlyCreated'; when it is 'true', the returned http_status_code will be '201' */
        $data->wasRecentlyCreated = $type_magnitude->wasRecentlyCreated;

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        \Log::debug(" id=$id");
        
        /* Get record */
        $data = TypeMagnitudeModel::findOrFail($id);
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $data;
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
        
        /* Validator */
        $validator_default_check    = config('dante.validator_default_check');
        $validator_default_message  = config('dante.validator_default_messages');
        $validator_update_rules     = (new TypeMagnitudeModel)->getValidatorRulesForUpdate();
        $validator = Validator::make($request->all(), $validator_update_rules, $validator_default_message)->validate();
        
        /* Get record to update */
        $type_magnitude = TypeMagnitudeModel::findOrFail($id);
        
        /* Updated record and save it */
        $type_magnitude->fill($request->all());
        $type_magnitude->save();
        
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $type_magnitude;
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
        
        /* Get record to destroy */
        $type_magnitude = TypeMagnitudeModel::findOrFail($id);
        
        /* Destroy */
        $type_magnitude->delete();

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return response()->json(null, 204);
    }
}
