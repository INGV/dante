<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dante version
    |--------------------------------------------------------------------------
    |
    */
    'version' => trim(yaml_parse_file(base_path().'/publiccode.yml')['softwareVersion']),

    /*
    |--------------------------------------------------------------------------
    | Enable or disable query cache
    |--------------------------------------------------------------------------
    |
    */
    'enableQueryCache' => env('ENABLE_QUERY_CACHE', 0),

    /*
    |--------------------------------------------------------------------------
    | Enable or disable request URL cache
    |--------------------------------------------------------------------------
    |
    */
    'enableCache' => env('ENABLE_CACHE', 0),
    
    /*
    |--------------------------------------------------------------------------
    | Enable or disable clustering
    |--------------------------------------------------------------------------
    |
    */
    'enableClustering' => env('ENABLE_CLUSTERING', 0),

    /*
    |--------------------------------------------------------------------------
    | Enable or disable DB query log into 'log_file'
    |--------------------------------------------------------------------------
    |
    */
    'logQuery' => env('LOG_QUERY', false),
    
    /*
    |--------------------------------------------------------------------------
    | Set WS URI
    |--------------------------------------------------------------------------
    |
    */
    'uri_ws__boundaries__get_region_name' => env('URI_WS__BOUNDARIES__GET_REGION_NAME'),
    
    /*
    |--------------------------------------------------------------------------
    | Validator static parameters
    |--------------------------------------------------------------------------
    |
    */
    'validator_default_check' => [
        'data_time_with_msec'       => 'date|required',
        'starttime'                 => 'date_format:"Y-m-d\TH:i:s"|required',
        'endtime'                   => 'date_format:"Y-m-d\TH:i:s"|required',
        'net'                       => 'string|between:1,2',
        'sta'                       => 'string',
        'cha'                       => 'string|size:3',
        'loc'                       => 'nullable|string',
        'model'                     => 'required|string',
        'sqlx_type'                 => 'in:P,F',
        'format'                    => 'in:json,text',
        'formatted'                 => 'boolean',
        'page'                      => 'numeric',
        //'lat'                     => array('numeric','regex:'."/^\-?[0-8]?[0-9](\.[0-9]*)?$|^\-?90(\.0*)?$/"),
        'lat'                       => 'numeric|min:-90|max:90',
        //'lon'                     => array('numeric','regex:'."/^-?180(\.0*)?$|^-?1[0-7][0-9](\.[0-9]*)?$|^-?[0-9]?[0-9](\.[0-9]*)?$/"),
        'lon'                       => 'numeric|min:-180|max:180',
        'radius'                    => 'numeric|digits_between:0,180',
        'radiuskm'                  => 'numeric|digits_between:0,800',
        'magnitude'                 => 'numeric',
        'error'                     => 'numeric|nullable',
        'distance'                  => 'numeric|nullable',
        'nsta'                      => 'numeric|nullable',
        'ncha'                      => 'numeric|nullable',
        'nsta_used'                 => 'numeric|nullable',
        'mag_quality'               => 'string|min:1|max:2|nullable',
        'type_magnitude'            => 'string|required',
        'azimuth'                   => 'numeric|nullable',
        'depth'                     => 'numeric|min:-10|max:10000',
        'elev'                      => 'numeric',
        'limit'                     => 'numeric|digits_between:0,10000',
        'orderby'                   => 'in:id,id-asc,id-desc,modified,modified-asc,modified-desc',
        'weight_integer'            => 'integer|nullable|in:0,1,2,3,4',
        'weight_float'              => 'numeric|nullable',
        'provenance__name'          => 'string',
		'provenance__priority'      => 'integer',
        'provenance__instance'      => 'required|string',
        'provenance__softwarename'  => 'required|string',
        'provenance__username'      => 'nullable|string',
        'provenance__hostname'      => 'nullable|string',
        'provenance__description'   => 'nullable|string',
		'event_id'					=> 'required|integer|exists:event,id',
        'hypocenter_id'             => 'required|integer|exists:hypocenter,id',
		'provenance_id'             => 'required|integer|exists:provenance,id',
		'model_id'					=> 'required|integer|exists:model,id',
		'loc_program_id'			=> 'required|integer|exists:loc_program,id',
		'type_hypocenter_id'		=> 'required|integer|exists:type_hypocenter,id',
		'type_magnitude_id'			=> 'required|integer|exists:type_magnitude,id',
		'magnitude_id'				=> 'required|integer|exists:magnitude,id',
		'amplitude_id'				=> 'required|integer|exists:amplitude,id',
		'scnl_id'					=> 'required|integer|exists:scnl,id',
		'strongmotion_id'			=> 'required|integer|exists:strongmotion,id',
        'event__fk_events_group'    => 'integer|exists:event,id',
        'data'                      => 'required|array',
    ],
    'validator_default_messages' => [
        'required'              => 'The \':attribute\' field is required',
		'required_with'         => 'The \':attribute\' field is required when \':values\' is present.',
        'in'                    => 'The \':attribute\' must be :values',
        'boolean'               => 'The \':attribute\' must be \'true\' or \'false\'',
        'numeric'               => 'The \':attribute\' must be "numeric"',
        'digits_between'        => 'The \':attribute\' must be between :min and :max',
		'between'				=> 'The \':attribute\' is not between \':min\' and \':max\' char(s).',
        'string'                => 'The \':attribute\' must be a string',
        'integer'               => 'The \':attribute\' must be an integer',
        'nullable'              => 'The \':attribute\' could be null',
        'unique'                => 'The \':attribute\' field is a duplicate entry',
        'same'                  => 'The \':attribute\' and \':other\' must match',
        'exists'                => 'The \':attribute\' does not exist',
        'date_format'           => 'The \':attribute\' does not match the format :format',
		'size'					=> 'The \':attribute\' must be exactly :size.',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | rfc7231 HTTP Status Code mapping
    |--------------------------------------------------------------------------
    |
    */
    'rfc7231'  => [
        400   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.1',
        402   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.2',
        403   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.3',
        404   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.4',
        405   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.5',
        406   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.6',
        408   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.7',
        409   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.8',
        410   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.9',
        411   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.10',
        412   =>  'https://tools.ietf.org/html/rfc4918#section-12.1',
        413   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.11',
        414   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.12',
        415   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.13',
        417   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.14',
        422   =>  'https://tools.ietf.org/html/rfc4918#section-11.2',
        423   =>  'https://tools.ietf.org/html/rfc4918#section-11.3',
        424   =>  'https://tools.ietf.org/html/rfc4918#section-11.4',
        426   =>  'https://tools.ietf.org/html/rfc7231#section-6.5.15',
        500   =>  'https://tools.ietf.org/html/rfc7231#section-6.6.1',
        501   =>  'https://tools.ietf.org/html/rfc7231#section-6.6.2',
        502   =>  'https://tools.ietf.org/html/rfc7231#section-6.6.3',
        503   =>  'https://tools.ietf.org/html/rfc7231#section-6.6.4',
        504   =>  'https://tools.ietf.org/html/rfc7231#section-6.6.5',
        505   =>  'https://tools.ietf.org/html/rfc7231#section-6.6.6',
        507   =>  'https://tools.ietf.org/html/rfc4918#section-11.5',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | default params
    |--------------------------------------------------------------------------
    |
    */
    'default_params' => [
        'limit'                 => 1000,
        //'formatted'             => env('APP_DEBUG', true),
    ],
];
