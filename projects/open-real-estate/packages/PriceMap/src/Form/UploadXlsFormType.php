<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Form;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFormType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

final class UploadXlsFormType extends EasyAdminFormType
{
    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add('file', FileType::class, [
            'label' => 'XLS soubor s cenami',
        ]);

        $formBuilder->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'btn-success',
            ],
        ]);
    }
}
