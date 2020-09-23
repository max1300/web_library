<?php


namespace App\Entity;

//Interface préfixé par "I"

//Définir une interface

interface IItemOutputTransformable 
{
    //on va définir que toute classe qui va implémenter cette interface 
    //va devoir implémenter la méthode définit dans l'interface
    public function getLabel():string;

}