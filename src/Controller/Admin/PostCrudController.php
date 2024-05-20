<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FileField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostCrudController extends AbstractCrudController
{
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::EDIT);
    }
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('contenu')->setLabel('Content'),
            DateTimeField::new('published')->setLabel('Published At'),
            AssociationField::new('user')->setLabel('Author'),
            BooleanField::new('est_publie')->setLabel('public'),
            TextField::new('uploadFile', 'Upload File')
            ->setFormType(FileType::class)
            ->setFormTypeOption('mapped', false)
            ->onlyOnForms(),

            // IntegerField::new('commentsCount')
            //     ->setLabel('Number of Comments')
            //     ->formatValue(fn ($value, $entity) => $entity->getCommentsCount()),
            // IntegerField::new('likesCount')
            //     ->setLabel('Number of Likes')
            //     ->formatValue(fn ($value, $entity) => $entity->getLikesCount())
        ];
    }
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleFileUpload($entityInstance, $this->getContext()->getRequest()->files);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->handleFileUpload($entityInstance, $this->getContext()->getRequest()->files);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function handleFileUpload(Post $post, $files): void
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $files->get('Post')['uploadFile'];
        if ($uploadedFile instanceof UploadedFile) {
            $fileName = md5(uniqid()) . '.' . $uploadedFile->guessExtension();
            $symfonyUploadsDir = $this->getParameter('uploads_directory');
            $angularAssetsDir = $this->getParameter('angular_assets_directory');

            // Move file to Symfony uploads directory
            $uploadedFile->move($symfonyUploadsDir, $fileName);

            // Ensure the Angular assets directory exists
            if (!is_dir($angularAssetsDir)) {
                mkdir($angularAssetsDir, 0777, true);
            }

            // Copy file to Angular assets directory
            copy($symfonyUploadsDir . '/' . $fileName, $angularAssetsDir . '/' . $fileName);

            $post->setFile($fileName);
        }
    }
    
    
}
