<?php

namespace MkOrm\Commands;

use MkOrm\Configs\Connection;
use MkOrm\Utils\Utils;
use PDO;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeAllController extends MakeController
{
    protected function configure()
    {
        $this->setName('make:allControllers')
            ->setDescription('Make all controllers')
            ->setHelp('make controllers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = (new Connection())->connect();

        $q = $db->prepare("SHOW TABLES;");
        $q->execute();
        $tables = $q->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tables as $table) {
            $tableName = $table['Tables_in_'.getenv('DB_DATABASE')];
            if ($table == 'migrations') {
                continue;
            }

            $q = $db->prepare("DESCRIBE `$tableName`");
            $q->execute();
            $tableFields = $q->fetchAll(PDO::FETCH_ASSOC);

            $className = Utils::camelize($tableName);
            $className = rtrim($className,'s');
            if(file_exists("src/Controllers/{$className}Controller.php"))
                continue;
            $myFile = fopen("src/Controllers/{$className}Controller.php", "w") or die("Unable to open file!");
            fwrite($myFile, $this->controllerMaker($tableName, $tableFields));
            fclose($myFile);
        }


        $output->writeln('All done');
    }
}
