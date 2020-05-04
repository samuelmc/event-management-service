<?php


namespace App\Repository;


interface CountableRepositoryInterface
{

    public function countMethod(): callable;

}