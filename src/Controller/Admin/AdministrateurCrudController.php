<?php

namespace App\Controller\Admin;

use App\Entity\Administrateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdministrateurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Administrateur::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        $type="admin";
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom')->setLabel('Nom'),
            TextField::new('prenom')->setLabel('Prénom'),
            EmailField::new('email')->setLabel('Email'),
            TelephoneField::new('numtel')->setLabel('Numéro de téléphone'),
            ChoiceField::new('role')
                ->setLabel('Role')
                ->setChoices([
                    'Agent' => 'agent',
                    'Administrateur' => 'administrateur'
                ]),
            TextField::new('password')->setLabel('Mot de passe')
        ];
    }
    
}
