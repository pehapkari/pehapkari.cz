<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class UploadXlsFormFactory
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function create(): FormInterface
    {
        $formBuilder = $this->formFactory->createBuilder();

        $formBuilder->add('file', FileType::class, [
            'label' => 'XLS soubor s cenami',
        ]);

        $formBuilder->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'btn-success',
            ],
        ]);

        return $formBuilder->getForm();
    }
}
