<?php

declare(strict_types = 1);

namespace App\EshopBundle\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class Address extends DataTransferObject
{
    public function __construct(
        private string $firstName,
        private string $surname,
        private string $street,
        private string $city,
        private int $zip,
        private string $country,
        private ?string $note
    )
    {
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return int
     */
    public function getZip(): int
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }
}
