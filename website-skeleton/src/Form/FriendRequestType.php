<?php

namespace App\Form;

use App\Entity\FriendShip;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FriendRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('friend', ChoiceType::class, [
                'choices' => $options['users'], // Przekazujemy listę użytkowników do wyboru
                'choice_label' => function(?User $user) {
                    return $user ? $user->getFirstname() : ''; // Wyświetlamy nazwę użytkownika jako etykietę
                },
                'label' => 'Choose friend', // Etykieta pola wyboru
                // Możesz dodać więcej opcji pola wyboru, w zależności od potrzeb
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FriendShip::class,
            'users' => []
        ]);
    }
}
