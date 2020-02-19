<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Symfony\Component\HttpFoundation\Response;
use App\Dante\Events\ExceptionWasThrownEvent;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        \Log::debug("START - ".__CLASS__.' -> '.__FUNCTION__);

        /* 1/2 - Build array to trigger the Event 'DanteExceptionWasThrownEvent' to send email */
        $eventArray['url']                  = $request->fullUrl();
        $eventArray['random_string']        = config('dante.random_string');
        $eventArray['log_file']             = config('dante.log_file');
        
        /* 1/2 - Build RCF7807 Problem Details for HTTP APIs: https://tools.ietf.org/html/rfc7807 */
        $rcf7807Output['type']              = 'unknown';
        $rcf7807Output['title']             = 'unknown';
        $rcf7807Output['status']            = 500;
        $rcf7807Output['detail']            = 'unknown';
        $rcf7807Output['instance']          = $request->fullUrl();
        $rcf7807Output['version']           = config('dante.version');
        $rcf7807Output['request_submitted'] = date("Y-m-d\TH:m:s T");
        
        /* Set header to get JSON render exception */
        $request->headers->set('Accept', 'application/json');

        /* Get default render */
        $defaultRender = parent::render($request, $exception);

        /* Get status, status code and status message */
        $status                             = (parent::isHttpException($exception) ? $exception->getStatusCode() : ($defaultRender->getStatusCode() ? $defaultRender->getStatusCode() : 500));
        $statusMessage                      = Response::$statusTexts[$status] ? Response::$statusTexts[$status] : '--';
        $message                            = $defaultRender->getData()->message;        

        /* 2/2 - Build RCF7807 Problem Details for HTTP APIs: https://tools.ietf.org/html/rfc7807 */
        $rcf7807Output['type']              = config('dante.rfc7231')[$status] ?? 'about:blank';
        $rcf7807Output['title']             = $statusMessage;
        $rcf7807Output['status']            = $status;
        $rcf7807Output['detail']            = $message ? $message : $statusMessage;
        
        /* 2/2 - Build array to trigger the Event 'DanteExceptionWasThrownEvent' to send email */
        $eventArray['message']              = $exception->getMessage() ? $exception->getMessage() : '--';
        $eventArray['status']               = $status;
        $eventArray['statusMessage']        = $statusMessage;
        $eventArray['message']              .= ' - '.$exception->getFile().':'.$exception->getLine();

        /* Set header to 'application/problem+json' */
        $defaultRender->header('Content-type','application/problem+json');

        /* Set errors array */ 
        if (isset($defaultRender->getData()->errors)) {
            $rcf7807Output['errors']     = (array)$defaultRender->getData()->errors;
        }

        /* Add debug */
        if ( config('app.debug') ) {
            $rcf7807Output['debug']     = (array)$defaultRender->getData();
        }

        /* Set output with new fields */
        $defaultRender->setData($rcf7807Output);

        /* set output */
        $prepareOutput = $defaultRender;

        /* print into log */
        \Log::debug(" exception:", $rcf7807Output);

        /* Trigger the event */
        event(new ExceptionWasThrownEvent($eventArray));

        \Log::debug("END - ".__CLASS__.' -> '.__FUNCTION__);
        return $prepareOutput;
    }
}
