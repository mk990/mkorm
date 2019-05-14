<?php

namespace MkOrm\Commands;

use MkOrm\Configs\Connection;
use MkOrm\Utils\Utils;
use PDO;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeController extends MakeModel
{
    protected function configure()
    {
        $this->setName('make:controller')
            ->setDescription('Make controller')
            ->setHelp('make controller.')
            ->addArgument('controller', InputArgument::REQUIRED, 'Pass the controller.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = (new Connection())->connect();

        $tableName = $input->getArgument('controller');

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_ASSOC);

        if (!is_dir('src/Controllers')) {
            mkdir('src/Controllers', 0755, true);
        }
        $className = Utils::camelize($tableName);
        $className = rtrim($className, 's');
        if (file_exists("src/Controllers/{$className}Controller.php")) {
            $output->writeln('file exists');
            return;
        }
        $myFile = fopen("src/Controllers/{$className}Controller.php", "w") or die("Unable to open file!");
        fwrite($myFile, $this->controllerMaker($tableName, $tableFields));
        fclose($myFile);
        $output->writeln('All done');
    }

    public function controllerMaker($tableName, $tableFields)
    {
        $className = Utils::camelize($tableName);
        $className = rtrim($className, 's');
        $date = date('Y-m-d H:i:s');
        $controller = "<?php
/**
 * Created by mk990/mkORM.
 * DateTime: $date
 */

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use OpenApi\Annotations as OA;

class {$className}Controller extends BaseController
{";
        $modelName = trim($className, 's');
        $routeName = $this->camelize($modelName);
        $getAll = "
    //======= GET ALL =========

    /**
     * @OA\GET(
     *   path=\"/$routeName\",
     *   tags={\"$modelName\"},
     *   summary=\"get all $className\",
     *   description=\"list of all $className\",
     *   operationId=\"getAll$className\",
     *   @OA\Parameter(
     *     name=\"page\",
     *     in=\"query\",
     *     required=false,
     *     example=1,
     *     @OA\Schema(
     *     type=\"string\"
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description=\"Success\",
     *     @OA\JsonContent(ref=\"#/components/schemas/$className\"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description=\"an \"\"unexpected\"\" error\",
     *     @OA\JsonContent(ref=\"#/components/schemas/ErrorModel\"),
     *   ),security={{\"api_key\": {}}}
     * )
     */

    public function getAll(Request \$request, Response \$response, array \$args)
    {
        // TODO: Implement getAll() method.
    }
";
        $getOne = "
    //======= GET ONE =========   

    /**
     * @OA\GET(
     *   path=\"/$routeName/{id}\",
     *   tags={\"$modelName\"},
     *   summary=\"get one $modelName\",
     *   description=\"one $modelName\",
     *   operationId=\"getOne$modelName\",
     *   @OA\Parameter(
     *     name=\"id\",
     *     in=\"path\",
     *     required=true,
     *     @OA\Schema(
     *     type=\"string\"
     *      )
     *   ),
     *   @OA\Response(
     *     response=\"200\",
     *     description=\"success\",
     *     @OA\JsonContent(ref=\"#/components/schemas/$className\"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description=\"an \"\"unexpected\"\" error\",
     *     @OA\JsonContent(ref=\"#/components/schemas/ErrorModel\"),
     *   ),security={{\"api_key\": {}}}
     * )
     */

    public function getOne(Request \$request, Response \$response, array \$args)
    {
        // TODO: Implement getOne() method.
    }
";
        $body = '';
        foreach ($tableFields as $tableField) {
            $property = $tableField['Field'];
            if (in_array($property, ['id', 'created_at', 'updated_at']))
                continue;
            $example = $this->exampleMaker($tableField['Field']);
            $type = $this->oaVarType($tableField['Type']);

            $body .= "
     *            @OA\Property(
     *              property=\"$property\",
     *              description=\"$property\",
     *              type=\"$type[0]\",
     *              format=\"$type[1]\",
     *              example=\"$example\",
     *            ),";
        }

        $post = "
    //======== CREATE ========

    /**
     * @OA\POST(
     *   path=\"/$routeName\",
     *   tags={\"$modelName\"},
     *   summary=\"create $modelName\",
     *   description=\"create $modelName\",
     *   operationId=\"Create$modelName\",
     *   @OA\Response(
     *     response=\"200\",
     *     description=\"Success\",
     *     @OA\JsonContent(ref=\"#/components/schemas/$className\"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description=\"an \"\"unexpected\"\" error\",
     *     @OA\JsonContent(ref=\"#/components/schemas/ErrorModel\"),
     *   ),
     *   @OA\RequestBody(
     *         description=\"tasks input\",
     *         required=true,
     *         @OA\JsonContent($body
     *      )
     *   ),security={{\"api_key\": {}}}
     * )
     */

    public function create(Request \$request, Response \$response, array \$args)
    {
        // TODO: Implement create() method.
    }
";

        $put = "
    //======== UPDATE ========

    /**
     * @OA\PUT(
     *   path=\"/$routeName/{id}\",
     *   tags={\"$modelName\"},
     *   summary=\"update $modelName\",
     *   description=\"update $modelName\",
     *   operationId=\"Update$modelName\",
     *   @OA\Response(
     *     response=\"200\",
     *     description=\"Success\",
     *     @OA\JsonContent(ref=\"#/components/schemas/$className\"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description=\"an \"\"unexpected\"\" error\",
     *     @OA\JsonContent(ref=\"#/components/schemas/ErrorModel\"),
     *   ),
     *   @OA\Parameter(
     *     name=\"id\",
     *     in=\"path\",
     *     required=true,
     *     @OA\Schema(
     *     type=\"string\"
     *      )
     *   ),
     *   @OA\RequestBody(
     *         description=\"tasks input\",
     *         required=true,
     *         @OA\JsonContent($body
     *      )
     *   ),security={{\"api_key\": {}}}
     * )
     */

    public function update(Request \$request, Response \$response, array \$args)
    {
        // TODO: Implement update() method.
    }

";

        $delete = "
    //======== DELETE ========

    /**
     * @OA\Delete(
     *   path=\"/$routeName/{id}\",
     *   tags={\"$modelName\"},
     *   summary=\"delete $modelName\",
     *   description=\"delete $modelName\",
     *   operationId=\"delete$modelName\",
     *   @OA\Parameter(
     *     name=\"id\",
     *     in=\"path\",
     *     required=true,
     *     @OA\Schema(
     *     type=\"string\"
     *      )
     *   ),
     *   @OA\Response(
     *     response=\"200\",
     *     description=\"Success Message\",
     *     @OA\JsonContent(ref=\"#/components/schemas/SuccessModel\"),
     *   ),
     *   @OA\Response(
     *     response=400,
     *     description=\"an \"\"unexpected\"\" error\",
     *     @OA\JsonContent(ref=\"#/components/schemas/ErrorModel\"),
     *   ),security={{\"api_key\": {}}}
     * )
     */

    public function delete(Request \$request, Response \$response, array \$args)
    {
        // TODO: Implement delete() method.
    }

";

        $controller .= "
$getAll
$getOne
$post
$put
$delete
}
";
        return $controller;
    }

}
