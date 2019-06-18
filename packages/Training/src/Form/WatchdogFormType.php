<?php declare(strict_types=1);

namespace Pehapkari\Training\Form;

use Pehapkari\Training\Entity\Training;
use Pehapkari\Training\Entity\Watchdog;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WatchdogFormType extends AbstractType
{
    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add('training', EntityType::class, [
            'label' => 'Vyber školení',
            'class' => Training::class,
        ]);

        $formBuilder->add('email', TextType::class, [
            'label' => 'Tvůj email',
            'required' => true,
        ]);

        $formBuilder->add('submit', SubmitType::class, [
            'label' => 'Dejte mi vědět, až bude',
            'attr' => [
                'class' => 'btn btn-success',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => Watchdog::class,
        ]);
    }
}
