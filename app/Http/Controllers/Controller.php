<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Teehive API Documentation",
     *      description="L5 Swagger OpenApi description",
     * )
     *
     * @OA\Server(
     *      url=L5_SWAGGER_CONST_HOST,
     *      description="API Server"
     * )

     *
     * @OA\Tag(
     *     name="Auth",
     *     description="API Endpoints for Authentication"
     * )
     * @OA\Tag(
     *     name="Social Auth",
     *     description="API Endpoints for Social Authentication"
     * )
     * @OA\Tag(
     *     name="User",
     *     description="API Endpoints for User"
     * )
     * @OA\Tag(
     *     name="Credits",
     *     description="API Endpoints for Credits"
     * )
     * @OA\Tag(
     *     name="Course",
     *     description="API Endpoints for Course"
     * )
     *
     * @OAS\SecurityScheme(
     *      securityScheme="bearer",
     *      type="http",
     *      in="header",
     *      bearerFormat="JWT",
     *      scheme="bearer"
     * )
     */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
