<?php


namespace App\Entity;

//Interface préfixé par "I"

//Définir une interface

interface IItemOutputTransformable 
{
    //on va définir que toute classe qui vont implementer cette interface 
    //va devoir implementer la methode definit dans l interface
    public function getLabel():string;

}