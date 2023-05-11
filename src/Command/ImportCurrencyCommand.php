<?php

declare(strict_types = 1);

namespace App\Command;

use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCurrencyCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct('currency:import');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $scopeEntity = new Currency('Euro', 'EUR', '€');
        $this->entityManager->persist($scopeEntity);

        $scopeEntity = new Currency('USD', 'USD', '$');
        $this->entityManager->persist($scopeEntity);

        $scopeEntity = new Currency('Česká koruna', 'CZK', 'Kč');
        $this->entityManager->persist($scopeEntity);

        $this->entityManager->flush();

        return 0;
    }

}
