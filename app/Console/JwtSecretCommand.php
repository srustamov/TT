<?php namespace App\Console;

//use Symfony\Component\Console\Input\ArrayInput;
//use Symfony\Component\Console\Input\InputArgument;
//use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TT\Engine\Cli\Helper;
use TT\Facades\Config;
use TT\Facades\Str;

class JwtSecretCommand extends Command
{
    protected static $defaultName = 'jwt:secret';

    protected function configure()
    {
        $this
            ->setDescription('Jwt security key generate')
            ->setHelp('php manage jwt:secret');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = Str::random(60);

        try {
            Helper::envFileChangeFragment('JWT_SECRET', $key);
            Config::set('jwt.key', $key);
            $output->writeln(
                '<fg=green>key:'.$key.'</>'
            );
        } catch (\Exception $e) {
            $output->writeln(
                '<fg=red>'.$e->getMessage().'</>'
            );
        }
    }
}
