<?php declare(strict_types=1);

namespace OpenRealEstate\PriceMap\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class EstimatePriceFormFactory
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

        $formBuilder->add('type', ChoiceType::class, [
            'choices' => [
                'Byt' => 'flat',
                'Dům' => 'house',
                'Pozemek' => 'land',
            ],
            'label' => 'Typ nemovitost',
            'expanded' => true,
        ]);

        $formBuilder->add('zip', TextType::class, [
            'label' => 'PSČ',
        ]);

        $formBuilder->add('area', IntegerType::class, [
            'label' => 'Plocha v m²',
        ]);

        $formBuilder->add('reconstruction', ChoiceType::class, [
            'choices' => [
                'Před rekonstrukcí' => 'before_reconstruction',
                'Po rekonstrukci' => 'after_reconstruction',
            ],
            'label' => 'Rekonstrukce',
            'expanded' => true,
        ]);

        $formBuilder->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'btn-success',
            ],
            'label' => 'Ocenit nemovitost',
        ]);

        return $formBuilder->getForm();
    }
}
