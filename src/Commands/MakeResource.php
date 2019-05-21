<?php

namespace MkOrm\Commands;

use MkOrm\Configs\Connection;
use MkOrm\Utils\Utils;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeResource extends Command
{
    protected function configure()
    {
        $this->setName('make:resource')
            ->setDescription('Make resource from database')
            ->setHelp('make dataBaseResource.')
            ->addArgument('table', InputArgument::REQUIRED, 'Pass the Table.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = (new Connection())->connect();

        $tableName = $input->getArgument('table');

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_ASSOC);

        $className = Utils::camelize($tableName);
        $modelName = rtrim($className, 's');
        if (file_exists("src/Resource/{$modelName}Resource.php")) {
            $output->writeln("{$modelName}Resource.php file exists");
        } else {
            $myFile = fopen("src/Resource/{$modelName}Resource.php", "w") or die("Unable to open file!");
            fwrite($myFile, $this->resourceMaker($tableName, $tableFields));
            fclose($myFile);
        }

        if (file_exists("src/Resource/{$className}Resource.php")) {
            $output->writeln("{$className}Resource.php file exists");
            return;
        }
        $myFile = fopen("src/Resource/{$className}Resource.php", "w") or die("Unable to open file!");
        fwrite($myFile, $this->resourcesMaker($tableName));
        fclose($myFile);

        $output->writeln('All done');
    }

    public function resourceMaker($tableName, $tableFields)
    {
        $className = Utils::camelize($tableName);
        $modelName = rtrim($className, 's');
        $lcfModelName = lcfirst(rtrim($className, 's'));

        $date = date('Y-m-d H:i:s');
        $model = "<?php
/**
 * Created by mk990/mkORM.
 * DateTime: $date
 */

namespace App\Resource;

use App\Models\\$className;
use MkOrm\Resource\Resource;

class {$modelName}Resource extends Resource
{
    public \$data;

    public function __construct({$className} \$$lcfModelName)
    {
        \$name = \$this->getName();
        \$link = 'url' . \"/\$name/\" . \${$lcfModelName}->getId();
        \$this->data = [
            \"type\"          => \"\$name\",
            \"id\"            => \${$lcfModelName}->getId(),
            \"attributes\"    => [";
        $body = '';
        foreach ($tableFields as $tableField) {
            if ($tableField['Field'] == 'id')
                continue;
            $key = "$className::" . strtoupper($tableField['Field']);
            $value = "\${$lcfModelName}->get" . Utils::camelize($tableField['Field']) . '()';
            $body .= "
                $key => $value,";
        }

        $model .= "$body
        ],
            \"relationships\" => [],
            \"links\"         => [
                \"self\" => \$link
            ]
        ];
    }
}
";
        return $model;
    }

    public function resourcesMaker($tableName)
    {
        $className = Utils::camelize($tableName);
        $modelName = rtrim($className, 's');

        $date = date('Y-m-d H:i:s');
        $model = "<?php
/**
 * Created by mk990/mkORM.
 * DateTime: $date
 */

namespace App\Resource;

use App\Models\\$className;
use MkOrm\Resource\Resource;

class {$className}Resource extends Resource
{
    public \$data;
    public \$links;
    public \$meta;

    public function __construct(\$results)
    {
        if (!empty(\$results['data'])) {
            \$this->links(\$results['info'], '/');
            \$this->meta(\$results['info'], '/');
            \$results = \$results['data'];
        }

        foreach (\$results as \$result) {
            if (\$result instanceof $className) {
                \$object = new {$modelName}Resource(\$result);
                \$this->data[] = \$object->data;
            }
        }
    }
}
";
        return $model;
    }

    protected function deCamelize($word)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $word));
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
