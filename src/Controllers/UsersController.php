<?php
/**
 * Created by mk990/mkORM.
 * DateTime: 2019-02-07 20:39:46
 */

namespace App\controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use OpenApi\Annotations as OA;

class UsersController extends BaseController
{

    //======= GET ALL =========

    /**
     * @OA\GET(
     *   path="/user",
     *   tags={"User"},
     *   summary="get all Users",
     *   description="list of all Users",
     *   operationId="getAllUsers",
     *   @OA\Parameter(
     *     name="page",
     *     in="query",
     *     required=false,
     *     example=1,
     *     @OA\Schema(
     *     type="string"
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="an ""unexpected"" error",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *   ),security={{"api_key": {}}}
     * )
     */

    public function getAll(Request $request, Response $response, array $args)
    {
        // TODO: Implement getAll() method.
    }


    //======= GET ONE =========   

    /**
     * @OA\GET(
     *   path="/user/{id}",
     *   tags={"User"},
     *   summary="get one User",
     *   description="one User",
     *   operationId="getOneUser",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *     type="string"
     *      )
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="success",
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="an ""unexpected"" error",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *   ),security={{"api_key": {}}}
     * )
     */

    public function getOne(Request $request, Response $response, array $args)
    {
        // TODO: Implement getOne() method.
    }


    //======== CREATE ========

    /**
     * @OA\POST(
     *   path="/user",
     *   tags={"User"},
     *   summary="create User",
     *   description="create User",
     *   operationId="CreateUser",
     *   @OA\Response(
     *     response="200",
     *     description="Success",
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="an ""unexpected"" error",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *   ),
     *   @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *            @OA\Property(
     *              property="name",
     *              description="name",
     *              type="string",
     *              format="",
     *              example="string",
     *            ),
     *            @OA\Property(
     *              property="password",
     *              description="password",
     *              type="string",
     *              format="",
     *              example="12345678",
     *            ),
     *      )
     *   ),security={{"api_key": {}}}
     * )
     */

    public function create(Request $request, Response $response, array $args)
    {
        // TODO: Implement create() method.
    }


    //======== UPDATE ========

    /**
     * @OA\PUT(
     *   path="/user/{id}",
     *   tags={"User"},
     *   summary="update User",
     *   description="update User",
     *   operationId="UpdateUser",
     *   @OA\Response(
     *     response="200",
     *     description="Success",
     *     @OA\JsonContent(ref="#/components/schemas/User"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="an ""unexpected"" error",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *   ),
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *     type="string"
     *      )
     *   ),
     *   @OA\RequestBody(
     *         description="tasks input",
     *         required=true,
     *         @OA\JsonContent(
     *            @OA\Property(
     *              property="name",
     *              description="name",
     *              type="string",
     *              format="",
     *              example="string",
     *            ),
     *            @OA\Property(
     *              property="password",
     *              description="password",
     *              type="string",
     *              format="",
     *              example="12345678",
     *            ),
     *      )
     *   ),security={{"api_key": {}}}
     * )
     */

    public function update(Request $request, Response $response, array $args)
    {
        // TODO: Implement update() method.
    }



    //======== DELETE ========

    /**
     * @OA\Delete(
     *   path="/user/{id}",
     *   tags={"User"},
     *   summary="delete User",
     *   description="delete User",
     *   operationId="deleteUser",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema(
     *     type="string"
     *      )
     *   ),
     *   @OA\Response(
     *     response="200",
     *     description="Success Message",
     *     @OA\JsonContent(ref="#/components/schemas/SuccessModel"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description="an ""unexpected"" error",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorModel"),
     *   ),security={{"api_key": {}}}
     * )
     */

    public function delete(Request $request, Response $response, array $args)
    {
        // TODO: Implement delete() method.
    }


}
