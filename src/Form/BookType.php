<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // on utilise le builder de form pour créer les inputs
        // de notre formulaire, chaque input correspondant
        // généralement à une propriété d'entité et donc une colonne de la table
        $builder
            ->add('title', null, [
                'label' => 'titre',
                'required' => false
            ])
            ->add('nbPages')
            // j'ajoute le champs genre et je lui mets
            // 'EntityType' en type de champs pour que symfony
            // créé une liste déroulante avec tous les genres existants en bdd
            // pour chaque genre, j'utilise sa propriété (donc sa colonne) 'name' pour afficher le genre
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name'
            ])
            ->add('resume')

            // je créé l'input File, avec en option "mapped => false" pour
            // que symfony n'enregistre pas automatiquement la valeur du champs
            // (comme il le fait sur les autres champs)
            // quand le formulaire est envoyé
            ->add('bookCover', FileType::class, [
                'mapped' => false
            ])

            // je rajoute manuellement un input submit
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
