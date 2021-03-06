<?php

namespace MkOrm\Commands;

use MkOrm\Configs\DBConnect;
use MkOrm\Utils\Utils;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeModel extends Command
{
    protected function configure()
    {
        $this->setName('make:model')
            ->setDescription('Make model from database')
            ->setHelp('make dataBaseModel.')
            ->addArgument('table', InputArgument::REQUIRED, 'Pass the Table.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = new DBConnect();

        $tableName = $input->getArgument('table');

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_ASSOC);

        $className = Utils::camelize($tableName);
        if (file_exists("src/Models/$className.php")) {
            $output->writeln('file exists');
            return;
        }

        if (!is_dir('src/Models')) {
            mkdir('src/Models', 0755, true);
        }

        $myFile = fopen("src/Models/$className.php", "w") or die("Unable to open file!");
        fwrite($myFile, $this->modelMaker($tableName, $tableFields));
        fclose($myFile);
        $output->writeln('All done');
    }

    public function modelMaker($tableName, $tableFields)
    {
        $className = Utils::camelize($tableName);
        $modelName = rtrim($className, 's');
        $getterSetter = "";
        $date = date('Y-m-d H:i:s');
        $model = "<?php
/**
 * Created by mk990/mkORM.
 * DateTime: $date
 */

namespace App\Models;

use MkOrm\Model\Model;
use OpenApi\Annotations as OA;

/**
 * Class $className
 *
 * @OA\Schema(
 *     description=\"$modelName model\",
 *     title=\"$modelName model\",
 * )
 */
class $className extends Model
{";
        $head = '';
        $body = '';
        foreach ($tableFields as $tableField) {
            $propertyName = $this->camelize($tableField['Field']);
            $methodName = ucfirst($this->camelize($tableField['Field']));
            $property = $tableField['Field'];
            $example = $this->exampleMaker($tableField['Field']);
            $type = $this->oaVarType($tableField['Type']);
            $phpType = $this->phpVarType($tableField['Type']);
            $uProperty = strtoupper($tableField['Field']);
            $head .= "
    const $uProperty = \"$property\";";

            $body .= "
    /**
     * @OA\Property(
     *     type=\"$type[0]\",
     *     format=\"$type[1]\",
     *     title=\"$property\",
     *     example=\"$example\",
     *     description=\"$property\",
     * )
     *
     * @var $type[0]
     */
    protected \$$propertyName;
";

            $getterSetter .= "
    /**
     * @return $phpType
     */
    public function get$methodName(): $phpType
    {
        return \$this->$propertyName;
    }

    /**
     * @param $phpType \$$propertyName
     */
    public function set$methodName($phpType \$$propertyName): void
    {
        \$this->$propertyName = \$$propertyName;
    }
";
        }

        $model .= "
$head
$body
$getterSetter
}
";
        return $model;
    }

    protected function camelize($word)
    {
        // split string by '-'
        $words = explode('_', $word);

        // make a strings first character uppercase
        $words = array_map('ucfirst', $words);

        // join array elements with '-'
        return lcfirst(str_replace('_', '', implode('_', $words)));
    }

    protected function oaVarType($mysqlType)
    {
        $mysqlType = trim($mysqlType, "()0123456789");;

        switch ($mysqlType) {
            case 'int':
                $type = ["integer", "int32"];
                break;
            case 'bigint':
                $type = ["integer", "int64"];
                break;
            case 'timestamp':
                $type = ["string", "date-time"];
                break;
            case 'tinyint':
                $type = ["boolean", ""];
                break;
            case 'float':
                $type = ["number", "float"];
                break;
            case 'double':
                $type = ["number", "double"];
                break;
            case 'decimal':
                $type = ["number", "double"];
                break;
            default:
                $type = ["string", ""];
        }
        return $type;
    }

    protected function phpVarType($mysqlType)
    {
        $mysqlType = trim($mysqlType, "()0123456789");;

        switch ($mysqlType) {
            case 'int':
                $type = 'int';
                break;
            case 'bigint':
                $type = 'int';
                break;
            case 'timestamp':
                $type = "string";
                break;
            case 'tinyint':
                $type = "bool";
                break;
            case 'float':
                $type = "float";
                break;
            case 'double':
                $type = "float";
                break;
            case 'decimal':
                $type = "float";
                break;
            default:
                $type = "string";
        }
        return $type;
    }

    protected function exampleMaker($input)
    {
        switch ($input) {
            case 'id':
                $value = '0';
                break;
            case 'username':
                $value = "user";
                break;
            case 'password':
                $value = "12345678";
                break;
            case 'email':
                $value = "example@example.com";
                break;
            case 'state':
                $value = '0';
                break;
            case 'stateus':
                $value = '0';
                break;
            case 'ip':
                $value = '127.0.0.1';
                break;
            case 'created_at':
                $value = date('Y-m-d H:i:s');
                break;
            case 'updated_at':
                $value = date('Y-m-d H:i:s');
                break;
            default:
                $value = "string";
        }
        return $value;
    }
}
