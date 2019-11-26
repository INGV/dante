<?php

namespace App\Api\v1\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Validator;

class InsertEwModel extends Model
{
    public static function validateInputToContainsEwLogo($input_parameters) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        $validator = Validator::make($input_parameters, [
            'ewLogo'            => 'required|array',
        ], ['required'   => 'The ":attribute" array key must exists and must be an array!'])->validate();
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
    
    public static function validateInputToContainsEwMessage($input_parameters) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        $validator = Validator::make($input_parameters, [
            'ewMessage'            => 'required|array',
        ], ['required'   => 'The ":attribute" array key must exists and must be an array!'])->validate();
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
    
    public static function validateEwLogo($input_parameters) {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);
        
        $validator_default_message  = config('dante.validator_default_messages');        
        $validator = Validator::make($input_parameters, [
            'type'            => 'string|required',
            'module'          => 'string|required',
            'installation'    => 'string|required',
            'user'            => 'string|required',
            'hostname'        => 'string|required',
            'instance'        => 'string|required'
        ], $validator_default_message)->validate();
        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
    }
}