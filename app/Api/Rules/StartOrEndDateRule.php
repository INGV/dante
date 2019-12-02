<?php

namespace App\Api\Rules;

use Illuminate\Contracts\Validation\Rule;

use Illuminate\Support\Facades\Validator;

class StartOrEndDateRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
		$return1 = true;
		$return2 = true;
		
        /* Check validation on 'YYYY-MM-DD\THH:ii:ss' */
        $validator = Validator::make([$attribute => $value], [
			$attribute		=> 'date_format:"Y-m-d\TH:i:s"',
        ]);
        if ($validator->fails()) {
            $return1 = false;
        }
		
		/* Check validation on 'YYYY-MM-DD' */
        $validator = Validator::make([$attribute => $value], [
			$attribute		=> 'date_format:"Y-m-d"',
        ]);		
        if ($validator->fails()) {
            $return2 = false;
        }
		
		/* Check value to return */
		if ( $return1 === false && $return2 === false ) {
			return false;
		} else {
			return true;
		}
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The ":attribute" does not match the format "Y-m-d\TH:i:s" or "Y-m-d"';
    }
}
