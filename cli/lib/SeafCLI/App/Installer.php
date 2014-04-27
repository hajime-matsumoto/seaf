<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace SeafCLI\App;

use Seaf\CLI;

class Installer extends CLI\CLIController
{
    public function setupController ( )
    {
        parent::setupController();
        $this->setupByAnnotation();
    }

    /**
     * @SeafRoute /twig
     */
    public function installTwig ( )
    {
        if (!$this->isSuperUser()) {
            $this->stdout('This Operation Neads Root Privirages'."\n");
        }

        $this->shellOut('pear channel-discover pear.twig-project.org');
        $this->shellOut('pear install twig/Twig', $output, $return_val);

        if ($return_val > 0){
            $this->outln("!!! Stop Installing");
            $this->outlines($output, " > ");
            die();
        }
    }


    /**
     * @SeafRoute /ctwig
     */
    public function installCTwig ( )
    {
        if (!$this->isSuperUser()) {
            $this->stdout('This Operation Neads Root Privirages'."\n");
        }

        $this->shellOut('pear channel-discover pear.twig-project.org');
        $this->shellOut('pear install twig/CTwig', $output, $return_val);

        if ($return_val > 0){
            $this->outln("!!! Stop Installing");
            $this->outlines($output, " > ");
            die();
        }

        $so = false;
        foreach ($output as $line) {
            if (preg_match('%Installing \'(/[^\s]*/twig.so)\'%', $line, $m)) {
                $so = $m[1];
            }
        }

        $this->outln('so found('.$so.')');

        if ($so == false) {
            $this->emerg('Install Failed');
            return;
        }

        $iniFileName = '/etc/php5/mods-available/twig.ini';
        $iniFileData = "; configuration for php Twig module\n";
        $iniFileData.= "; priority=20\n";
        $iniFileData.= "extension=".$so."\n";

        $this->outln("Writing File $iniFileName");
        $this->outln("< ".$iniFileData);

        file_put_contents($iniFileName, $iniFileData);

        $this->shellOut('php5enmod twig');
    }
}
