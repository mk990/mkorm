<?php

namespace MkOrm\Commands;

use MkOrm\Configs\Connection;
use MkOrm\Utils\Utils;
use PDO;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeAllResource extends MakeResource
{
    protected function configure()
    {
        $this->setName('make:allResource')
            ->setDescription('Make all Resource from database')
            ->setHelp('make all dataBase Resource.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = (new Connection())->connect();

        $q = $db->prepare("SHOW TABLES;");
        $q->execute();
        $tables = $q->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tables as $table) {
            $tableName = $table['Tables_in_' . getenv('DB_DATABASE')];
            if ($tableName == 'migrations') {
                continue;
            }

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
            } else {
                $myFile = fopen("src/Resource/{$className}Resource.php", "w") or die("Unable to open file!");
                fwrite($myFile, $this->resourcesMaker($tableName, $tableFields));
                fclose($myFile);
            }
        }

        $output->writeln('All done');
    }
}
