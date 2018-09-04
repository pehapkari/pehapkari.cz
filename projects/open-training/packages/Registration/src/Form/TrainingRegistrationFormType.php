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
        $formBuilder->add('name', TextType::class);
        $formBuilder->add('email', TextType::class);
        $formBuilder->add('ico', TextType::class, [
            'required' => false,
        ]);
        $formBuilder->add('note', TextareaType::class, [
            'required' => false,
        ]);

        $formBuilder->add('register', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => TrainingRegistration::class,
        ]);
    }
}
