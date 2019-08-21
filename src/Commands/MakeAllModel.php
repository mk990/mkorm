<?php

namespace MkOrm\Commands;

use MkOrm\Configs\DBConnect;
use MkOrm\Utils\Utils;
use PDO;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeAllModel extends MakeModel
{
    protected function configure()
    {
        $this->setName('make:allModels')
            ->setDescription('Make all model from database')
            ->setHelp('make all dataBase Model.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = new DBConnect();

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
            if (file_exists("src/Models/$className.php"))
                continue;
            if (!is_dir('src/Models')) {
                mkdir('src/Models', 0755, true);
            }
            $myFile = fopen("src/Models/$className.php", "w") or die("Unable to open file!");
            fwrite($myFile, $this->modelMaker($tableName, $tableFields));
            fclose($myFile);
        }


        $output->writeln('All done');
    }
}
