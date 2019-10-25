<?php

declare(strict_types=1);

namespace Pehapkari\Registration\Form;

use Pehapkari\Registration\Entity\TrainingRegistration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see TrainingRegistration
 */
final class RegistrationFormType extends AbstractType
{
    /**
     * @param mixed[] $options
     */
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder->add('name', TextType::class, [
            'label' => 'Tvé jméno',
            'required' => true,
        ]);

        $formBuilder->add('email', TextType::class, [
            'label' => 'Tvůj email',
            'required' => true,
        ]);

        $formBuilder->add('phone', TextType::class, [
            'label' => 'Tvé telefonní číslo',
            'required' => true,
            'help' => 'Abychom tě mohli rychle informovat v případě změn nebo kdybychom potřebovali podrobnosti k tvé registraci',
        ]);

        $formBuilder->add('ico', TextType::class, [
            'label' => 'IČO pro fakturaci nebo Tvá adresa',
            'required' => true,
        ]);

        $formBuilder->add('participantCount', IntegerType::class, [
            'label' => 'Kolik vás bude?',
            'required' => true,
            'data' => 1, // default value
            'help' => 'Kolik lidí máme fakturovat na toto IČO. Jestli vás bude víc než ty, napiš prosím jména do poznámky ↓, ať i ostatní mají pěkné certifikáty.',
        ]);

        $formBuilder->add('note', TextareaType::class, [
            'required' => false,
            'label' => 'Poznámka',
        ]);

        $formBuilder->add(
            'agrees_with_personal_data',
            CheckboxType::class,
            [
                'label' => 'Zaškrtnutím souhlasíš se zpracováním osobních údajů firmou Edukai s.r.o., která pořádá školení. Firma údaje uchovává po dobu 2 let pouze pro účely organizace kurzu a zpracování zpětné vazby. Máš právo kdykoliv požádat o úpravu či smazání údajů z naší databáze. Nikdo jiný než organizátor a školitel k tvým údajům nemá přístup.',
                'required' => true,
            ]
        );

        $formBuilder->add('register', SubmitType::class, [
            'label' => 'Přihlásit se na školení',
            'attr' => [
                'class' => 'btn btn-success mt-5 d-block',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults([
            'data_class' => TrainingRegistration::class,
        ]);
    }
}
