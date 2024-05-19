<?php

namespace App\Controller\Admin;

use App\Entity\Classe;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ClasseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Classe::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom')->setLabel('Nom'),
            AssociationField::new('enseignants')
            ->setLabel('Enseignants')
            ->setFormTypeOptions([
                'by_reference' => false,
            ]),
            ArrayField::new('etudiants', 'Etudiants')
                ->formatValue(function ($value, $entity) {
                    return implode(', ', $entity->getEtudiants()->toArray());
                }),
        ];
    }
    
}
