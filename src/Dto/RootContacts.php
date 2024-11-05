<?php

namespace App\Dto;

class RootContacts
{
    /** @var Contacts[] */
    public array $contacts;
    public int $count;

    /**
     * @param Contacts[] $contacts
     */
    public function __construct(array $contacts, int $count)
    {
        $this->contacts = $contacts;
        $this->count = $count;
    }
}

class Contacts
{
    public string $email;
    public int $id;
    public bool $emailBlacklisted;
    public bool $smsBlacklisted;
    public string $createdAt;
    public string $modifiedAt;
    /** @var int[] */
    public array $listIds;
    public Attributes $attributes;

    /**
     * @param int[] $listIds
     */
    public function __construct(
        string     $email,
        int        $id,
        bool       $emailBlacklisted,
        bool       $smsBlacklisted,
        string     $createdAt,
        string     $modifiedAt,
        array      $listIds,
        Attributes $attributes
    )
    {
        $this->email = $email;
        $this->id = $id;
        $this->emailBlacklisted = $emailBlacklisted;
        $this->smsBlacklisted = $smsBlacklisted;
        $this->createdAt = $createdAt;
        $this->modifiedAt = $modifiedAt;
        $this->listIds = $listIds;
        $this->attributes = $attributes;
    }
}

class Attributes
{
    public ?string $NEWSTYPE = null;

    public function __construct(?string $newstype)
    {
        $this->NEWSTYPE = $newstype;
    }


}