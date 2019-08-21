<?php

namespace MkOrm\Commands;

use MkOrm\Configs\DBConnect;
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
        $db = new DBConnect();

        $tableName = $input->getArgument('table');

        $q = $db->prepare("DESCRIBE `$tableName`");
        $q->execute();
        $tableFields = $q->fetchAll(PDO::FETCH_ASSOC);

        $className = Utils::camelize($tableName);
        $modelName = rtrim($className, 's');
        if (file_exists("src/Resources/{$modelName}Resource.php")) {
            $output->writeln("{$modelName}Resource.php file exists");
        } else {
            if (!is_dir('src/Resources')) {
                mkdir('src/Resources', 0755, true);
            }
            $myFile = fopen("src/Resources/{$modelName}Resource.php", "w") or die("Unable to open file!");
            fwrite($myFile, $this->resourceMaker($tableName, $tableFields));
            fclose($myFile);
        }

        if (file_exists("src/Resources/{$className}Resource.php")) {
            $output->writeln("{$className}Resource.php file exists");
            return;
        }else{
            if (!is_dir('src/Resources')) {
                mkdir('src/Resources', 0755, true);
            }
            $myFile = fopen("src/Resources/{$className}Resource.php", "w") or die("Unable to open file!");
            fwrite($myFile, $this->resourcesMaker($tableName));
            fclose($myFile);
        }


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

namespace App\Resources;

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

namespace App\Resources;

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
}
