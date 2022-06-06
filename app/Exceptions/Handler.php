<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Google\Cloud\Core\Exception\BadRequestException;
use Google\Cloud\Core\Exception\NotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [];

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
     * @param \Exception $exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // if ($request->ajax() || $request->wantsJson()) {
        //     $json = [
        //         'success' => false,
        //         'error' => [
        //             'code' => $exception->getCode(),
        //             'message' => $exception->getMessage(),
        //         ],
        //     ];
        //     return response()->json($json, 400);
        // }
        if ($exception instanceof  BadRequestException) {
            return response(['status' => 4, 'message' => 'Invalid request to firestore'], Response::HTTP_OK);
        }
        // if ($exception instanceof  NotFoundException) {
        //     return response(['status' => 4, 'message' => 'Document not found in firestore'], Response::HTTP_OK);
        // }
        if ($exception instanceof TokenBlacklistedException) {
            return response(['status' => 3, 'message' => 'Token can not be used, get new one'], Response::HTTP_OK);
        }
        if ($exception instanceof TokenInvalidException) {
            return response(['status' => 3, 'message' => 'Token is invalid'], Response::HTTP_OK);
        }
        if ($exception instanceof TokenExpiredException) {
            return response(['status' => 3, 'message' => 'Token is expired'], Response::HTTP_OK);
        }
        if ($exception instanceof JWTException) {
            return response(['status' => 3, 'message' => 'Token is not provided'], Response::HTTP_OK);
        }
        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }
        if ($exception instanceof AuthorizationException) {
            return response()->json(['status' => 2, 'message' => $exception->getMessage()], Response::HTTP_OK);
        }
        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));

            if ($modelName == 'doortodoor' || $modelName == 'busdropoff') {
                $message = 'Our service is not available for this destination.';
            } elseif ($modelName == 'producttype') {
                $message = "Cannot find product type";
            } elseif ($modelName == 'producttag') {
                $message = "Cannot find product tag";
            } elseif ($modelName == 'variationmeta') {
                $message = "Cannot find variation meta";
            } elseif ($modelName == 'productvariation') {
                $message = "Cannot find product variation";
            } elseif ($modelName == 'productreview') {
                $message = "Cannot find product review";
            } elseif ($modelName == 'productdiscount') {
                $message = "Cannot find product discount";
            } else {
                $modelName = class_basename($exception->getModel());
                $message = "Cannot find {$modelName}";
            }
            return response()->json([
                'status' => 2,
                'message' => $message,
            ], Response::HTTP_OK);
        }
        if ($exception instanceof NotFoundHttpException) {
            if (str_contains(request()->url(), 'api')) {
                return response()->json(['status' => 2, 'message' => 'Incorect route'], Response::HTTP_OK);
            }
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json(['status' => 2, 'message' => 'The specified method for the request is invalid'], Response::HTTP_OK);
        }
        // if ($exception instanceof Exception) {
        //     return response()->json(['status' => 2, 'message' => 'Internal Server Error'], Response::HTTP_OK);
        // }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->isFrontend($request)) {
            return redirect()->guest('login');
        }
        return response()->json(['status' => 2, 'message' => 'Unauthenticated'], Response::HTTP_OK);
    }

    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())
            ->contains('web');
    }
}
