<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', TextType::class)
            ->add('email', EmailType::class)
            ->add('plainPassword', TextType::class,[
                'constraints' => [
                            new NotBlank([
                                'message' => 'Please enter a password',
                            ]),
                            new Regex([
                                'pattern' => "/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/",
                                'message' => "Password must be at least seven character long and containe at least one digit or one special character, one upper case letter and one lower case letter"
                            ]),
                        ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            //on a desactivé la protection CSRF qui pose des problèmes de validation
            'csrf_protection' => false,
        ));
    }
}
