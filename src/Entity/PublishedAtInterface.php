<?php


namespace App\Entity;


interface PublishedAtInterface
{
    public function setCreatedAt(\DateTimeInterface $createdAt): PublishedAtInterface;

}