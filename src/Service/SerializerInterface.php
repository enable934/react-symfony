<?php


namespace App\Service;


interface SerializerInterface
{
    public function serialize(array $data, array $context):string;
}