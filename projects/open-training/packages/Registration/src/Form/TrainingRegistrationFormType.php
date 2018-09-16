<?php declare(strict_types=1);

namespace OpenTraining\Registration\Form;

use OpenTraining\Registration\Entity\TrainingRegistration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TrainingRegistrationFormType extends AbstractType
{
    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add('name', TextType::class, [
            'label' => 'Tvé jméno',
        ]);
        $formBuilder->add('email', TextType::class, [
            'label' => 'Tvůj email',
        ]);
        $formBuilder->add('phone', TextType::class, [
            'label' => 'Tvé telefonní číslo',
        ]);
        $formBuilder->add('ico', TextType::class, [
            'label' => 'IČO pro fakturaci nebo Tvá adresa',
        ]);
        $formBuilder->add('note', TextareaType::class, [
            'required' => false,
            'label' => 'Poznámka',
        ]);

        $formBuilder->add('register', SubmitType::class, [
            'label' => 'Odeslat přihlášku',
        ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => TrainingRegistration::class,
        ]);
    }
}
